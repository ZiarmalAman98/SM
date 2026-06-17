<?php

namespace App\Filament\Pages;

use App\Models\InventoryProduct;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventoryStockReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.inventory-stock-report';

    public static function getNavigationGroup(): string
    {
        return __('Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Stock');
    }

    public function getTitle(): string
    {
        return __('Stock');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => InventoryProduct::query()->with('category'))
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label(__('SKU'))
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Product'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('Category'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label(__('Available Stock'))
                    ->getStateUsing(fn(InventoryProduct $record) => $record->current_stock)
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label(__('Purchase Price'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->label(__('Sale Price'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('inventory_category_id')
                    ->label(__('Category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('name')
            ->paginated([10, 25, 50, 100]);
    }
}
