<?php

namespace App\Filament\Resources\QuestionLanguageResource\Pages;

use App\Filament\Resources\QuestionLanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestionLanguages extends ListRecords
{
    protected static string $resource = QuestionLanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
