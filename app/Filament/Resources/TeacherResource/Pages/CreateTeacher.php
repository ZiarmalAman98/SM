<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        // Create the user record
        $user = static::getModel()::create($data);
    
        // Assign the 'super_admin' role to the teacher
        $user->assignRole('super_admin');
    
        return $user;
    }
}
