<?php

namespace App\Filament\Resources\LoanResource\Pages;

use App\Filament\Resources\LoanResource;
use App\Models\Loan;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;

    protected function getRedirectUrl(): string
    {
        if (auth()->user()?->isKasir()) {
            return $this->getResource()::getUrl('view', ['record' => $this->record]);
        }

        return parent::getRedirectUrl();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['loan_code'] = 'LN-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        $data['applied_at'] = now();
        $data['applied_by'] = auth()->id();
        $data['status'] = Loan::STATUS_PENDING;

        $principal = (float) ($data['principal'] ?? 0);
        $rate = (float) ($data['interest_rate'] ?? 0);
        $adminFee = (float) ($data['admin_fee'] ?? 0);

        $interestAmount = round($principal * ($rate / 100), 2);
        $data['interest_amount'] = $interestAmount;
        $data['total_payable'] = round($principal + $interestAmount + $adminFee, 2);

        return $data;
    }
}
