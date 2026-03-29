<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ProfitLossReport extends Page
{
    protected static string | \UnitEnum | null $navigationGroup = 'Kas';

    protected static ?string $navigationLabel = 'Laba Rugi';

    protected static ?int $navigationSort = 99;

    protected static ?string $slug = 'laba-rugi';

    protected string $view = 'filament.pages.profit-loss-report';

    public function getTitle(): string
    {
        return 'Laba Rugi';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ProfitLossStats::class,
            \App\Filament\Widgets\ProfitLossChart::class,
        ];
    }
}
