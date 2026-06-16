<?php

namespace App\Filament\Resources\ParentInvoiceResource\Pages;

use App\Filament\Resources\ParentInvoiceResource;
use App\Services\ParentInvoiceBuilder;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateParentInvoice extends CreateRecord
{
    protected static string $resource = ParentInvoiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        unset($data['preview_items'], $data['items'], $data['previous_balance'], $data['subtotal'], $data['total_amount'], $data['balance']);

        return app(ParentInvoiceBuilder::class)->create($data);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', [
            'record' => $this->record,
        ]);
    }
}
