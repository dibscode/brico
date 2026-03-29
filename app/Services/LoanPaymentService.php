<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\LoanPayment;
use Illuminate\Support\Facades\DB;
use LogicException;

class LoanPaymentService
{
    public function applyPayment(LoanPayment $payment): void
    {
        $loan = $payment->loan()->lockForUpdate()->first();

        if (! $loan) {
            throw new LogicException('Loan not found for payment.');
        }

        if (! in_array($loan->status, [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE, Loan::STATUS_PAID], true)) {
            throw new LogicException('Cannot accept payment for a loan that is not active.');
        }

        DB::transaction(function () use ($loan, $payment) {
            $remainingForComponents = (float) $payment->amount;
            $remainingForSchedule = (float) $payment->amount;

            $adminFeePaid = (float) $loan->payments()->sum('admin_fee_component');
            $interestPaid = (float) $loan->payments()->sum('interest_component');
            $principalPaid = (float) $loan->payments()->sum('principal_component');

            $adminFeeRemaining = max(0, (float) $loan->admin_fee - $adminFeePaid);
            $interestRemaining = max(0, (float) $loan->interest_amount - $interestPaid);
            $principalRemaining = max(0, (float) $loan->principal - $principalPaid);

            $adminComponent = min($remainingForComponents, $adminFeeRemaining);
            $remainingForComponents = round($remainingForComponents - $adminComponent, 2);

            $interestComponent = min($remainingForComponents, $interestRemaining);
            $remainingForComponents = round($remainingForComponents - $interestComponent, 2);

            $principalComponent = min($remainingForComponents, $principalRemaining);
            $remainingForComponents = round($remainingForComponents - $principalComponent, 2);

            $payment->admin_fee_component = round($adminComponent, 2);
            $payment->interest_component = round($interestComponent, 2);
            $payment->principal_component = round($principalComponent, 2);
            $payment->save();

            /** @var LoanInstallment[] $installments */
            $installments = $loan->installments()
                ->orderBy('sequence')
                ->lockForUpdate()
                ->get()
                ->all();

            foreach ($installments as $installment) {
                if ($remainingForSchedule <= 0) {
                    break;
                }

                $dueRemaining = max(0, (float) $installment->total_due - (float) $installment->paid_amount);

                if ($dueRemaining <= 0) {
                    continue;
                }

                $pay = min($remainingForSchedule, $dueRemaining);

                $installment->paid_amount = round(((float) $installment->paid_amount) + $pay, 2);

                if ($installment->isPaid() && ! $installment->paid_at) {
                    $installment->paid_at = $payment->paid_at;
                }

                $installment->save();

                $remainingForSchedule = round($remainingForSchedule - $pay, 2);
            }

            $allPaid = ! $loan->installments()
                ->whereColumn('paid_amount', '<', 'total_due')
                ->exists();

            if ($allPaid) {
                $loan->status = Loan::STATUS_PAID;
            } else {
                $loan->status = $loan->isOverdue() ? Loan::STATUS_OVERDUE : Loan::STATUS_ACTIVE;
            }

            $loan->save();

            $cashService = app(CashTransactionService::class);
            $memberName = $loan->member()->value('name');
            $loanLabel = $loan->loan_code ?: ('#' . $loan->id);

            $baseDescription = $memberName
                ? "Pembayaran pinjaman {$loanLabel} ({$memberName})"
                : "Pembayaran pinjaman {$loanLabel}";

            $cashService->recordIncome(
                categoryName: 'Angsuran Pinjaman',
                amount: (float) $payment->principal_component,
                occurredAt: $payment->paid_at,
                description: $baseDescription . ' - Pokok',
                referenceType: LoanPayment::class,
                referenceId: $payment->id,
                createdBy: $payment->received_by,
            );

            $cashService->recordIncome(
                categoryName: 'Pendapatan Bunga Pinjaman',
                amount: (float) $payment->interest_component,
                occurredAt: $payment->paid_at,
                description: $baseDescription . ' - Bunga',
                referenceType: LoanPayment::class,
                referenceId: $payment->id,
                createdBy: $payment->received_by,
            );

            $cashService->recordIncome(
                categoryName: 'Pendapatan Administrasi',
                amount: (float) $payment->admin_fee_component,
                occurredAt: $payment->paid_at,
                description: $baseDescription . ' - Administrasi',
                referenceType: LoanPayment::class,
                referenceId: $payment->id,
                createdBy: $payment->received_by,
            );
        });
    }
}
