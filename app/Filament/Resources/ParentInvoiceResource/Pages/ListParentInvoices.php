<?php

namespace App\Filament\Resources\ParentInvoiceResource\Pages;

use App\Filament\Resources\ParentInvoiceResource;
use App\Services\ParentInvoiceBuilder;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListParentInvoices extends ListRecords
{
    protected static string $resource = ParentInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        $period = ParentInvoiceBuilder::billingPeriodForGeneration();

        return [
            Actions\Action::make('generate_monthly')
                ->label(__('Generate All Invoices'))
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->modalHeading(__('Generate All Monthly Invoices'))
                ->modalDescription(__('This will create missing invoices for every family with active fees or unpaid inventory sales. Unpaid sales are also added to existing invoices for the selected month and year.'))
                ->form([
                    Forms\Components\Select::make('billing_month')
                        ->label(__('Billing Month'))
                        ->options(ParentInvoiceBuilder::MONTHS)
                        ->default($period['month'])
                        ->native(false)
                        ->required(),
                    Forms\Components\TextInput::make('billing_year')
                        ->label(__('Billing Year'))
                        ->numeric()
                        ->minValue(1400)
                        ->maxValue(1500)
                        ->default($period['year'])
                        ->required(),
                ])
                ->requiresConfirmation()
                ->action(function (array $data): void {
                    $summary = app(ParentInvoiceBuilder::class)->generateMonthly([
                        'billing_month' => $data['billing_month'],
                        'billing_year' => (int) $data['billing_year'],
                        'invoice_date' => now(),
                        'due_date' => now()->addDays(3),
                        'notes' => __('Manually generated monthly invoice.'),
                    ]);

                    $body = __(
                        'Created: :created | Sales added to existing: :updated | Existing skipped: :existing | No fee skipped: :empty | Failed: :failed',
                        [
                            'created' => $summary['created'],
                            'updated' => $summary['updated'] ?? 0,
                            'existing' => $summary['skipped_existing'],
                            'empty' => $summary['skipped_empty'],
                            'failed' => $summary['failed'],
                        ],
                    );

                    Notification::make()
                        ->title($summary['failed'] > 0 ? __('Invoice generation completed with errors') : __('Invoices generated successfully'))
                        ->body($body)
                        ->color($summary['failed'] > 0 ? 'warning' : 'success')
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
                    'all' => Tab::make(__('All'))
                        ->icon('heroicon-o-squares-2x2')
                        ->badge(fn () => $this->getModel()::count())
                        ->badgeColor('gray'),

                    'partial' => Tab::make(__('Partial'))
                        ->icon('heroicon-o-clock')
                        ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'partial'))
                        ->badge(fn () => $this->getModel()::where('status', 'partial')->count())
                        ->badgeColor('warning'),

                    'issued' => Tab::make(__('Issued'))
                        ->icon('heroicon-o-document-text')
                        ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'issued'))
                        ->badge(fn () => $this->getModel()::where('status', 'issued')->count())
                        ->badgeColor('info'),

                    'paid' => Tab::make(__('Paid'))
                        ->icon('heroicon-o-check-circle')
                        ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid'))
                        ->badge(fn () => $this->getModel()::where('status', 'paid')->count())
                        ->badgeColor('success'),

        ];
    }
}
