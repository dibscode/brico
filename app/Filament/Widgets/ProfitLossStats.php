<?php

namespace App\Filament\Widgets;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProfitLossStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $start = now()->startOfMonth()->startOfDay();
        $end = now()->endOfMonth()->endOfDay();

        $income = (float) CashTransaction::query()
            ->where('type', CashCategory::TYPE_INCOME)
            ->whereBetween('occurred_at', [$start, $end])
            ->sum('amount');

        $expense = (float) CashTransaction::query()
            ->where('type', CashCategory::TYPE_EXPENSE)
            ->whereBetween('occurred_at', [$start, $end])
            ->sum('amount');

        $net = $income - $expense;

        return [
            Stat::make('Pemasukan (bulan ini)', $this->formatIdr($income))
                ->color('success'),
            Stat::make('Pengeluaran (bulan ini)', $this->formatIdr($expense))
                ->color('danger'),
            Stat::make('Laba / Rugi (bulan ini)', $this->formatIdr($net))
                ->color($net >= 0 ? 'success' : 'danger'),
        ];
    }

    private function formatIdr(float $amount): string
    {
        $rounded = (int) round($amount, 0);

        return 'Rp ' . number_format($rounded, 0, ',', '.');
    }
}
