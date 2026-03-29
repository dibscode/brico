<?php

namespace App\Services;

use App\Models\CashCategory;
use App\Models\CashTransaction;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class CashTransactionService
{
    public function record(
        string $type,
        string $categoryName,
        float $amount,
        CarbonInterface|string $occurredAt,
        ?string $description = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $createdBy = null,
    ): ?CashTransaction {
        $amount = round($amount, 2);

        if ($amount <= 0) {
            return null;
        }

        $category = CashCategory::query()->firstOrCreate(
            [
                'type' => $type,
                'name' => $categoryName,
            ],
            [
                'is_active' => true,
            ],
        );

        $query = CashTransaction::query()
            ->where('type', $type)
            ->where('cash_category_id', $category->id);

        if ($referenceType && $referenceId) {
            $query
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId);
        }

        if ($query->exists()) {
            return null;
        }

        return CashTransaction::query()->create([
            'type' => $type,
            'cash_category_id' => $category->id,
            'amount' => $amount,
            'occurred_at' => Carbon::parse($occurredAt),
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'created_by' => $createdBy,
        ]);
    }

    public function recordIncome(
        string $categoryName,
        float $amount,
        CarbonInterface|string $occurredAt,
        ?string $description = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $createdBy = null,
    ): ?CashTransaction {
        return $this->record(
            CashCategory::TYPE_INCOME,
            $categoryName,
            $amount,
            $occurredAt,
            $description,
            $referenceType,
            $referenceId,
            $createdBy,
        );
    }

    public function recordExpense(
        string $categoryName,
        float $amount,
        CarbonInterface|string $occurredAt,
        ?string $description = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $createdBy = null,
    ): ?CashTransaction {
        return $this->record(
            CashCategory::TYPE_EXPENSE,
            $categoryName,
            $amount,
            $occurredAt,
            $description,
            $referenceType,
            $referenceId,
            $createdBy,
        );
    }
}
