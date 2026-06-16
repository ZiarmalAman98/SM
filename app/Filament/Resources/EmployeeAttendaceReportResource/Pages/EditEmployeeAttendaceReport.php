<?php

namespace App\Filament\Resources\EmployeeAttendaceReportResource\Pages;

use App\Filament\Resources\EmployeeAttendaceReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeAttendaceReport extends EditRecord
{
    protected static string $resource = EmployeeAttendaceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
