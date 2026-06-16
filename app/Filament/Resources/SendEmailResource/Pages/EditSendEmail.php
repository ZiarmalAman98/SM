<?php

namespace App\Filament\Resources\SendEmailResource\Pages;

use App\Filament\Resources\SendEmailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSendEmail extends EditRecord
{
    protected static string $resource = SendEmailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
