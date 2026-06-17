<?php

namespace App\Filament\Resources\InventoryStockMovementResource\Pages;

use App\Filament\Resources\InventoryStockMovementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventoryStockMovements extends ListRecords
{
    protected static string $resource = InventoryStockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
