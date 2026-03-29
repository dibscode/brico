<?php

namespace App\Services;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use Illuminate\Support\Carbon;

class CashLedgerReportService
{
    /**
    * @return array{openingBalance:float,rows:array<int, array{occurred_at:string,category:string,description:string,debit:float,credit:float,balance:float,input_by:string}>}
     */
    public function build(Carbon $start, Carbon $end): array
    {
        $openingBalance = (float) CashTransaction::query()
            ->where('occurred_at', '<', $start)
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN type = ? THEN amount ELSE -amount END), 0) as balance',
                [CashCategory::TYPE_INCOME],
            )
            ->value('balance');

        $transactions = CashTransaction::query()
            ->with(['category', 'creator'])
            ->whereBetween('occurred_at', [$start, $end])
            ->orderBy('occurred_at')
            ->orderBy('id')
            ->get();

        $running = $openingBalance;

        $rows = $transactions
            ->map(function (CashTransaction $transaction) use (&$running): array {
                $amount = (float) $transaction->amount;

                $debit = 0.0;
                $credit = 0.0;

                if ($transaction->type === CashCategory::TYPE_INCOME) {
                    $debit = $amount;
                    $running += $amount;
                } else {
                    $credit = $amount;
                    $running -= $amount;
                }

                $creator = $transaction->creator;
                $inputBy = '-';

                if ($creator) {
                    $roleLabel = match ($creator->role) {
                        'admin' => 'Admin',
                        'kasir' => 'Kasir',
                        default => ucfirst((string) $creator->role),
                    };

                    $inputBy = "{$roleLabel} - {$creator->name}";
                }

                return [
                    'occurred_at' => $transaction->occurred_at?->format('d/m/Y H:i') ?? '-',
                    'category' => $transaction->category?->name ?? '-',
                    'description' => $transaction->description ?? '-',
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $running,
                    'input_by' => $inputBy,
                ];
            })
            ->values()
            ->all();

        return [
            'openingBalance' => $openingBalance,
            'rows' => $rows,
        ];
    }

    public function makePeriodLabel(Carbon $start, Carbon $end, string $period): string
    {
        return match ($period) {
            'daily' => 'Export Harian: ' . $start->format('d/m/Y'),
            'weekly' => 'Export Mingguan: ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'),
            'monthly' => 'Export Bulanan: ' . $start->format('m/Y'),
            'yearly' => 'Export Tahunan: ' . $start->format('Y'),
            default => 'Periode: ' . $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'),
        };
    }
}
