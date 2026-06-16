<?php

namespace App\Filament\Resources\AddBookResource\Pages;

use App\Filament\Resources\AddBookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ViewRecord;

class ViewAddBook extends ViewRecord
{
    protected static string $resource = AddBookResource::class;
       protected function getViewFormTitle(): string
    {
        return __('View Book');
    }
}
