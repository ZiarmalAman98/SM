<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryPurchaseResource\Pages;
use App\Models\InventoryProduct;
use App\Models\InventoryPurchase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;

class InventoryPurchaseResource extends Resource
{
    protected static ?string $model = InventoryPurchase::class;
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string
    {
        return __('Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Purchases');
    }

    public static function getModelLabel(): string
    {
        return __('Purchase');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Purchases');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Purchase Information'))
                ->schema([
                    Forms\Components\TextInput::make('purchase_no')
                        ->label(__('Purchase Number'))
                        ->default(fn() => InventoryPurchase::generatePurchaseNo())
                        ->unique(ignoreRecord: true)
                        ->required(),
                    Forms\Components\Select::make('inventory_supplier_id')
                        ->label(__('Supplier'))
                        ->relationship('supplier', 'name')
                        ->searchable()
                        ->preload()
                        ->helperText(__('Paid amount on this purchase will be included in this supplier balance.')),
                    Forms\Components\DatePicker::make('purchase_date')
                        ->label(__('Purchase Date'))
                        ->jalali()
                        ->locale('fa')
                        ->default(now())
                        ->required(),
                    Forms\Components\TextInput::make('discount_percent')
                        ->label(__('Discount'))
                        ->suffix('%')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->default(0),
                    Forms\Components\TextInput::make('paid_amount')
                        ->label(__('Paid Amount'))
                        ->prefix('AFN ')
                        ->numeric()
                        ->default(0)
                        ->helperText(__('This payment is recorded against the selected supplier.')),
                    Forms\Components\Select::make('payment_method')
                        ->label(__('Payment Method'))
                        ->options([
                            'cash' => __('Cash'),
                            'bank' => __('Bank'),
                            'mobile_money' => __('Mobile Money'),
                            'card' => __('Card'),
                            'other' => __('Other'),
                        ])
                        ->native(false)
                        ->default('cash')
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label(__('Notes'))
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(3),
            Forms\Components\Section::make(__('Purchase Items'))
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('inventory_product_id')
                                ->label(__('Product'))
                                ->options(fn() => InventoryProduct::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set): void {
                                    $product = InventoryProduct::find($state);
                                    $set('unit_price', $product?->purchase_price ?? 0);
                                    $set('total_amount', (float) ($product?->purchase_price ?? 0));
                                }),
                            Forms\Components\TextInput::make('quantity')
                                ->label(__('Quantity'))
                                ->numeric()
                                ->default(1)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) => $set(
                                    'total_amount',
                                    max(0, (float) $state * (float) ($get('unit_price') ?: 0)),
                                ))
                                ->minValue(0.01)
                                ->required(),
                            Forms\Components\TextInput::make('unit_price')
                                ->label(__('Unit Price'))
                                ->prefix('AFN ')
                                ->numeric()
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) => $set(
                                    'total_amount',
                                    max(0, (float) ($get('quantity') ?: 0) * (float) $state),
                                ))
                                ->required(),
                            Forms\Components\TextInput::make('total_amount')
                                ->label(__('Total'))
                                ->prefix('AFN ')
                                ->numeric()
                                ->default(0)
                                ->disabled()
                                ->dehydrated(false),
                        ])
                        ->columns(4)
                        ->defaultItems(1)
                        ->addActionLabel(__('Add Item'))
                        ->reorderable(false)
                        ->required()
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make(__('Totals'))
                ->schema([
                    Forms\Components\TextInput::make('subtotal')->label(__('Subtotal'))->prefix('AFN ')->disabled()->dehydrated(false),
                    Forms\Components\Placeholder::make('discount_percent_display')
                        ->label(__('Discount'))
                        ->content(fn(?InventoryPurchase $record) => number_format((float) ($record?->discount_percent ?? 0), 2) . '%'),
                    Forms\Components\TextInput::make('discount_amount')->label(__('Discount Amount'))->prefix('AFN ')->disabled()->dehydrated(false),
                    Forms\Components\TextInput::make('total_amount')->label(__('Total'))->prefix('AFN ')->disabled()->dehydrated(false),
                    Forms\Components\TextInput::make('balance')->label(__('Balance'))->prefix('AFN ')->disabled()->dehydrated(false),
                    Forms\Components\TextInput::make('status')->label(__('Status'))->disabled()->dehydrated(false),
                ])
                ->columns(3)
                ->visible(fn(?InventoryPurchase $record) => filled($record)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with('supplier'))
            ->columns([
                Tables\Columns\TextColumn::make('purchase_no')->label(__('Purchase #'))->searchable()->sortable()->badge(),
                Tables\Columns\TextColumn::make('supplier.name')->label(__('Supplier'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label(__('Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->label(__('Total'))->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')->label(__('Paid'))->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('Method'))
                    ->badge()
                    ->formatStateUsing(fn($state) => __(ucwords(str_replace('_', ' ', (string) ($state ?: 'cash')))))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('balance')->label(__('Balance'))->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')->sortable(),
                Tables\Columns\TextColumn::make('status')->label(__('Status'))->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'unpaid' => __('Unpaid'),
                        'partial' => __('Partial'),
                        'paid' => __('Paid'),
                        'cancelled' => __('Cancelled'),
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label(__('Print'))
                    ->icon('heroicon-o-printer')
                    ->url(fn(InventoryPurchase $record) => route('inventory-purchases.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('purchase_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryPurchases::route('/'),
            'create' => Pages\CreateInventoryPurchase::route('/create'),
            'view' => Pages\ViewInventoryPurchase::route('/{record}'),
            'edit' => Pages\EditInventoryPurchase::route('/{record}/edit'),
        ];
    }
}
