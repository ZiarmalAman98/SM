<?php

namespace App\Filament\Resources\AssignmentSubmissionResource\Pages;

use App\Filament\Resources\AssignmentSubmissionResource;
use App\Models\User;
use App\Notifications\StatusChanged;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditAssignmentSubmission extends EditRecord
{
    protected static string $resource = AssignmentSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        try {
            $student = User::find($data['student_id']);

            if ($student) {
                $appUrl = env('APP_URL');

                // Proceed only if not running on localhost
                if (!in_array($appUrl, ['http://127.0.0.1:8000', 'http://localhost'])) {
                    $message = 'Your assignment status has changed.';
                    $student->notify(new StatusChanged($message));
                }
            }
        } catch (\Exception $e) {
            Log::error('Notification sending failed: ' . $e->getMessage());
        }

        return $data;
    }
}
