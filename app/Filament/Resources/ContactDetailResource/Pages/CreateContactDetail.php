<?php

namespace App\Filament\Resources\ContactDetailResource\Pages;

use App\Filament\Resources\ContactDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateContactDetail extends CreateRecord
{
    protected static string $resource = ContactDetailResource::class;
}
