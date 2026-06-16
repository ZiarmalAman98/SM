<?php

namespace App\Filament\Resources\EmployeeAllocationResource\Pages;

use App\Filament\Resources\EmployeeAllocationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeAllocation extends EditRecord
{
    protected static string $resource = EmployeeAllocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
