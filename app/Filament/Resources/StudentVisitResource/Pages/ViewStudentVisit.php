<?php

namespace App\Filament\Resources\StudentVisitResource\Pages;

use App\Filament\Resources\StudentVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentVisit extends ViewRecord
{
    protected static string $resource = StudentVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
