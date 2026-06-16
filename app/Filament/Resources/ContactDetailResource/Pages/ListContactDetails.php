<?php

namespace App\Filament\Resources\ContactDetailResource\Pages;

use App\Filament\Resources\ContactDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactDetails extends ListRecords
{
    protected static string $resource = ContactDetailResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
}
