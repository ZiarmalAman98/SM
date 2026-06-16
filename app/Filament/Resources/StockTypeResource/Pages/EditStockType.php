<?php

namespace App\Filament\Resources\StockTypeResource\Pages;

use App\Filament\Resources\StockTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockType extends EditRecord
{
    protected static string $resource = StockTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
