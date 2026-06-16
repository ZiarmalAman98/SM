<?php

namespace App\Filament\Resources\StudentClassResource\Pages;

use App\Filament\Resources\StudentClassResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class CreateStudentClass extends CreateRecord
{
    protected static string $resource = StudentClassResource::class;
    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->submit(form: null) // Skip submitting the form immediately
            ->requiresConfirmation(function () {
                $studentClass = $this->form->getState()['class_id'] ?? null;
                $studentId = $this->form->getState()['student_id'] ?? null;

                if (!$studentClass && !$studentId) {
                    return false; // If no farm is selected, do not show the modal
                }

                // Check if there is an active batch for the selected farm
                $hasActiveClass = DB::table('student_classes')
                    ->where('class_id', $studentClass)
                    ->where('student_id', $studentId)
                    ->where('status', 'active')
                    ->exists();

                return $hasActiveClass; // Show the modal only if there is an active batch
            })
            ->modalHeading('Confirm Class Creation')
            ->modalDescription('If you proceed, the active class for this student will be completed and a new class will be created.')
            ->modalSubmitActionLabel('Yes, Complete and Create')
            ->modalCancelActionLabel('No, Cancel')
            ->action(function () {
                $studentClass = $this->form->getState()['class_id'] ?? null;
                $studentId = $this->form->getState()['student_id'] ?? null;

                if (!$studentClass && !$studentId) {
                    Notification::make()
                        ->title('Error')
                        ->body('No sheet selected.')
                        ->danger()
                        ->send();
                    return;
                }

                // Check if there is an active batch for the selected farm
                $hasActiveClass = DB::table('student_classes')
                    ->where('class_id', $studentClass)
                    ->where('student_id', $studentId)
                    ->where('status', 'active')
                    ->exists();

                if ($hasActiveClass) {
                    // Complete active batches and create a new one
                    DB::transaction(function () {
                        $this->completeActiveBatches();
                        $this->callHook('beforeCreate'); // Call the beforeCreate hook
                        $this->record = $this->handleRecordCreation($this->form->getState());
                        $this->callHook('afterCreate'); // Call the afterCreate hook
                    });

                    Notification::make()
                        ->title('Class Created Successfully')
                        ->body('The active class was completed and the new class was created.')
                        ->success()
                        ->send();
                } else {
                    // If no active batch, proceed with form submission
                    $this->callHook('beforeCreate'); // Call the beforeCreate hook
                    $this->record = $this->handleRecordCreation($this->form->getState());
                    $this->callHook('afterCreate'); // Call the afterCreate hook

                    Notification::make()
                        ->title('Class Created Successfully')
                        ->body('A new class was created for the selected student.')
                        ->success()
                        ->send();
                }
            });
    }

    /**
     * Complete all active batches for the selected farm.
     */
    public function completeActiveBatches()
    {
        $studentClass = $this->form->getState()['class_id']; // Get the farm ID from the form state
        $studentId = $this->form->getState()['student_id'] ?? null;

        // Update all active batches to "Completed"
        DB::table('student_classes')
            ->where('class_id', $studentClass)
            ->where('student_id', $studentId)
            ->where('status', 'active')
            ->update(['status' => 'completed']);
    }
}
