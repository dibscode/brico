<?php

namespace App\Filament\Widgets;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use Filament\Widgets\ChartWidget;

class ProfitLossChart extends ChartWidget
{
    protected ?string $heading = 'Tren Kas (bulan ini)';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = now()->startOfMonth()->startOfDay();
        $daysInMonth = (int) now()->daysInMonth;

        $labels = [];
        $incomeByDay = array_fill(0, $daysInMonth, 0.0);
        $expenseByDay = array_fill(0, $daysInMonth, 0.0);

        $end = now()->endOfMonth()->endOfDay();

        $transactions = CashTransaction::query()
            ->whereBetween('occurred_at', [$start, $end])
            ->get(['type', 'amount', 'occurred_at']);

        foreach ($transactions as $tx) {
            $dayIndex = ((int) $tx->occurred_at->format('j')) - 1;

            if ($dayIndex < 0 || $dayIndex >= $daysInMonth) {
                continue;
            }

            if ($tx->type === CashCategory::TYPE_INCOME) {
                $incomeByDay[$dayIndex] += (float) $tx->amount;
            } elseif ($tx->type === CashCategory::TYPE_EXPENSE) {
                $expenseByDay[$dayIndex] += (float) $tx->amount;
            }
        }

        for ($i = 0; $i < $daysInMonth; $i++) {
            $labels[] = $start->copy()->addDays($i)->format('d/m');
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => array_map(fn (float $v) => round($v, 2), $incomeByDay),
                    'tension' => 0.25,
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => array_map(fn (float $v) => round($v, 2), $expenseByDay),
                    'tension' => 0.25,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
