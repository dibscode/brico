<?php

namespace Database\Seeders;

use App\Models\CashCategory;
use App\Models\LoanRate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->firstOrCreate([
            'email' => 'admin@koperasi.test',
        ], [
            'name' => 'Admin',
            'role' => 'admin',
            'password' => 'password',
        ]);

        $admin->forceFill([
            'name' => 'Admin',
            'role' => 'admin',
            'password' => 'password',
        ])->save();

        $kasir = User::query()->firstOrCreate([
            'email' => 'kasir@koperasi.test',
        ], [
            'name' => 'Kasir',
            'role' => 'kasir',
            'password' => 'password',
        ]);

        $kasir->forceFill([
            'name' => 'Kasir',
            'role' => 'kasir',
            'password' => 'password',
        ])->save();

        $rates = [20, 25, 30, 35, 40, 45, 50];

        foreach ([LoanRate::TYPE_DAILY, LoanRate::TYPE_WEEKLY] as $type) {
            foreach ($rates as $rate) {
                LoanRate::query()->firstOrCreate([
                    'type' => $type,
                    'rate' => $rate,
                ], [
                    'is_active' => true,
                ]);
            }
        }

        $income = [
            'Simpanan',
            'Angsuran Pinjaman',
            'Pendapatan Bunga Pinjaman',
            'Pendapatan Administrasi',
            'Pemasukan Lainnya',
        ];

        $expense = [
            'Pencairan Pinjaman',
            'Biaya Operasional',
            'Pengeluaran Lainnya',
        ];

        foreach ($income as $name) {
            CashCategory::query()->firstOrCreate([
                'type' => CashCategory::TYPE_INCOME,
                'name' => $name,
            ], [
                'is_active' => true,
            ]);
        }

        foreach ($expense as $name) {
            CashCategory::query()->firstOrCreate([
                'type' => CashCategory::TYPE_EXPENSE,
                'name' => $name,
            ], [
                'is_active' => true,
            ]);
        }
    }
}
