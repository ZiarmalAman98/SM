<?php

namespace App\Filament\Resources\InventorySaleResource\Pages;

use App\Filament\Resources\InventorySaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInventorySale extends ViewRecord
{
    protected static string $resource = InventorySaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('Print Receipt'))
                ->icon('heroicon-o-printer')
                ->url(fn() => route('inventory-sales.print', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
        ];
    }
}
