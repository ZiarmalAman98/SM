<?php

namespace App\Filament\Resources\DownloadCenterResource\Pages;

use App\Filament\Resources\DownloadCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDownloadCenters extends ListRecords
{
    protected static string $resource = DownloadCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
