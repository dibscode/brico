<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'loan_code',
        'principal',
        'interest_type',
        'interest_rate',
        'interest_amount',
        'admin_fee',
        'total_payable',
        'term_count',
        'first_due_date',
        'status',
        'applied_at',
        'applied_by',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_note',
        'disbursed_at',
        'disbursed_by',
    ];

    protected $casts = [
        'principal' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'term_count' => 'int',
        'first_due_date' => 'date',
        'applied_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'disbursed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_ACTIVE => 'Aktif',
            self::STATUS_PAID => 'Lunas',
            self::STATUS_OVERDUE => 'Menunggak',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class)->orderBy('sequence');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class)->orderByDesc('paid_at');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applied_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function disburser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function recalculateTotals(): void
    {
        $interestAmount = round(((float) $this->principal) * (((float) $this->interest_rate) / 100), 2);

        $this->interest_amount = $interestAmount;
        $this->total_payable = round(((float) $this->principal) + $interestAmount + ((float) $this->admin_fee), 2);
    }

    public function getOutstandingAmount(): float
    {
        $paid = (float) $this->payments()->sum('amount');

        return max(0, (float) $this->total_payable - $paid);
    }

    public function isOverdue(Carbon|string|null $asOf = null): bool
    {
        $asOf = $asOf ? Carbon::parse($asOf) : now();

        return $this->installments()
            ->whereDate('due_date', '<', $asOf->toDateString())
            ->whereColumn('paid_amount', '<', 'total_due')
            ->exists();
    }
}
