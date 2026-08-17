<?php

namespace App\Filament\Resources\StudentVisitResource\Pages;

use App\Filament\Resources\StudentVisitResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudentVisit extends ViewRecord
{
    protected static string $resource = StudentVisitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('Print'))
                ->icon('heroicon-o-printer')
                ->url(fn() => route('visitor-logs.print', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
        ];
    }
}
