<?php

namespace App\Filament\Widgets;

use App\Models\ParentInvoicePayment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentPayments extends TableWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static bool $isLazy = true;

    protected function getTableHeading(): ?string
    {
        return __('Recent Student Payments');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ParentInvoicePayment::query()->latest('payment_date')->latest('id'))
            ->columns([
                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label(__('Invoice'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('AFN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label(__('Payment Date'))
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('Method'))
                    ->badge(),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}
