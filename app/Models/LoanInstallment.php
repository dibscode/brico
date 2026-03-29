<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'sequence',
        'due_date',
        'principal_due',
        'interest_due',
        'total_due',
        'paid_amount',
        'paid_at',
    ];

    protected $casts = [
        'sequence' => 'int',
        'due_date' => 'date',
        'principal_due' => 'decimal:2',
        'interest_due' => 'decimal:2',
        'total_due' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function isPaid(): bool
    {
        return (float) $this->paid_amount >= (float) $this->total_due;
    }
}
