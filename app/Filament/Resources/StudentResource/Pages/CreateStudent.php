<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        // Create the user record
        $user = static::getModel()::create($data);
    
        // Assign the 'super_admin' role to the student
        $user->assignRole('super_admin');
    
        return $user;
    }
    
}
