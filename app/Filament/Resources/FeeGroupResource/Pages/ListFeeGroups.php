<?php

namespace App\Filament\Resources\FeeGroupResource\Pages;

use App\Filament\Resources\FeeGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeGroups extends ListRecords
{
    protected static string $resource = FeeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
