<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('loans:refresh-overdue', function (\App\Services\LoanOverdueService $service) {
    $updated = $service->refreshStatuses();

    $this->info("Updated {$updated} loan status(es).");
})->purpose('Refresh pinjaman menunggak / lunas status');

Schedule::command('loans:refresh-overdue')->dailyAt('00:05');
