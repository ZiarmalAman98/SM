<?php

namespace App\Filament\Resources\QuestionDifficultyResource\Pages;

use App\Filament\Resources\QuestionDifficultyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionDifficulty extends ViewRecord
{
    protected static string $resource = QuestionDifficultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
