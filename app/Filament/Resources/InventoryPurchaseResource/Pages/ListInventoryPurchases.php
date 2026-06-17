<?php

namespace App\Filament\Resources\InventoryPurchaseResource\Pages;

use App\Filament\Resources\InventoryPurchaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventoryPurchases extends ListRecords
{
    protected static string $resource = InventoryPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
