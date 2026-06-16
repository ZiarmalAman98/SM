<?php

namespace App\Filament\Resources\BorrowBookResource\Pages;

use App\Filament\Resources\BorrowBookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBorrowBooks extends ListRecords
{
    protected static string $resource = BorrowBookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
