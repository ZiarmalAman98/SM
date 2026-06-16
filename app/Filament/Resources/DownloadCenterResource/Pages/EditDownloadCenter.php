<?php

namespace App\Filament\Resources\DownloadCenterResource\Pages;

use App\Filament\Resources\DownloadCenterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDownloadCenter extends EditRecord
{
    protected static string $resource = DownloadCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['uploaded_by']); // Prevents accidental changes
        return $data;
    }
}
