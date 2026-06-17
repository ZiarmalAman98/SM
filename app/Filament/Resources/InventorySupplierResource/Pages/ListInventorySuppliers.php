<?php

namespace App\Filament\Resources\InventorySupplierResource\Pages;

use App\Filament\Resources\InventorySupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInventorySuppliers extends ListRecords
{
    protected static string $resource = InventorySupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
