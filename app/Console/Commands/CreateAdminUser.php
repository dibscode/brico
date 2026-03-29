<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * Example:
     * php artisan user:create-admin admin@brico.test --name="Admin BRICO" --password="password"
     */
    protected $signature = 'user:create-admin
        {email : Email untuk admin}
        {--name= : Nama admin}
        {--password= : Password admin}
        {--force : Jika email sudah ada, paksa update user tsb}';

    protected $description = 'Membuat / meng-update akun admin (role=admin).';

    public function handle(): int
    {
        $email = trim((string) $this->argument('email'));
        $name = (string) ($this->option('name') ?: 'Admin');
        $password = (string) ($this->option('password') ?: 'password');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->error("Email tidak valid: {$email}");
            return self::FAILURE;
        }

        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser && (! $this->option('force'))) {
            $this->error('User dengan email tersebut sudah ada. Gunakan opsi --force untuk meng-update.');
            return self::FAILURE;
        }

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => 'admin',
                'password' => Hash::make($password),
            ],
        );

        $this->info('Akun admin berhasil disimpan.');
        $this->line("- ID: {$user->id}");
        $this->line("- Nama: {$user->name}");
        $this->line("- Email: {$user->email}");
        $this->line("- Role: {$user->role}");

        return self::SUCCESS;
    }
}
