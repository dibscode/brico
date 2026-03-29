<?php

namespace App\Providers;

use App\Console\Commands\WindowsOctaneStartCommand;
use App\Console\Commands\WindowsOctaneStartRoadRunnerCommand;
use App\Models\Loan;
use App\Observers\LoanObserver;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Commands\StartCommand;
use Laravel\Octane\Commands\StartRoadRunnerCommand;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->app->bind(StartCommand::class, WindowsOctaneStartCommand::class);
            $this->app->bind(StartRoadRunnerCommand::class, WindowsOctaneStartRoadRunnerCommand::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Loan::observe(LoanObserver::class);
    }
}
