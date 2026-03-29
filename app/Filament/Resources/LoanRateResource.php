<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanRateResource\Pages;
use App\Models\LoanRate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LoanRateResource extends Resource
{
    protected static ?string $model = LoanRate::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Koperasi';

    protected static ?string $navigationLabel = 'Setting Bunga';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Bunga Pinjaman')
                    ->schema([
                        Select::make('type')
                            ->label('Jenis')
                            ->options(LoanRate::types())
                            ->required(),
                        TextInput::make('rate')
                            ->label('Persentase (%)')
                            ->numeric()
                            ->required(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('Jenis')->formatStateUsing(fn (string $state) => LoanRate::types()[$state] ?? $state)->sortable(),
                TextColumn::make('rate')->label('Bunga (%)')->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('updated_at')->label('Update')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('type');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLoanRates::route('/'),
            'create' => Pages\CreateLoanRate::route('/create'),
            'edit' => Pages\EditLoanRate::route('/{record}/edit'),
        ];
    }
}
