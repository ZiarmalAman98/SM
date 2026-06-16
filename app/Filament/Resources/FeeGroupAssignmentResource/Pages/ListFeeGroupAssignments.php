<?php

namespace App\Filament\Resources\FeeGroupAssignmentResource\Pages;

use App\Filament\Resources\FeeGroupAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeGroupAssignments extends ListRecords
{
    protected static string $resource = FeeGroupAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
