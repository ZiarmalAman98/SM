<?php

namespace App\Filament\Resources\InventoryPurchaseResource\Pages;

use App\Filament\Resources\InventoryPurchaseResource;
use App\Services\InventoryService;
use Filament\Resources\Pages\CreateRecord;

class CreateInventoryPurchase extends CreateRecord
{
    protected static string $resource = InventoryPurchaseResource::class;

    protected function afterCreate(): void
    {
        app(InventoryService::class)->syncPurchase($this->record);
    }
}
