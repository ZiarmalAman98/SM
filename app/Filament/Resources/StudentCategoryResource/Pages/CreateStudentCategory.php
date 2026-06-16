<?php

namespace App\Filament\Resources\StudentCategoryResource\Pages;

use App\Filament\Resources\StudentCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStudentCategory extends CreateRecord
{
    protected static string $resource = StudentCategoryResource::class;
}
