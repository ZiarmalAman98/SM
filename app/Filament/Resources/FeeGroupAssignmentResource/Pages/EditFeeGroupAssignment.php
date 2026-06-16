<?php

namespace App\Filament\Resources\FeeGroupAssignmentResource\Pages;

use App\Filament\Resources\FeeGroupAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeeGroupAssignment extends EditRecord
{
    protected static string $resource = FeeGroupAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
