<?php

namespace App\Filament\Resources\DownloadCenterResource\Pages;

use App\Filament\Resources\DownloadCenterResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDownloadCenter extends CreateRecord
{
    protected static string $resource = DownloadCenterResource::class;

    /**
     * Automatically assign `uploaded_by` on create.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_by'] = Auth::id();
        return $data;
    }
}
