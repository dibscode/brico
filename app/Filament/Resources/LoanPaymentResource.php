<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanPaymentResource\Pages;
use App\Models\Loan;
use App\Models\LoanPayment;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LoanPaymentResource extends Resource
{
    protected static ?string $model = LoanPayment::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string | \UnitEnum | null $navigationGroup = 'Koperasi';

    protected static ?string $navigationLabel = 'Pembayaran Angsuran';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pembayaran')
                    ->schema([
                        Select::make('loan_id')
                            ->label('Pinjaman')
                            ->options(function () {
                                return Loan::query()
                                    ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])
                                    ->with('member')
                                    ->orderByDesc('created_at')
                                    ->get()
                                    ->mapWithKeys(fn (Loan $loan) => [
                                        $loan->id => $loan->loan_code . ' — ' . ($loan->member?->name ?? '-'),
                                    ])
                                    ->all();
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if (! $state) {
                                    $set('member_id', null);

                                    return;
                                }

                                $loan = Loan::query()->find($state);
                                $set('member_id', $loan?->member_id);
                            }),
                        Hidden::make('member_id')->required(),
                        TextInput::make('amount')
                            ->label('Nominal')
                            ->numeric()
                            ->required(),
                        DateTimePicker::make('paid_at')
                            ->label('Tanggal Bayar')
                            ->seconds(false)
                            ->default(now())
                            ->required(),
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('paid_at')->label('Tanggal')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('loan.loan_code')->label('Kode Pinjaman')->searchable()->sortable(),
                TextColumn::make('member.name')->label('Anggota')->searchable()->sortable(),
                TextColumn::make('amount')->label('Nominal')->money('IDR')->sortable(),
                TextColumn::make('interest_component')->label('Bunga')->money('IDR')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('admin_fee_component')->label('Admin')->money('IDR')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('receiver.name')->label('Diterima oleh')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('paid_at', 'desc');
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();

        return $user?->isAdmin() || $user?->isKasir() || false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoanPayments::route('/'),
            'create' => Pages\CreateLoanPayment::route('/create'),
            'edit' => Pages\EditLoanPayment::route('/{record}/edit'),
        ];
    }
}
