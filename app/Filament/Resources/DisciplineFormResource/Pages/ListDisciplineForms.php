<?php

namespace App\Filament\Resources\DisciplineFormResource\Pages;

use App\Filament\Resources\DisciplineFormResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDisciplineForms extends ListRecords
{
    protected static string $resource = DisciplineFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
