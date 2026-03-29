<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanInstallment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoanScheduleService
{
    public function generateInstallments(Loan $loan): void
    {
        $loan->recalculateTotals();

        $termCount = max(1, (int) $loan->term_count);
        $principalTotal = (float) $loan->principal;
        $interestTotal = (float) $loan->interest_amount;
        $adminFeeTotal = (float) $loan->admin_fee;

        $principalPer = round($principalTotal / $termCount, 2);
        $interestPer = round($interestTotal / $termCount, 2);

        $principalAllocated = 0.0;
        $interestAllocated = 0.0;

        $firstDue = Carbon::parse($loan->first_due_date)->startOfDay();
        $isWeekly = $loan->interest_type === 'weekly';

        DB::transaction(function () use ($loan, $termCount, $principalPer, $interestPer, $principalTotal, $interestTotal, $adminFeeTotal, &$principalAllocated, &$interestAllocated, $firstDue, $isWeekly) {
            $loan->installments()->delete();

            for ($i = 1; $i <= $termCount; $i++) {
                $isLast = $i === $termCount;

                $principalDue = $isLast ? round($principalTotal - $principalAllocated, 2) : $principalPer;
                $interestDue = $isLast ? round($interestTotal - $interestAllocated, 2) : $interestPer;

                if ($i === 1 && $adminFeeTotal > 0) {
                    $interestDue = round($interestDue + $adminFeeTotal, 2);
                }

                $principalAllocated += $principalDue;
                $interestAllocated += ($i === 1 ? max(0, $interestDue - $adminFeeTotal) : $interestDue);

                $dueDate = (clone $firstDue);
                $isWeekly
                    ? $dueDate->addWeeks($i - 1)
                    : $dueDate->addDays($i - 1);

                LoanInstallment::create([
                    'loan_id' => $loan->id,
                    'sequence' => $i,
                    'due_date' => $dueDate->toDateString(),
                    'principal_due' => $principalDue,
                    'interest_due' => $interestDue,
                    'total_due' => round($principalDue + $interestDue, 2),
                    'paid_amount' => 0,
                ]);
            }

            $loan->save();
        });
    }
}
