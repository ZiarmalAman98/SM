<?php

namespace App\Filament\Parent\Resources\DisciplineResource\Pages;

use App\Filament\Parent\Resources\DisciplineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDisciplines extends ListRecords
{
    protected static string $resource = DisciplineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
