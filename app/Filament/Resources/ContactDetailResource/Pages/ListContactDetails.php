<?php

namespace App\Filament\Resources\ContactDetailResource\Pages;

use App\Filament\Resources\ContactDetailResource;
use App\Models\ContactDetail;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListContactDetails extends ListRecords
{
    protected static string $resource = ContactDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print_all')
                ->label(__('Print All'))
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->action(function (): void {
                    if (ContactDetail::query()->doesntExist()) {
                        Notification::make()
                            ->title(__('No phone numbers found'))
                            ->warning()
                            ->send();

                        return;
                    }

                    $this->js('window.open(' . json_encode(route('contact-details.print-all')) . ', "_blank")');
                }),
        ];
    }
}
