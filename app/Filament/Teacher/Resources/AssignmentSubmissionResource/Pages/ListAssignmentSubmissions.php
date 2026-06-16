<?php

namespace App\Filament\Teacher\Resources\AssignmentSubmissionResource\Pages;

use App\Filament\Teacher\Resources\AssignmentSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssignmentSubmissions extends ListRecords
{
    protected static string $resource = AssignmentSubmissionResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
    protected function canCreate(): bool
    {
        return false; // teachers usually don't create submissions
    }
}
