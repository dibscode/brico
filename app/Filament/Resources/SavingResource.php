<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SavingResource\Pages;
use App\Models\Saving;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Koperasi';

    protected static ?string $navigationLabel = 'Simpanan';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transaksi Simpanan')
                    ->schema([
                        Select::make('member_id')
                            ->label('Anggota')
                            ->relationship('member', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('amount')
                            ->label('Nominal')
                            ->numeric()
                            ->required(),
                        DateTimePicker::make('occurred_at')
                            ->label('Tanggal')
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
                TextColumn::make('occurred_at')->label('Tanggal')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('member.name')->label('Anggota')->searchable()->sortable(),
                TextColumn::make('amount')->label('Nominal')->money('IDR')->sortable(),
                TextColumn::make('creator.name')->label('Input oleh')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('notes')->label('Catatan')->limit(40)->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('occurred_at', 'desc');
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
            'index' => Pages\ListSavings::route('/'),
            'create' => Pages\CreateSaving::route('/create'),
            'edit' => Pages\EditSaving::route('/{record}/edit'),
        ];
    }
}
