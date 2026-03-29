<?php

namespace App\Filament\Resources\CashTransactionResource\Pages;

use App\Filament\Resources\CashTransactionResource;
use App\Models\CashCategory;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditCashTransaction extends EditRecord
{
    protected static string $resource = CashTransactionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $amount = (float) ($data['amount'] ?? 0);

        if (($data['type'] ?? null) === CashCategory::TYPE_INCOME) {
            $data['debit'] = $amount;
            $data['credit'] = null;
        } elseif (($data['type'] ?? null) === CashCategory::TYPE_EXPENSE) {
            $data['credit'] = $amount;
            $data['debit'] = null;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $debit = (float) ($data['debit'] ?? 0);
        $credit = (float) ($data['credit'] ?? 0);

        if (($debit > 0) && ($credit > 0)) {
            throw ValidationException::withMessages([
                'debit' => 'Isi salah satu saja: Debit atau Kredit.',
                'credit' => 'Isi salah satu saja: Debit atau Kredit.',
            ]);
        }

        if (($debit <= 0) && ($credit <= 0)) {
            throw ValidationException::withMessages([
                'debit' => 'Debit atau Kredit wajib diisi.',
                'credit' => 'Debit atau Kredit wajib diisi.',
            ]);
        }

        if ($debit > 0) {
            $data['type'] = CashCategory::TYPE_INCOME;
            $data['amount'] = round($debit, 2);
        } else {
            $data['type'] = CashCategory::TYPE_EXPENSE;
            $data['amount'] = round($credit, 2);
        }

        unset($data['debit'], $data['credit']);

        return $data;
    }
}
