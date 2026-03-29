<?php

namespace App\Console\Commands;

use Laravel\Octane\Commands\StartRoadRunnerCommand;

class WindowsOctaneStartRoadRunnerCommand extends StartRoadRunnerCommand
{
    public function getSubscribedSignals(): array
    {
        if (PHP_OS_FAMILY === 'Windows' || ! extension_loaded('pcntl')) {
            return [];
        }

        return parent::getSubscribedSignals();
    }
}
