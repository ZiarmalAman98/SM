<?php

namespace App\Filament\Resources\FeeGroupResource\Pages;

use App\Filament\Resources\FeeGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeeGroup extends EditRecord
{
    protected static string $resource = FeeGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
