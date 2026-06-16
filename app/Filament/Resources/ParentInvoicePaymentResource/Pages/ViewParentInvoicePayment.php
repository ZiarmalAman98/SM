<?php

namespace App\Filament\Resources\ParentInvoicePaymentResource\Pages;

use App\Filament\Resources\ParentInvoicePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewParentInvoicePayment extends ViewRecord
{
    protected static string $resource = ParentInvoicePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label(__('Print Receipt'))
                ->icon('heroicon-o-printer')
                ->url(fn() => route('parent-invoice-payments.print', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
