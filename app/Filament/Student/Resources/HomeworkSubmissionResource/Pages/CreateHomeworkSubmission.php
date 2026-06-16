<?php

namespace App\Filament\Student\Resources\HomeworkSubmissionResource\Pages;

use App\Filament\Student\Resources\HomeworkSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeworkSubmission extends CreateRecord
{
    protected static string $resource = HomeworkSubmissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['student_id'] = auth()->id();
        return $data;
    }
}
