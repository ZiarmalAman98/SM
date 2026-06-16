<?php

namespace App\Filament\Resources\StudentAttendanceReportResource\Pages;

use App\Filament\Resources\StudentAttendanceReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentAttendanceReports extends ListRecords
{
    protected static string $resource = StudentAttendanceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
