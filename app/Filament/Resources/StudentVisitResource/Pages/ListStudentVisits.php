<?php

namespace App\Filament\Resources\StudentVisitResource\Pages;

use App\Filament\Resources\StudentVisitResource;
use App\Filament\Resources\StudentVisitResource\Widgets\StudentVisitStatsChart;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentVisits extends ListRecords
{
    protected static string $resource = StudentVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array {
        return [
            StudentVisitStatsChart::class,
        ];
    }
}
