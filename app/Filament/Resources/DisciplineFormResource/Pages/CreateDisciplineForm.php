<?php

namespace App\Filament\Resources\DisciplineFormResource\Pages;

use App\Filament\Resources\DisciplineFormResource;
use App\Models\ParentStudent;
use App\Models\User;
use App\Notifications\StatusChanged;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateDisciplineForm extends CreateRecord
{
    protected static string $resource = DisciplineFormResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        try {
            $student = User::find($data['student_id']);
            $parents = ParentStudent::where("student_id", $student->id)->get();

            $appUrl = env('APP_URL');

            // Proceed only if not running on localhost
            if (!in_array($appUrl, ['http://127.0.0.1:8000', 'http://localhost'])) {
                $status = $data['status'] ?? 'warning';
                $message = "Your child has {$status}.";

                foreach ($parents as $p) {
                    if ($p) {
                        $p->parentGuard->notify(new StatusChanged($message));
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Notification sending failed: ' . $e->getMessage());
        }

        return $data;
    }
}
