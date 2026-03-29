<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashTransactionResource\Pages;
use App\Models\CashCategory;
use App\Models\CashTransaction;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CashTransactionResource extends Resource
{
    protected static ?string $model = CashTransaction::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Kas';

    protected static ?string $navigationLabel = 'Transaksi Kas';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaksi Kas')
                    ->schema([
                        Hidden::make('type')
                            ->default(CashCategory::TYPE_INCOME)
                            ->required(),
                        Select::make('cash_category_id')
                            ->label('Kategori')
                            ->options(function (callable $get) {
                                $type = $get('type');

                                if (! $type) {
                                    return [];
                                }

                                return CashCategory::query()
                                    ->where('type', $type)
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all();
                            })
                            ->searchable()
                            ->required(),
                        TextInput::make('debit')
                            ->label('Debit')
                            ->numeric()
                            ->dehydrated(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! filled($state)) {
                                    return;
                                }

                                $set('credit', null);
                                $set('type', CashCategory::TYPE_INCOME);
                                $set('cash_category_id', null);
                            })
                            ->helperText('Debit = penambahan saldo koperasi.'),
                        TextInput::make('credit')
                            ->label('Kredit')
                            ->numeric()
                            ->dehydrated(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! filled($state)) {
                                    return;
                                }

                                $set('debit', null);
                                $set('type', CashCategory::TYPE_EXPENSE);
                                $set('cash_category_id', null);
                            })
                            ->helperText('Kredit = pengurangan saldo koperasi.'),
                        DateTimePicker::make('occurred_at')
                            ->label('Tanggal')
                            ->seconds(false)
                            ->default(now())
                            ->required(),
                        Textarea::make('description')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $runningBalanceSubquery = CashTransaction::query()
            ->from('cash_transactions as ct2')
            ->selectRaw(
                "COALESCE(SUM(CASE WHEN ct2.type = ? THEN ct2.amount ELSE -ct2.amount END), 0)",
                [CashCategory::TYPE_INCOME],
            )
            ->where(function (Builder $sub): void {
                $sub
                    ->whereColumn('ct2.occurred_at', '<', 'cash_transactions.occurred_at')
                    ->orWhere(function (Builder $sub2): void {
                        $sub2
                            ->whereColumn('ct2.occurred_at', '=', 'cash_transactions.occurred_at')
                            ->whereColumn('ct2.id', '<=', 'cash_transactions.id');
                    });
            });

        return $query
            ->select('cash_transactions.*')
            ->selectSub($runningBalanceSubquery, 'running_balance');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')->label('Tanggal')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('category.name')->label('Kategori')->searchable()->sortable(),
                TextColumn::make('description')->label('Keterangan')->limit(60)->toggleable(),
                TextColumn::make('debit')
                    ->label('Debit')
                    ->state(function (CashTransaction $record): ?float {
                        return $record->type === CashCategory::TYPE_INCOME
                            ? (float) $record->amount
                            : null;
                    })
                    ->money('IDR')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("CASE WHEN type = ? THEN amount ELSE 0 END {$direction}", [CashCategory::TYPE_INCOME]);
                    }),
                TextColumn::make('credit')
                    ->label('Kredit')
                    ->state(function (CashTransaction $record): ?float {
                        return $record->type === CashCategory::TYPE_EXPENSE
                            ? (float) $record->amount
                            : null;
                    })
                    ->money('IDR')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw("CASE WHEN type = ? THEN amount ELSE 0 END {$direction}", [CashCategory::TYPE_EXPENSE]);
                    }),
                TextColumn::make('running_balance')->label('Saldo')->money('IDR')->sortable(),
                TextColumn::make('input_by')
                    ->label('Input oleh')
                    ->state(function (CashTransaction $record): string {
                        $creator = $record->creator;

                        if (! $creator) {
                            return '-';
                        }

                        $roleLabel = match ($creator->role) {
                            'admin' => 'Admin',
                            'kasir' => 'Kasir',
                            default => ucfirst((string) $creator->role),
                        };

                        return "{$roleLabel} - {$creator->name}";
                    })
            ->toggleable(),
            ])
            ->defaultSort('occurred_at', 'desc');
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->guard()->user();

        return $user instanceof \App\Models\User
            ? $user->isAdmin()
            : false;
    }

    public static function canCreate(): bool
    {
        $user = auth()->guard()->user();

        return $user instanceof \App\Models\User
            ? ($user->isAdmin() || $user->isKasir())
            : false;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->guard()->user();

        return $user instanceof \App\Models\User
            ? $user->isAdmin()
            : false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCashTransactions::route('/'),
            'create' => Pages\CreateCashTransaction::route('/create'),
            'edit' => Pages\EditCashTransaction::route('/{record}/edit'),
        ];
    }
}
