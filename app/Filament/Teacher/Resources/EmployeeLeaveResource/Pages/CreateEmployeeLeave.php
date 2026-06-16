<?php

namespace App\Filament\Teacher\Resources\EmployeeLeaveResource\Pages;

use App\Filament\Teacher\Resources\EmployeeLeaveResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeLeave extends CreateRecord
{
    protected static string $resource = EmployeeLeaveResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
}
