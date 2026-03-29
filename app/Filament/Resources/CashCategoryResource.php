<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashCategoryResource\Pages;
use App\Models\CashCategory;
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

class CashCategoryResource extends Resource
{
    protected static ?string $model = CashCategory::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Kas';

    protected static ?string $navigationLabel = 'Kategori Kas';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Kategori')
                    ->schema([
                        Select::make('type')
                            ->label('Jenis')
                            ->options(CashCategory::types())
                            ->required(),
                        TextInput::make('account_code')
                            ->label('Akun')
                            ->helperText('Contoh: 1001')
                            ->maxLength(50),
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('Jenis')->formatStateUsing(fn (string $state) => CashCategory::types()[$state] ?? $state)->sortable(),
                TextColumn::make('account_code')->label('Akun')->searchable()->sortable(),
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
            ])
            ->defaultSort('type');
    }

    public static function canAccess(): bool
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
            ? $user->isAdmin()
            : false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->guard()->user();

        return $user instanceof \App\Models\User
            ? $user->isAdmin()
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
            'index' => Pages\ListCashCategories::route('/'),
            'create' => Pages\CreateCashCategory::route('/create'),
            'edit' => Pages\EditCashCategory::route('/{record}/edit'),
        ];
    }
}
