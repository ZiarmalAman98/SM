<?php

namespace App\Filament\Resources\InventorySupplierResource\RelationManagers;

use App\Models\InventoryPurchase;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PurchasesRelationManager extends RelationManager
{
    protected static string $relationship = 'purchases';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('Supplier Ledger');
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('purchase_no')
            ->columns([
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label(__('Date'))
                    ->jalaliDate()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_no')
                    ->label(__('Bill No'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->getStateUsing(function (InventoryPurchase $record): string {
                        $isPaymentOnly = (float) $record->total_amount <= 0 && (float) $record->paid_amount > 0;

                        return $isPaymentOnly ? __('Payment') : __('Purchase');
                    })
                    ->badge()
                    ->color(fn(string $state): string => $state === __('Payment') ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Bill Amount'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label(__('Paid'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('balance')
                    ->label(__('Bill Balance'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->alignEnd()
                    ->color(fn($state) => (float) $state > 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->formatStateUsing(fn($state) => __($state))
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('Payment Method'))
                    ->formatStateUsing(fn($state) => $state ? __(str_replace('_', ' ', ucfirst($state))) : '-')
                    ->toggleable(),
            ])
            ->defaultSort('purchase_date', 'desc')
            ->headerActions([])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label(__('Print'))
                    ->icon('heroicon-o-printer')
                    ->url(fn(InventoryPurchase $record) => route('inventory-purchases.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make()
                    ->url(fn(InventoryPurchase $record) => \App\Filament\Resources\InventoryPurchaseResource::getUrl('view', ['record' => $record])),
            ])
            ->bulkActions([]);
    }
}
