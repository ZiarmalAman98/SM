<?php

namespace App\Filament\Resources\AddBookResource\Pages;

use App\Filament\Resources\AddBookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAddBooks extends ListRecords
{
    protected static string $resource = AddBookResource::class;

    public function getTitle(): string
    {
        return __('List');
    }
    protected function getHeaderActions(): array
    {
        return [
            \Filament\Pages\Actions\CreateAction::make(),
        ];
    }
}
