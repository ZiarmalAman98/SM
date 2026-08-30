<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DailyBalanceService;
use Illuminate\Console\Command;

class ProcessDailyTransfer extends Command
{
    protected $signature = 'transfer:daily {--user= : Destination staff user id}';

    protected $description = 'Transfer today remaining cash (income + invoice payments − expenses) to a staff account';

    public function handle(DailyBalanceService $dailyBalance): int
    {
        $userId = $this->option('user')
            ? (int) $this->option('user')
            : (int) User::query()->where('type', 'staff')->value('id');

        if ($userId <= 0) {
            $this->error('No destination staff user found. Pass --user=ID.');

            return self::FAILURE;
        }

        $summary = $dailyBalance->todaySummary();

        if ($summary['net'] <= 0) {
            $this->info('No cash remaining to transfer today.');

            return self::SUCCESS;
        }

        $transfer = $dailyBalance->processTransfer($userId);

        $this->info('Transferred AFN '.number_format((float) $transfer->amount, 2).' ('.$transfer->reference.')');

        return self::SUCCESS;
    }
}
