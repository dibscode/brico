<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers\LoansRelationManager;
use App\Models\Loan;
use App\Models\Member;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Koperasi';

    protected static ?string $navigationLabel = 'Anggota';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Anggota')
                    ->schema([
                        TextInput::make('member_code')
                            ->label('No. Ktp')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->maxLength(50),
                        DatePicker::make('joined_at')
                            ->label('Tanggal Bergabung'),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
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
                TextColumn::make('member_code')->label('No. Ktp')->searchable()->sortable(),
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('phone')->label('Telepon')->searchable(),
                TextColumn::make('joined_at')->label('Gabung')->date()->sortable(),
                IconColumn::make('is_active')->label('Aktif')->boolean(),
                TextColumn::make('created_at')->label('Dibuat')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kredit')
                    ->label('Kredit')
                    ->options([
                        'macet' => 'Macet',
                        'belum_bayar' => 'Belum Bayar',
                        'lunas' => 'Lunas',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        return match ($value) {
                            'macet' => $query->whereHas('loans', fn (Builder $q) => $q->where('status', Loan::STATUS_OVERDUE)),
                            'belum_bayar' => $query->whereHas('loans', fn (Builder $q) => $q->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])),
                            'lunas' => $query
                                ->whereHas('loans', fn (Builder $q) => $q->where('status', Loan::STATUS_PAID))
                                ->whereDoesntHave('loans', fn (Builder $q) => $q->whereIn('status', [Loan::STATUS_PENDING, Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])),
                            default => $query,
                        };
                    }),
            ])
            ->recordUrl(fn (Member $record): string => Pages\ViewMember::getUrl(['record' => $record]))
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            LoansRelationManager::class,
        ];
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
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
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'view' => Pages\ViewMember::route('/{record}'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
