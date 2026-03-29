<?php

namespace App\Filament\Resources\MemberResource\RelationManagers;

use App\Filament\Resources\LoanResource\Pages\ViewLoan;
use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoansRelationManager extends RelationManager
{
    protected static string $relationship = 'loans';

    protected static ?string $title = 'Pinjaman';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('loan_code')->label('Kode')->searchable()->sortable(),
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
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->url(fn (Loan $record) => ViewLoan::getUrl(['record' => $record])),
            ])
            ->recordUrl(fn (Loan $record) => ViewLoan::getUrl(['record' => $record]));
    }
}
