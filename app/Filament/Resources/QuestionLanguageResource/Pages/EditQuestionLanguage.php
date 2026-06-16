<?php

namespace App\Filament\Resources\QuestionLanguageResource\Pages;

use App\Filament\Resources\QuestionLanguageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuestionLanguage extends EditRecord
{
    protected static string $resource = QuestionLanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
