<?php

namespace App\Filament\Resources\FeeDiscountResource\Pages;

use App\Filament\Resources\FeeDiscountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeDiscounts extends ListRecords
{
    protected static string $resource = FeeDiscountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
