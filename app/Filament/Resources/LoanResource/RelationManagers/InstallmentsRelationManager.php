<?php

namespace App\Filament\Resources\LoanResource\RelationManagers;

use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use App\Services\LoanPaymentService;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class InstallmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'installments';

    protected static ?string $title = 'Angsuran';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sequence')->label('#')->sortable(),
                TextColumn::make('due_date')->label('Jatuh Tempo')->date('d/m/Y')->sortable(),
                TextColumn::make('principal_due')->label('Pokok')->money('IDR'),
                TextColumn::make('interest_due')->label('Bunga/Admin')->money('IDR'),
                TextColumn::make('total_due')->label('Total')->money('IDR'),
                TextColumn::make('paid_amount')->label('Dibayar')->money('IDR'),
                IconColumn::make('paid_at')->label('Lunas')->boolean(fn (LoanInstallment $record) => $record->isPaid()),
            ])
            ->defaultSort('sequence')
            ->actions([
                Action::make('pay')
                    ->label('Bayar')
                    ->button()
                    ->color('success')
                    ->visible(fn (LoanInstallment $record) => ! $record->isPaid())
                    ->disabled(function (LoanInstallment $record): bool {
                        // Require sequential payments: can't pay this installment if an earlier one is still unpaid.
                        return $record->loan
                            ->installments()
                            ->where('sequence', '<', $record->sequence)
                            ->whereColumn('paid_amount', '<', 'total_due')
                            ->exists();
                    })
                    ->form(function (LoanInstallment $record): array {
                        $remaining = max(0, (float) $record->total_due - (float) $record->paid_amount);

                        return [
                            TextInput::make('amount')
                                ->label('Nominal')
                                ->numeric()
                                ->required()
                                ->default($remaining)
                                ->minValue(0.01),
                            DateTimePicker::make('paid_at')
                                ->label('Tanggal Bayar')
                                ->seconds(false)
                                ->default(now())
                                ->required(),
                            Textarea::make('notes')
                                ->label('Catatan')
                                ->rows(2)
                                ->default('Bayar angsuran #' . $record->sequence),
                        ];
                    })
                    ->action(function (LoanInstallment $record, array $data): void {
                        $loan = $record->loan;

                        $payment = LoanPayment::create([
                            'loan_id' => $loan->id,
                            'member_id' => $loan->member_id,
                            'amount' => (float) ($data['amount'] ?? 0),
                            'paid_at' => $data['paid_at'] ?? now(),
                            'notes' => $data['notes'] ?? null,
                            'received_by' => Auth::id(),
                        ]);

                        app(LoanPaymentService::class)->applyPayment($payment);

                        Notification::make()->success()->title('Pembayaran tersimpan')->send();
                    }),
            ]);
    }

}
