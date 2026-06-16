<?php

namespace App\Filament\Student\Resources\HomeworkRequestResource\Pages;

use App\Filament\Student\Resources\HomeworkRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeworkRequests extends ListRecords
{
    protected static string $resource = HomeworkRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
