<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'account_code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public const TYPE_INCOME = 'income';
    public const TYPE_EXPENSE = 'expense';

    public static function types(): array
    {
        return [
            self::TYPE_INCOME => 'Pemasukan',
            self::TYPE_EXPENSE => 'Pengeluaran',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashTransaction::class);
    }
}
