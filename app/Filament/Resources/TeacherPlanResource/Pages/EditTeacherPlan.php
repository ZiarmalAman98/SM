<?php

namespace App\Filament\Resources\TeacherPlanResource\Pages;

use App\Filament\Resources\TeacherPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeacherPlan extends EditRecord
{
    protected static string $resource = TeacherPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
