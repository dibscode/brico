<?php

namespace App\Services;

use App\Models\Loan;
use Illuminate\Support\Carbon;

class LoanOverdueService
{
    public function refreshStatuses(?Carbon $asOf = null): int
    {
        $asOf = $asOf ?? now();
        $updated = 0;

        $loans = Loan::query()
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])
            ->get();

        foreach ($loans as $loan) {
            $allPaid = ! $loan->installments()
                ->whereColumn('paid_amount', '<', 'total_due')
                ->exists();

            $newStatus = $allPaid
                ? Loan::STATUS_PAID
                : ($loan->isOverdue($asOf) ? Loan::STATUS_OVERDUE : Loan::STATUS_ACTIVE);

            if ($loan->status !== $newStatus) {
                $loan->status = $newStatus;
                $loan->save();
                $updated++;
            }
        }

        return $updated;
    }
}
