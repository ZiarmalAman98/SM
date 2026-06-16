<?php

namespace App\Filament\Resources\StudentCategoryResource\Pages;

use App\Filament\Resources\StudentCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentCategories extends ListRecords
{
    protected static string $resource = StudentCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
