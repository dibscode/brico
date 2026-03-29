<?php

namespace App\Console\Commands;

use Laravel\Octane\Commands\StartCommand;

class WindowsOctaneStartCommand extends StartCommand
{
    public function getSubscribedSignals(): array
    {
        if (PHP_OS_FAMILY === 'Windows' || ! extension_loaded('pcntl')) {
            return [];
        }

        return parent::getSubscribedSignals();
    }
}
