<?php

namespace App\Filament\Resources\AddBookResource\Pages;

use App\Filament\Resources\AddBookResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAddBook extends CreateRecord
{
    protected static string $resource = AddBookResource::class;
    
    protected function getCreateFormTitle(): string
    {
        return __('Create Book');
    }
    
}
