<?php

namespace App\Filament\Resources\InventoryStockMovementResource\Pages;

use App\Filament\Resources\InventoryStockMovementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventoryStockMovement extends EditRecord
{
    protected static string $resource = InventoryStockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
