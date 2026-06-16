<?php

namespace App\Filament\Resources\ExamResultResource\Pages;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Actions;
use App\Models\Student;
use App\Models\ExamType;
use App\Models\SchoolClass;
use App\Exports\ClassResultsExport;
use Maatwebsite\Excel\Excel;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ExamResultResource;

class ListExamResults extends ListRecords
{
    protected static string $resource = ExamResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('enter-marks')->label('Marks Entry')->icon('heroicon-o-pencil-square')->url(fn() => route('filament.admin.resources.exam-results.enter'))->color('success'),
        ];
    }
}
