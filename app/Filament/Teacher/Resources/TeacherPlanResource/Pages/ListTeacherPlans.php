<?php

namespace App\Filament\Teacher\Resources\TeacherPlanResource\Pages;

use App\Filament\Teacher\Resources\TeacherPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeacherPlans extends ListRecords
{
    protected static string $resource = TeacherPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
