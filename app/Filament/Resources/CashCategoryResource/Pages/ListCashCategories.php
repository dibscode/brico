<?php

namespace App\Filament\Resources\CashCategoryResource\Pages;

use App\Filament\Resources\CashCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCashCategories extends ListRecords
{
    protected static string $resource = CashCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return static::getResource()::canCreate()
            ? [CreateAction::make()->modal()]
            : [];
    }
}
