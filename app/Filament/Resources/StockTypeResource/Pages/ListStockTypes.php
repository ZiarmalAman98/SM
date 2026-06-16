<?php

namespace App\Filament\Resources\StockTypeResource\Pages;

use App\Filament\Resources\StockTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockTypes extends ListRecords
{
    protected static string $resource = StockTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
