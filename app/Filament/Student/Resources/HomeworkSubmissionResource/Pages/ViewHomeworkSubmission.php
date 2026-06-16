<?php

namespace App\Filament\Student\Resources\HomeworkSubmissionResource\Pages;

use App\Filament\Student\Resources\HomeworkSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHomeworkSubmission extends ViewRecord
{
    protected static string $resource = HomeworkSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
