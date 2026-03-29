<?php

namespace App\Filament\Resources\LoanRateResource\Pages;

use App\Filament\Resources\LoanRateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLoanRates extends ListRecords
{
    protected static string $resource = LoanRateResource::class;

    protected function getHeaderActions(): array
    {
        return static::getResource()::canCreate()
            ? [CreateAction::make()->modal()]
            : [];
    }
}
