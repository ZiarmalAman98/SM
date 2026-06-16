<?php

namespace App\Filament\Student\Resources\HomeworkSubmissionResource\Pages;

use App\Filament\Student\Resources\HomeworkSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeworkSubmissions extends ListRecords
{
    protected static string $resource = HomeworkSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
