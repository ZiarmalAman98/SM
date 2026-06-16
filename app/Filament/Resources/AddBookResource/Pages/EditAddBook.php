<?php

namespace App\Filament\Resources\AddBookResource\Pages;

use App\Filament\Resources\AddBookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAddBook extends EditRecord
{
    protected static string $resource = AddBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
     protected function getEditFormTitle(): string
    {
        return __('Edit Book');
    }
}
