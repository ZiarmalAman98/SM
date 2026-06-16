<?php

namespace App\Filament\Resources\EvaluationQuestionResource\Pages;

use App\Filament\Resources\EvaluationQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvaluationQuestion extends EditRecord
{
    protected static string $resource = EvaluationQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
