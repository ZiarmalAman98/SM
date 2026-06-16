<?php

namespace App\Filament\Resources\ClassAttendanceReportResource\Pages;

use App\Filament\Resources\ClassAttendanceReportResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;

class ListClassAttendanceReports extends ListRecords
{
    protected static string $resource = ClassAttendanceReportResource::class;

    public function table(Table $table): Table
    {
        return ClassAttendanceReportResource::table($table);
    }
}
