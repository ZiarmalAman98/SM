<?php

namespace App\Filament\Resources\SendEmailResource\Pages;

use App\Filament\Resources\SendEmailResource;
use App\Mail\GenericSendEmail;
;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSendEmail extends CreateRecord
{
    protected static string $resource = SendEmailResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['from'] = env('MAIL_FROM_ADDRESS');
        return $data;
    }


    protected function afterCreate(): void
    {
        $record = $this->record; // The newly created SendEmail model

        $mailData = [
            'from' => $record->from,
            'to' => $record->to,
            'title' => $record->title,
            'description' => $record->description,
        ];

        Mail::send(new GenericSendEmail($mailData));

        Notification::make()
            ->title('Email sent successfully!')
            ->success()
            ->send();
    }
}
