<?php

namespace App\Filament\Resources\EmployeeAttendaceReportResource\Pages;

use App\Filament\Resources\EmployeeAttendaceReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeAttendaceReports extends ListRecords
{
    protected static string $resource = EmployeeAttendaceReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url('/admin/employee-attendance')
            ,
        ];
    }
}
