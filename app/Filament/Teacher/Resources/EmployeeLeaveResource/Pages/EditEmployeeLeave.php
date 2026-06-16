<?php

namespace App\Filament\Teacher\Resources\EmployeeLeaveResource\Pages;

use App\Filament\Teacher\Resources\EmployeeLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployeeLeave extends EditRecord
{
    protected static string $resource = EmployeeLeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
