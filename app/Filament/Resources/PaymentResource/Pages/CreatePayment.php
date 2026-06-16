<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getCreatedNotificationRedirectUrl(): string
    {
        // Redirect to the print route for the newly created payment
        return route('print.payment', $this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            // You can keep any existing actions here
        ];
    }
}