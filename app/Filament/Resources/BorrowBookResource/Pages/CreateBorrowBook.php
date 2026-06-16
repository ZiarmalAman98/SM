<?php

namespace App\Filament\Resources\BorrowBookResource\Pages;

use App\Filament\Resources\BorrowBookResource;
use App\Models\AddBook;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBorrowBook extends CreateRecord
{
    protected static string $resource = BorrowBookResource::class;

    protected function afterCreate(): void
    {
        // ✅ Update book's state to 'false' after borrowing
        AddBook::where('id', $this->record->addBook_id)->update(['state' => false]);

        // ✅ Optional: Add additional logic (e.g., notifications)
        // Notification::send($this->record->user, new BookBorrowedNotification($this->record));
    }
}
