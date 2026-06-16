<?php

namespace App\Filament\Resources\QuestionDifficultyResource\Pages;

use App\Filament\Resources\QuestionDifficultyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestionDifficulty extends EditRecord
{
    protected static string $resource = QuestionDifficultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
