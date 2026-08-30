<?php

namespace App\Console\Commands;

use App\Services\DailyBalanceService;
use Illuminate\Console\Command;

class SyncDailyBalance extends Command
{
    protected $signature = 'daily-balance:sync';

    protected $description = 'Sync existing income, expense, and invoice payments into the daily balance ledger';

    public function handle(DailyBalanceService $dailyBalance): int
    {
        $count = $dailyBalance->backfill();

        $this->info("Synced {$count} finance records into daily balance.");

        return self::SUCCESS;
    }
}
