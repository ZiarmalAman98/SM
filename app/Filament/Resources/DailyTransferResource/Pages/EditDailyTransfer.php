<?php

namespace App\Filament\Resources\DailyTransferResource\Pages;

use App\Filament\Resources\DailyTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailyTransfer extends EditRecord
{
    protected static string $resource = DailyTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
