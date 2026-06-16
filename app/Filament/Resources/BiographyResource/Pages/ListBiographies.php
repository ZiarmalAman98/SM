<?php

namespace App\Filament\Resources\BiographyResource\Pages;

use App\Filament\Resources\BiographyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBiographies extends ListRecords
{
    protected static string $resource = BiographyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
