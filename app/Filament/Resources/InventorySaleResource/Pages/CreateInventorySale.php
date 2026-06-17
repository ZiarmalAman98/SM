<?php

namespace App\Filament\Resources\InventorySaleResource\Pages;

use App\Filament\Resources\InventorySaleResource;
use App\Services\InventoryService;
use Filament\Resources\Pages\CreateRecord;

class CreateInventorySale extends CreateRecord
{
    protected static string $resource = InventorySaleResource::class;

    protected function afterCreate(): void
    {
        app(InventoryService::class)->syncSale($this->record);
        $this->record->refresh();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', [
            'record' => $this->record,
        ]);
    }
}
