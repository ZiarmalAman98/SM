<?php

namespace App\Console\Commands;

use App\Models\DailyTransfer;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessDailyTransfer extends Command
{
    protected $signature = 'transfer:daily';
    protected $description = 'Transfer daily income to destination account';

    public function handle()
    {
        // Calculate today's total income (excluding any transfers)
        $totalIncome = Transaction::where('type', 'income')
            ->whereDate('created_at', today())
            ->whereNull('daily_transfer_id')
            ->sum('amount');

        if ($totalIncome > 0) {
            DB::transaction(function () use ($totalIncome) {
                // Create the daily transfer record
                $transfer = DailyTransfer::create([
                    'amount' => $totalIncome,
                    'destination' => config('app.default_transfer_destination'), // Set this in config
                    'reference' => 'DAILY-' . now()->format('Ymd'),
                    'transfer_date' => now(),
                ]);

                // Create the transfer-out transaction
                Transaction::create([
                    'amount' => $totalIncome,
                    'type' => 'transfer_out',
                    'description' => 'Daily transfer to ' . $transfer->destination,
                    'daily_transfer_id' => $transfer->id,
                ]);

                // Link all today's income transactions to this transfer
                Transaction::where('type', 'income')
                    ->whereDate('created_at', today())
                    ->whereNull('daily_transfer_id')
                    ->update(['daily_transfer_id' => $transfer->id]);
            });

            $this->info('Successfully transferred ؋' . number_format($totalIncome, 2));
        } else {
            $this->info('No income to transfer today.');
        }
    }
}
