<?php

namespace App\Filament\Resources\BorrowBookResource\Pages;

use App\Filament\Resources\BorrowBookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ViewRecord;

class ViewBorrowBook extends ViewRecord
{
    protected static string $resource = BorrowBookResource::class;
}
