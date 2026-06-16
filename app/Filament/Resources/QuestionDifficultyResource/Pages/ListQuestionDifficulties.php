<?php

namespace App\Filament\Resources\QuestionDifficultyResource\Pages;

use App\Filament\Resources\QuestionDifficultyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestionDifficulties extends ListRecords
{
    protected static string $resource = QuestionDifficultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
