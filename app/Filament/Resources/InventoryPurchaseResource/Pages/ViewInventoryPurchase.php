<?php

namespace App\Filament\Resources\InventoryPurchaseResource\Pages;

use App\Filament\Resources\InventoryPurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInventoryPurchase extends ViewRecord
{
    protected static string $resource = InventoryPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('Print Purchase'))
                ->icon('heroicon-o-printer')
                ->url(fn() => route('inventory-purchases.print', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
        ];
    }
}
