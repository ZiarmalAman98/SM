<?php

namespace App\Filament\Resources\ExamResultResource\Pages;

use App\Filament\Resources\ExamResultResource;
use App\Models\User;
use App\Notifications\StatusChanged;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateExamResult extends CreateRecord
{
    protected static string $resource = ExamResultResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            $student = User::find($data['student_id']);

            if ($student) {
                $appUrl = env('APP_URL');

                // Proceed only if not running on localhost
                if (!in_array($appUrl, ['http://127.0.0.1:8000', 'http://localhost'])) {
                    $subject = $data['subject'] ?? 'the subject';
                    $message = "Your exam score for {$subject} has been added.";
                    $student->notify(new StatusChanged($message));
                }
            }
        } catch (\Exception $e) {
            Log::error('Notification sending failed: ' . $e->getMessage());
        }

        return $data;
    }
}
