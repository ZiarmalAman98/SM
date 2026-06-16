<?php

namespace App\Schedulers;

use Illuminate\Console\Scheduling\Schedule;

class TransferScheduler
{
    public function __invoke(Schedule $schedule): void
    {
        $schedule->command('transfer:daily')->dailyAt('23:59');
    }
}
