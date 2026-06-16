<?php

namespace App\Filament\Resources\EvaluationResponseResource\Pages;

use App\Filament\Resources\EvaluationResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvaluationResponse extends EditRecord
{
    protected static string $resource = EvaluationResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
