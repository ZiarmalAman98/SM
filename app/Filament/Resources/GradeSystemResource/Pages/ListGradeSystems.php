<?php

namespace App\Filament\Resources\GradeSystemResource\Pages;

use App\Filament\Resources\GradeSystemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGradeSystems extends ListRecords
{
    protected static string $resource = GradeSystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
