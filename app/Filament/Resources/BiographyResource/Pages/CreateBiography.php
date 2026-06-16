<?php

namespace App\Filament\Resources\BiographyResource\Pages;

use App\Filament\Resources\BiographyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBiography extends CreateRecord
{
    protected static string $resource = BiographyResource::class;
}
