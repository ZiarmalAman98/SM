<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventorySaleResource\Pages;
use App\Models\InventoryProduct;
use App\Models\InventorySale;
use App\Models\InventorySalePayment;
use App\Models\ParentGuardian;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;

class InventorySaleResource extends Resource
{
    protected static ?string $model = InventorySale::class;
    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string
    {
        return __('Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Sales');
    }

    public static function getModelLabel(): string
    {
        return __('Sale');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Sales');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Sale Information'))
                ->schema([
                    Forms\Components\TextInput::make('sale_no')
                        ->label(__('Sale Number'))
                        ->default(fn() => InventorySale::generateSaleNo())
                        ->unique(ignoreRecord: true)
                        ->required(),
                    Forms\Components\Select::make('sale_type')
                        ->label(__('Sale Type'))
                        ->options([
                            'regular' => __('Regular Sale'),
                            'admission' => __('New Student / Admission'),
                        ])
                        ->native(false)
                        ->default('regular')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, Forms\Set $set): void {
                            $set('payment_destination', $state === 'admission' ? 'unpaid' : 'pay_here');
                        }),
                    Forms\Components\Select::make('payment_destination')
                        ->label(__('Payment Destination'))
                        ->options([
                            'pay_here' => __('Pay Here'),
                            'parent_invoice' => __('Add to Parent Invoice Now'),
                            'unpaid' => __('Unpaid in Inventory'),
                        ])
                        ->native(false)
                        ->default('pay_here')
                        ->required()
                        ->live(),
                    Forms\Components\DatePicker::make('sale_date')
                        ->label(__('Sale Date'))
                        ->jalali()
                        ->locale('fa')
                        ->default(now())
                        ->required(),
                    Forms\Components\Select::make('student_id')
                        ->label(__('Student'))
                        ->options(fn() => User::where('type', 'student')->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                    Forms\Components\Select::make('parent_guardian_id')
                        ->label(__('Parent / Family'))
                        ->options(fn() => self::parentOptions())
                        ->searchable()
                        ->preload()
                        ->required(fn(Forms\Get $get) => in_array($get('payment_destination'), ['parent_invoice', 'unpaid'], true)),
                    Forms\Components\TextInput::make('customer_name')
                        ->label(__('Walk-in Customer'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('discount_amount')
                        ->label(__('Invoice Discount'))
                        ->prefix('AFN ')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Textarea::make('notes')
                        ->label(__('Notes'))
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(3),
            Forms\Components\Section::make(__('Sale Items'))
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
                                    $set('unit_price', $product?->sale_price ?? 0);
                                    $set('total_amount', (float) ($product?->sale_price ?? 0));
                                }),
                            Forms\Components\TextInput::make('quantity')
                                ->label(__('Quantity'))
                                ->numeric()
                                ->default(1)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) => self::setSaleItemTotal($set, $get, (float) $state))
                                ->minValue(0.01)
                                ->required(),
                            Forms\Components\TextInput::make('unit_price')
                                ->label(__('Unit Price'))
                                ->prefix('AFN ')
                                ->numeric()
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) => self::setSaleItemTotal($set, $get, null, (float) $state))
                                ->required(),
                            Forms\Components\TextInput::make('discount_percent')
                                ->label(__('Discount'))
                                ->suffix('%')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, Forms\Set $set, Forms\Get $get) => self::setSaleItemTotal($set, $get, null, null, (float) $state)),
                            Forms\Components\TextInput::make('total_amount')
                                ->label(__('Total'))
                                ->prefix('AFN ')
                                ->numeric()
                                ->default(0)
                                ->disabled()
                                ->dehydrated(false),
                        ])
                        ->columns(5)
                        ->defaultItems(1)
                        ->addActionLabel(__('Add Item'))
                        ->reorderable(false)
                        ->required()
                        ->columnSpanFull(),
                ]),
            Forms\Components\Section::make(__('Payments'))
                ->schema([
                    Forms\Components\Repeater::make('payments')
                        ->relationship()
                        ->schema([
                            Forms\Components\TextInput::make('receipt_number')
                                ->label(__('Receipt Number'))
                                ->default(fn() => InventorySalePayment::generateReceiptNumber())
                                ->required(),
                            Forms\Components\TextInput::make('amount')
                                ->label(__('Amount'))
                                ->prefix('AFN ')
                                ->numeric()
                                ->minValue(0.01)
                                ->required(),
                            Forms\Components\DatePicker::make('payment_date')
                                ->label(__('Payment Date'))
                                ->jalali()
                                ->locale('fa')
                                ->default(now())
                                ->required(),
                            Forms\Components\Select::make('payment_method')
                                ->label(__('Method'))
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
                        ])
                        ->columns(4)
                        ->addActionLabel(__('Add Payment'))
                        ->reorderable(false)
                        ->columnSpanFull(),
                ])
                ->visible(fn(Forms\Get $get) => $get('payment_destination') === 'pay_here'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['student', 'parentGuardian.user']))
            ->columns([
                Tables\Columns\TextColumn::make('sale_no')->label(__('Sale #'))->searchable()->sortable()->badge(),
                Tables\Columns\TextColumn::make('student.name')->label(__('Student'))->searchable()->sortable()->default('-'),
                Tables\Columns\TextColumn::make('parentGuardian.family_code')->label(__('Family'))->searchable()->default('-'),
                Tables\Columns\TextColumn::make('customer_name')->label(__('Customer'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('sale_date')
                    ->label(__('Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->label(__('Total'))->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')->sortable(),
                Tables\Columns\TextColumn::make('payment_destination')
                    ->label(__('Payment'))
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'parent_invoice' => __('Parent Invoice'),
                        'unpaid' => __('Inventory Unpaid'),
                        default => __('Pay Here'),
                    }),
                Tables\Columns\TextColumn::make('paid_amount')->label(__('Paid'))->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')->sortable(),
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
                    ->url(fn(InventorySale $record) => route('inventory-sales.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('sale_date', 'desc');
    }

    private static function parentOptions(): array
    {
        return ParentGuardian::with('user')
            ->orderBy('family_code')
            ->get()
            ->mapWithKeys(function (ParentGuardian $parentGuardian) {
                $name = trim(($parentGuardian->user?->name ?? '') . ' ' . ($parentGuardian->user?->last_name ?? ''));

                return [
                    $parentGuardian->id => "{$parentGuardian->family_code} - {$name}",
                ];
            })
            ->all();
    }

    private static function setSaleItemTotal(
        Forms\Set $set,
        Forms\Get $get,
        ?float $quantity = null,
        ?float $unitPrice = null,
        ?float $discountPercent = null,
    ): void {
        $quantity ??= (float) ($get('quantity') ?: 0);
        $unitPrice ??= (float) ($get('unit_price') ?: 0);
        $discountPercent ??= (float) ($get('discount_percent') ?: 0);

        $grossTotal = max(0, $quantity * $unitPrice);
        $discountAmount = $grossTotal * (min(100, max(0, $discountPercent)) / 100);

        $set('total_amount', max(0, $grossTotal - $discountAmount));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventorySales::route('/'),
            'create' => Pages\CreateInventorySale::route('/create'),
            'view' => Pages\ViewInventorySale::route('/{record}'),
            'edit' => Pages\EditInventorySale::route('/{record}/edit'),
        ];
    }
}
