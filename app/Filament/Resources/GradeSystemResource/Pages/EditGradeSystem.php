<?php

namespace App\Filament\Resources\GradeSystemResource\Pages;

use App\Filament\Resources\GradeSystemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGradeSystem extends EditRecord
{
    protected static string $resource = GradeSystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
