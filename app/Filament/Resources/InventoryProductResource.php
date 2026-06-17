<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryProductResource\Pages;
use App\Models\InventoryProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryProductResource extends Resource
{
    protected static ?string $model = InventoryProduct::class;
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return __('Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Products');
    }

    public static function getModelLabel(): string
    {
        return __('Product');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Products');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Product Information'))
                ->schema([
                    Forms\Components\TextInput::make('sku')
                        ->label(__('SKU'))
                        ->default(fn() => InventoryProduct::generateSku())
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('inventory_category_id')
                        ->label(__('Category'))
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('unit')
                        ->label(__('Unit'))
                        ->default('piece')
                        ->required()
                        ->maxLength(30),
                    Forms\Components\TextInput::make('purchase_price')
                        ->label(__('Purchase Price'))
                        ->prefix('AFN ')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Forms\Components\TextInput::make('sale_price')
                        ->label(__('Sale Price'))
                        ->prefix('AFN ')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Active'))
                        ->default(true),
                    Forms\Components\Textarea::make('description')
                        ->label(__('Description'))
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')->label(__('SKU'))->searchable()->sortable()->badge(),
                Tables\Columns\TextColumn::make('name')->label(__('Name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category.name')->label(__('Category'))->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label(__('Stock'))
                    ->getStateUsing(fn(InventoryProduct $record) => $record->current_stock)
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2))
                    ->color(fn(InventoryProduct $record) => $record->current_stock <= (float) $record->min_stock ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label(__('Purchase'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->label(__('Sale'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label(__('Active'))->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('inventory_category_id')
                    ->label(__('Category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_active')->label(__('Active')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('deactivate')
                    ->label(__('Deactivate'))
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn(InventoryProduct $record): bool => $record->is_active && $record->hasInventoryHistory())
                    ->action(fn(InventoryProduct $record) => $record->update(['is_active' => false])),
                Tables\Actions\Action::make('activate')
                    ->label(__('Activate'))
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn(InventoryProduct $record): bool => ! $record->is_active)
                    ->action(fn(InventoryProduct $record) => $record->update(['is_active' => true])),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn(InventoryProduct $record): bool => ! $record->hasInventoryHistory()),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('delete_unused')
                    ->label(__('Delete Selected'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($records): void {
                        $deleted = 0;
                        $protected = 0;

                        foreach ($records as $record) {
                            if ($record->hasInventoryHistory()) {
                                $protected++;
                                continue;
                            }

                            $record->delete();
                            $deleted++;
                        }

                        if ($deleted > 0) {
                            Notification::make()
                                ->title(__('Deleted :count product(s).', ['count' => $deleted]))
                                ->success()
                                ->send();
                        }

                        if ($protected > 0) {
                            Notification::make()
                                ->title(__('Some products cannot be deleted.'))
                                ->body(__('They are used in purchases, sales, or stock history. Deactivate them instead.'))
                                ->warning()
                                ->send();
                        }
                    }),
                Tables\Actions\BulkAction::make('deactivate')
                    ->label(__('Deactivate Selected'))
                    ->icon('heroicon-o-eye-slash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn($records) => $records->each->update(['is_active' => false])),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryProducts::route('/'),
            'create' => Pages\CreateInventoryProduct::route('/create'),
            'view' => Pages\ViewInventoryProduct::route('/{record}'),
            'edit' => Pages\EditInventoryProduct::route('/{record}/edit'),
        ];
    }
}
