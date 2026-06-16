<?php

namespace App\Filament\Resources\EmployeeAllocationResource\Pages;

use App\Filament\Resources\EmployeeAllocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmployeeAllocation extends ViewRecord
{
    protected static string $resource = EmployeeAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
