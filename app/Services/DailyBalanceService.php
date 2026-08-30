<?php

namespace App\Services;

use App\Models\DailyTransfer;
use App\Models\Expense;
use App\Models\Income;
use App\Models\ParentInvoicePayment;
use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DailyBalanceService
{
    public function syncIncome(Income $income): void
    {
        if ($income->status === 'rejected') {
            $this->removeSource($income);

            return;
        }

        $this->upsertSource(
            $income,
            'income',
            (float) $income->amount,
            $income->date,
            $this->plainText($income->description, 'Income #'.$income->id),
        );
    }

    public function syncExpense(Expense $expense): void
    {
        if ($expense->status === 'rejected') {
            $this->removeSource($expense);

            return;
        }

        $this->upsertSource(
            $expense,
            'expense',
            (float) $expense->amount,
            $expense->date,
            $this->plainText($expense->description, 'Expense #'.$expense->id),
        );
    }

    public function syncInvoicePayment(ParentInvoicePayment $payment): void
    {
        $receipt = $payment->receipt_number ?: ('#'.$payment->id);

        $this->upsertSource(
            $payment,
            'income',
            (float) $payment->amount,
            $payment->payment_date,
            __('Invoice payment').' '.$receipt,
        );
    }

    public function removeSource(Model $source): void
    {
        Transaction::query()
            ->where('source_type', $source->getMorphClass())
            ->where('source_id', $source->getKey())
            ->whereNull('daily_transfer_id')
            ->delete();
    }

    public function todaySummary(?CarbonInterface $date = null): array
    {
        $date = Carbon::parse($date ?? today())->startOfDay();
        $income = $this->pendingSum('income', $date);
        $expense = $this->pendingSum('expense', $date);

        return [
            'date' => $date,
            'income' => $income,
            'expense' => $expense,
            'net' => round($income - $expense, 2),
        ];
    }

    public function processTransfer(int $destinationUserId, ?CarbonInterface $date = null): DailyTransfer
    {
        $date = Carbon::parse($date ?? today())->startOfDay();
        $summary = $this->todaySummary($date);

        if ($summary['net'] <= 0) {
            throw new \RuntimeException(__('There is no cash remaining to transfer for this date.'));
        }

        return DB::transaction(function () use ($destinationUserId, $date, $summary) {
            $transfer = DailyTransfer::create([
                'amount' => $summary['net'],
                'destination_user_id' => $destinationUserId,
                'reference' => 'DAILY-'.$date->format('Ymd').'-'.now()->format('His'),
                'transfer_date' => $date,
            ]);

            Transaction::create([
                'amount' => $summary['net'],
                'type' => 'transfer_out',
                'description' => __('Daily transfer to staff').' #'.$destinationUserId,
                'daily_transfer_id' => $transfer->id,
                'occurred_on' => $date->toDateString(),
            ]);

            Transaction::create([
                'amount' => $summary['net'],
                'type' => 'transfer_in',
                'description' => __('Daily cash received'),
                'daily_transfer_id' => $transfer->id,
                'occurred_on' => $date->toDateString(),
            ]);

            $this->pendingQuery($date)
                ->whereIn('type', ['income', 'expense'])
                ->update(['daily_transfer_id' => $transfer->id]);

            return $transfer;
        });
    }

    public function backfill(): int
    {
        $count = 0;

        Income::query()->each(function (Income $income) use (&$count) {
            $this->syncIncome($income);
            $count++;
        });

        Expense::query()->each(function (Expense $expense) use (&$count) {
            $this->syncExpense($expense);
            $count++;
        });

        ParentInvoicePayment::query()->each(function (ParentInvoicePayment $payment) use (&$count) {
            $this->syncInvoicePayment($payment);
            $count++;
        });

        return $count;
    }

    protected function upsertSource(Model $source, string $type, float $amount, mixed $date, string $description): void
    {
        $existing = Transaction::query()
            ->where('source_type', $source->getMorphClass())
            ->where('source_id', $source->getKey())
            ->first();

        if (! $existing) {
            $existing = Transaction::query()
                ->whereNull('source_id')
                ->where('type', $type)
                ->where('amount', $amount)
                ->whereNull('daily_transfer_id')
                ->where(function ($query) use ($description, $source) {
                    $query->where('description', $description)
                        ->orWhere('description', 'like', '%#'.$source->getKey().'%');
                })
                ->orderByDesc('id')
                ->first();
        }

        if ($existing?->daily_transfer_id) {
            return;
        }

        $payload = [
            'amount' => $amount,
            'type' => $type,
            'description' => Str::limit($description, 250),
            'occurred_on' => Carbon::parse($date ?? now())->toDateString(),
            'source_type' => $source->getMorphClass(),
            'source_id' => $source->getKey(),
        ];

        if ($existing) {
            $existing->update($payload);

            return;
        }

        Transaction::create($payload);
    }

    protected function pendingSum(string $type, CarbonInterface $date): float
    {
        return (float) $this->pendingQuery($date)
            ->where('type', $type)
            ->sum('amount');
    }

    protected function pendingQuery(CarbonInterface $date)
    {
        return Transaction::query()
            ->whereNull('daily_transfer_id')
            ->where(function ($query) use ($date) {
                $query->whereDate('occurred_on', $date)
                    ->orWhere(function ($inner) use ($date) {
                        $inner->whereNull('occurred_on')
                            ->whereDate('created_at', $date);
                    });
            });
    }

    protected function plainText(?string $value, string $fallback): string
    {
        $text = trim(html_entity_decode(strip_tags((string) $value)));

        return $text !== '' ? $text : $fallback;
    }
}
