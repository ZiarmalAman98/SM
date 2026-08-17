<?php

namespace App\Filament\Resources\InventorySupplierResource\Pages;

use App\Filament\Resources\InventorySupplierResource;
use App\Models\InventoryPurchase;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewInventorySupplier extends ViewRecord
{
    protected static string $resource = InventorySupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('printLedger')
                ->label(__('Print Ledger'))
                ->icon('heroicon-o-printer')
                ->url(fn() => route('inventory-suppliers.ledger', $this->record))
                ->openUrlInNewTab(),
            Actions\Action::make('addPayment')
                ->label(__('Add Payment'))
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->form([
                    Forms\Components\TextInput::make('paid_amount')
                        ->label(__('Paid Amount'))
                        ->prefix('AFN ')
                        ->numeric()
                        ->minValue(0.01)
                        ->required(),
                    Forms\Components\DatePicker::make('purchase_date')
                        ->label(__('Payment Date'))
                        ->jalali()
                        ->locale('fa')
                        ->default(now())
                        ->required(),
                    Forms\Components\Select::make('payment_method')
                        ->label(__('Payment Method'))
                        ->options([
                            'cash' => __('Cash'),
                            'bank' => __('Bank'),
                            'mobile_money' => __('Mobile Money'),
                            'card' => __('Card'),
                            'other' => __('Other'),
                        ])
                        ->native(false)
                        ->default('cash')
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label(__('Notes'))
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->modalSubmitActionLabel(__('Save Payment'))
                ->action(function (array $data): void {
                    InventoryPurchase::create([
                        'purchase_no' => InventoryPurchase::generatePurchaseNo(),
                        'inventory_supplier_id' => $this->record->id,
                        'purchase_date' => $data['purchase_date'],
                        'subtotal' => 0,
                        'discount_percent' => 0,
                        'discount_amount' => 0,
                        'total_amount' => 0,
                        'paid_amount' => $data['paid_amount'],
                        'payment_method' => $data['payment_method'],
                        'balance' => 0,
                        'status' => 'paid',
                        'notes' => $data['notes'] ?? null,
                    ]);

                    $this->record->refresh();

                    Notification::make()
                        ->title(__('Payment added successfully'))
                        ->success()
                        ->send();
                }),
            Actions\EditAction::make(),
        ];
    }
}
