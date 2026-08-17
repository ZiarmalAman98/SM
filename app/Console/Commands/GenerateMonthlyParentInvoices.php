<?php

namespace App\Console\Commands;

use App\Services\ParentInvoiceBuilder;
use Illuminate\Console\Command;
use Morilog\Jalali\Jalalian;

class GenerateMonthlyParentInvoices extends Command
{
    protected $signature = 'invoices:generate-monthly
        {--month= : Billing month name, for example Hamal}
        {--year= : Billing year, for example 1405}
        {--force : Generate even when today is not an automatic invoice generation day}';

    protected $description = 'Generate monthly parent invoices for all families with active class fees.';

    public function handle(ParentInvoiceBuilder $builder): int
    {
        $today = Jalalian::now();

        if (! $this->option('force') && ! ParentInvoiceBuilder::canRunAutomaticGeneration($today)) {
            $this->info('Skipped. Automatic generation only runs from Shamsi day 27 to the end of the month, or on Shamsi day 1 and 2.');

            return self::SUCCESS;
        }

        $period = ParentInvoiceBuilder::billingPeriodForGeneration($today);
        $month = $this->option('month') ?: $period['month'];
        $year = (int) ($this->option('year') ?: $period['year']);

        if (! array_key_exists($month, ParentInvoiceBuilder::MONTHS)) {
            $this->error("Invalid billing month [{$month}].");

            return self::FAILURE;
        }

        $summary = $builder->generateMonthly([
            'billing_month' => $month,
            'billing_year' => $year,
            'invoice_date' => now(),
            'due_date' => now()->addDays(3),
            'notes' => __('Automatically generated monthly invoice.'),
        ]);

        $this->info("Created: {$summary['created']}");
        $this->info("Sales added to existing: {$summary['updated']}");
        $this->info("Existing skipped: {$summary['skipped_existing']}");
        $this->info("No active fee skipped: {$summary['skipped_empty']}");
        $this->info("Failed: {$summary['failed']}");

        foreach ($summary['errors'] as $error) {
            $this->warn($error);
        }

        return $summary['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
