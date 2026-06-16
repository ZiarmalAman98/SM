<?php

namespace App\Filament\Teacher\Resources\AssignmentSubmissionResource\Pages;

use App\Filament\Teacher\Resources\AssignmentSubmissionResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ViewRecord;

class ViewAssignmentSubmission extends ViewRecord
{
    protected static string $resource = AssignmentSubmissionResource::class;

    // Optional: belt & suspenders security
    protected function authorizeAccess(): void
    {
        $record = $this->getRecord();
        abort_unless(
            $record->assignment && $record->assignment->teacher_id === Filament::auth()->id(),
            403
        );
    }
}
