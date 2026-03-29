<?php

namespace App\Filament\Resources\SavingResource\Pages;

use App\Filament\Resources\SavingResource;
use App\Models\Saving;
use App\Services\CashTransactionService;
use Filament\Resources\Pages\CreateRecord;

class CreateSaving extends CreateRecord
{
    protected static string $resource = SavingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Saving $saving */
        $saving = $this->record;

        $memberName = $saving->member()->value('name');

        app(CashTransactionService::class)->recordIncome(
            categoryName: 'Simpanan',
            amount: (float) $saving->amount,
            occurredAt: $saving->occurred_at,
            description: $memberName ? "Simpanan anggota: {$memberName}" : 'Simpanan anggota',
            referenceType: Saving::class,
            referenceId: $saving->id,
            createdBy: $saving->created_by,
        );
    }
}
