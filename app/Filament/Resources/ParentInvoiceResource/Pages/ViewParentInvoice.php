<?php

namespace App\Filament\Resources\ParentInvoiceResource\Pages;

use App\Filament\Resources\ParentInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewParentInvoice extends ViewRecord
{
    protected static string $resource = ParentInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add_payment')
                ->label(__('Add Payment'))
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->url(fn() => \App\Filament\Resources\ParentInvoicePaymentResource::getUrl('create', [
                    'parent_invoice_id' => $this->record->id,
                ]))
                ->visible(fn() => $this->record->balance > 0 && $this->record->status !== 'cancelled'),
            Actions\Action::make('print')
                ->label(__('Print Invoice'))
                ->icon('heroicon-o-printer')
                ->url(fn() => route('parent-invoices.print', $this->record))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
