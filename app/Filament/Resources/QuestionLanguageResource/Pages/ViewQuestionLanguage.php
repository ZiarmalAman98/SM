<?php

namespace App\Filament\Resources\QuestionLanguageResource\Pages;

use App\Filament\Resources\QuestionLanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionLanguage extends ViewRecord
{
    protected static string $resource = QuestionLanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
