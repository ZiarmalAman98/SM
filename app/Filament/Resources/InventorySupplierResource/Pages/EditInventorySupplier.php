<?php

namespace App\Filament\Resources\InventorySupplierResource\Pages;

use App\Filament\Resources\InventorySupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventorySupplier extends EditRecord
{
    protected static string $resource = InventorySupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
