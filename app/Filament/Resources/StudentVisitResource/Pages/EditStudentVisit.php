<?php

namespace App\Filament\Resources\StudentVisitResource\Pages;

use App\Filament\Resources\StudentVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStudentVisit extends EditRecord
{
    protected static string $resource = StudentVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
