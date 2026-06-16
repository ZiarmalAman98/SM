<?php

namespace App\Filament\Resources\BorrowBookResource\Pages;

use App\Filament\Resources\BorrowBookResource;
use App\Models\AddBook;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBorrowBook extends EditRecord
{
    protected static string $resource = BorrowBookResource::class;

    protected function afterSave(): void
    {
        // ✅ Step 1: Check if `return_date` is set
        if ($this->record->return_date) {
            // ✅ Step 2: Mark the book as 'available' (state = true)
            AddBook::where('id', $this->record->addBook_id)->update(['state' => true]);
        }
    }
}
