<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers\InstallmentsRelationManager;
use App\Models\Loan;
use App\Models\LoanRate;
use App\Services\CashTransactionService;
use App\Services\LoanScheduleService;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Koperasi';

    protected static ?string $navigationLabel = 'Pinjaman';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pengajuan Pinjaman')
                    ->schema([
                        Select::make('member_id')
                            ->label('Anggota')
                            ->relationship('member', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('principal')
                            ->label('Pokok Pinjaman')
                            ->numeric()
                            ->required(),
                        Select::make('interest_type')
                            ->label('Jenis Bunga')
                            ->options(LoanRate::types())
                            ->required()
                            ->reactive(),
                        Select::make('interest_rate')
                            ->label('Bunga (%)')
                            ->options(function (callable $get) {
                                $type = $get('interest_type');

                                if (! $type) {
                                    return [];
                                }

                                return LoanRate::query()
                                    ->where('type', $type)
                                    ->where('is_active', true)
                                    ->orderBy('rate')
                                    ->pluck('rate', 'rate')
                                    ->mapWithKeys(fn ($rate, $key) => [(string) $key => (string) $rate])
                                    ->all();
                            })
                            ->required(),
                        TextInput::make('admin_fee')
                            ->label('Biaya Admin')
                            ->numeric()
                            ->default(0)
                            ->visible(fn () => Auth::user()?->isAdmin() ?? false),
                        TextInput::make('term_count')
                            ->label('Jumlah Angsuran')
                            ->numeric()
                            ->required(),
                        DatePicker::make('first_due_date')
                            ->label('Jatuh Tempo Pertama')
                            ->required(),
                        TextInput::make('status')
                            ->label('Status')
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (?Loan $record) => filled($record)),

                        Placeholder::make('outstanding_amount')
                            ->label('Sisa Belum Terbayar')
                            ->content(function (?Loan $record): string {
                                if (! $record) {
                                    return '-';
                                }

                                $outstanding = $record->getOutstandingAmount();

                                return 'Rp ' . number_format($outstanding, 0, ',', '.');
                            })
                            ->visible(fn (?Loan $record) => filled($record)),

                        Placeholder::make('unpaid_installments')
                            ->label('Angsuran Belum Lunas')
                            ->content(function (?Loan $record): string {
                                if (! $record) {
                                    return '-';
                                }

                                $unpaidCount = $record->installments()
                                    ->whereColumn('paid_amount', '<', 'total_due')
                                    ->count();

                                $total = (int) $record->term_count;

                                return $total > 0
                                    ? ($unpaidCount . ' dari ' . $total)
                                    : (string) $unpaidCount;
                            })
                            ->visible(fn (?Loan $record) => filled($record)),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loan_code')->label('Kode')->searchable()->sortable(),
                TextColumn::make('member.name')->label('Anggota')->searchable()->sortable(),
                TextColumn::make('principal')->label('Pokok')->money('IDR')->sortable(),
                TextColumn::make('total_payable')->label('Total')->money('IDR')->sortable(),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => Loan::STATUS_PENDING,
                        'danger' => Loan::STATUS_REJECTED,
                        'success' => Loan::STATUS_PAID,
                        'primary' => Loan::STATUS_ACTIVE,
                        'danger' => Loan::STATUS_OVERDUE,
                    ])
                    ->formatStateUsing(fn (string $state) => Loan::statuses()[$state] ?? $state),
                TextColumn::make('applied_at')->label('Diajukan')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->button()
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Loan $record): bool => (Auth::user()?->isAdmin() ?? false) && $record->status === Loan::STATUS_PENDING)
                    ->action(function (Loan $record, LoanScheduleService $scheduleService, CashTransactionService $cashService): void {
                        $now = now();

                        $record->approved_at = $now;
                        $record->approved_by = Auth::id();
                        $record->status = Loan::STATUS_ACTIVE;

                        if (! $record->disbursed_at) {
                            $record->disbursed_at = $now;
                            $record->disbursed_by = Auth::id();
                        }

                        $record->save();

                        if (! $record->installments()->exists()) {
                            $scheduleService->generateInstallments($record);
                        }

                        if ($record->disbursed_at && $record->disbursed_by) {
                            $memberName = $record->member()->value('name');
                            $loanLabel = $record->loan_code ?: ('#' . $record->id);
                            $description = $memberName
                                ? "Pencairan pinjaman {$loanLabel} ({$memberName})"
                                : "Pencairan pinjaman {$loanLabel}";

                            $cashService->recordExpense(
                                categoryName: 'Pencairan Pinjaman',
                                amount: (float) $record->principal,
                                occurredAt: $record->disbursed_at,
                                description: $description,
                                referenceType: Loan::class,
                                referenceId: $record->id,
                                createdBy: $record->disbursed_by,
                            );
                        }

                        Notification::make()->success()->title('Pinjaman disetujui & dicairkan')->send();
                    }),
                Action::make('reject')
                    ->label('Tolak')
                    ->button()
                    ->color('danger')
                    ->visible(fn (Loan $record): bool => (Auth::user()?->isAdmin() ?? false) && $record->status === Loan::STATUS_PENDING)
                    ->form([
                        Textarea::make('rejection_note')
                            ->label('Catatan Ditolak')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Loan $record, array $data): void {
                        $record->rejected_at = now();
                        $record->rejected_by = Auth::id();
                        $record->rejection_note = $data['rejection_note'] ?? null;
                        $record->status = Loan::STATUS_REJECTED;
                        $record->save();

                        Notification::make()->success()->title('Pinjaman ditolak')->send();
                    }),
            ])
            ->recordUrl(fn (Loan $record): string => Pages\ViewLoan::getUrl(['record' => $record]))
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            InstallmentsRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Loan::query()->where('status', Loan::STATUS_OVERDUE)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string | array | null
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user?->isAdmin() || $user?->isKasir() || false;
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'view' => Pages\ViewLoan::route('/{record}'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}
