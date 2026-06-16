<?php

namespace App\Filament\Resources\EmployeeAllocationResource\Pages;

use App\Filament\Resources\EmployeeAllocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployeeAllocations extends ListRecords
{
    protected static string $resource = EmployeeAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
