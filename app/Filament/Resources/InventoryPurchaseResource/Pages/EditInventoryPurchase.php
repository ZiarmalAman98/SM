<?php

namespace App\Filament\Resources\InventoryPurchaseResource\Pages;

use App\Filament\Resources\InventoryPurchaseResource;
use App\Services\InventoryService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoryPurchase extends EditRecord
{
    protected static string $resource = InventoryPurchaseResource::class;

    protected function afterSave(): void
    {
        app(InventoryService::class)->syncPurchase($this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('Print Purchase'))
                ->icon('heroicon-o-printer')
                ->url(fn() => route('inventory-purchases.print', $this->record))
                ->openUrlInNewTab(),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
