<?php

namespace App\Filament\Student\Resources\StudentClassResource\Pages;

use App\Filament\Student\Resources\StudentClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentClass extends ViewRecord
{
    protected static string $resource = StudentClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
          
        ];
    }
}
