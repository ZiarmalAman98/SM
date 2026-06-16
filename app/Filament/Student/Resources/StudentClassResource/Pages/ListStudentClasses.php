<?php

namespace App\Filament\Student\Resources\StudentClassResource\Pages;

use App\Filament\Student\Resources\StudentClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentClasses extends ListRecords
{
    protected static string $resource = StudentClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
