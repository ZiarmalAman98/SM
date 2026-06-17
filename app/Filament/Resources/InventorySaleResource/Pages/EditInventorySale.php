<?php

namespace App\Filament\Resources\InventorySaleResource\Pages;

use App\Filament\Resources\InventorySaleResource;
use App\Services\InventoryService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInventorySale extends EditRecord
{
    protected static string $resource = InventorySaleResource::class;

    protected function afterSave(): void
    {
        app(InventoryService::class)->syncSale($this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('Print Receipt'))
                ->icon('heroicon-o-printer')
                ->url(fn() => route('inventory-sales.print', $this->record))
                ->openUrlInNewTab(),
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
