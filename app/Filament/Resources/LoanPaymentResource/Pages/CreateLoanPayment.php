<?php

namespace App\Filament\Resources\LoanPaymentResource\Pages;

use App\Filament\Resources\LoanPaymentResource;
use App\Models\LoanPayment;
use App\Services\LoanPaymentService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateLoanPayment extends CreateRecord
{
    protected static string $resource = LoanPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['received_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var LoanPayment $payment */
        $payment = $this->record;

        app(LoanPaymentService::class)->applyPayment($payment);

        Notification::make()->success()->title('Pembayaran tersimpan')->send();
    }
}
