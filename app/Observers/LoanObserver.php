<?php

namespace App\Observers;

use App\Models\Loan;
use App\Services\LoanScheduleService;

class LoanObserver
{
    public function saved(Loan $loan): void
    {
        if (! $loan->wasChanged('status')) {
            return;
        }

        $originalStatus = $loan->getOriginal('status');

        if ($originalStatus !== Loan::STATUS_PENDING) {
            return;
        }

        if ($loan->status !== Loan::STATUS_ACTIVE) {
            return;
        }

        if ($loan->installments()->exists()) {
            return;
        }

        app(LoanScheduleService::class)->generateInstallments($loan);
    }
}
