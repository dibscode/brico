<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'rate',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'bool',
    ];

    public const TYPE_DAILY = 'daily';
    public const TYPE_WEEKLY = 'weekly';

    public static function types(): array
    {
        return [
            self::TYPE_DAILY => 'Harian',
            self::TYPE_WEEKLY => 'Mingguan',
        ];
    }
}
