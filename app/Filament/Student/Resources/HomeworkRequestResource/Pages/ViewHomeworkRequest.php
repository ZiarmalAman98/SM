<?php

namespace App\Filament\Student\Resources\HomeworkRequestResource\Pages;

use App\Filament\Student\Resources\HomeworkRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHomeworkRequest extends ViewRecord
{
    protected static string $resource = HomeworkRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
