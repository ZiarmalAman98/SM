<?php

namespace App\Filament\Resources\InventorySupplierResource\Pages;

use App\Filament\Resources\InventorySupplierResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInventorySupplier extends CreateRecord
{
    protected static string $resource = InventorySupplierResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['opening_balance'] = filled($data['opening_balance'] ?? null)
            ? $data['opening_balance']
            : 0;

        $data['opening_balance_type'] ??= 'payable';

        return $data;
    }
}
