<?php

namespace App\Filament\Resources\ClassAttendanceReportResource\Pages;

use App\Filament\Resources\ClassAttendanceReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassAttendanceReport extends EditRecord
{
    protected static string $resource = ClassAttendanceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
