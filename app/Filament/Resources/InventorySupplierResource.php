<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventorySupplierResource\Pages;
use App\Filament\Resources\InventorySupplierResource\RelationManagers;
use App\Models\InventoryPurchase;
use App\Models\InventorySupplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InventorySupplierResource extends Resource
{
    protected static ?string $model = InventorySupplier::class;
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Suppliers');
    }

    public static function getModelLabel(): string
    {
        return __('Supplier');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Suppliers');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Supplier Information'))
                ->schema([
                    Forms\Components\Hidden::make('supplier_code')
                        ->default(fn() => InventorySupplier::generateSupplierCode())
                        ->dehydrated(fn($state) => filled($state)),
                    Forms\Components\TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->live(onBlur: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label(__('Email'))
                        ->email()
                        ->live(onBlur: true)
                        ->maxLength(255),
                    Forms\Components\Toggle::make('is_active')
                        ->label(__('Status'))
                        ->onColor('success')
                        ->offColor('danger')
                        ->default(true),
                    Forms\Components\TextInput::make('phone')
                        ->label(__('Phone'))
                        ->tel()
                        ->live(onBlur: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('opening_balance')
                        ->label(__('Balance'))
                        ->prefix('AFN ')
                        ->numeric()
                        ->live(onBlur: true)
                        ->placeholder('0')
                        ->default(0)
                        ->dehydrateStateUsing(fn($state) => filled($state) ? $state : 0)
                        ->visible(fn(?InventorySupplier $record) => blank($record)),
                    Forms\Components\Placeholder::make('balance_display')
                        ->label(__('Balance'))
                        ->content(fn(?InventorySupplier $record) => $record
                            ? number_format(abs((float) $record->net_balance), 2) . ' AFN - ' . $record->balance_status
                            : '0.00 AFN')
                        ->visible(fn(?InventorySupplier $record) => filled($record)),
                    Forms\Components\Hidden::make('opening_balance_type')
                        ->default('payable'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->with('latestPaidPurchase')
                ->withSum([
                    'purchases as purchases_total_amount' => fn(Builder $query) => $query->where('status', '!=', 'cancelled'),
                ], 'total_amount')
                ->withSum([
                    'purchases as purchases_paid_amount' => fn(Builder $query) => $query->where('status', '!=', 'cancelled'),
                ], 'paid_amount'))
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label(__('Email'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('phone')->label(__('Phone'))->searchable(),
                Tables\Columns\TextColumn::make('purchases_total')
                    ->label(__('Purchases'))
                    ->getStateUsing(fn(InventorySupplier $record) => $record->purchases_total)
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('purchases_paid')
                    ->label(__('Paid'))
                    ->getStateUsing(fn(InventorySupplier $record) => $record->purchases_paid)
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('net_balance')
                    ->label(__('Balance'))
                    ->getStateUsing(fn(InventorySupplier $record) => $record->net_balance)
                    ->formatStateUsing(fn($state) => number_format(abs((float) $state), 2) . ' AFN')
                    ->color(fn($state) => match (true) {
                        (float) $state > 0 => 'danger',
                        (float) $state < 0 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('balance_status')
                    ->label(__('Status'))
                    ->getStateUsing(fn(InventorySupplier $record) => $record->balance_status)
                    ->badge()
                    ->color(fn($state, InventorySupplier $record) => match (true) {
                        $record->net_balance > 0 => 'danger',
                        $record->net_balance < 0 => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('is_active')
                    ->label(__('Active Status'))
                    ->formatStateUsing(fn(bool $state): string => $state ? __('Active') : __('Inactive'))
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created At'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label(__('Active')),
                Tables\Filters\SelectFilter::make('opening_balance_type')
                    ->label(__('Balance Type'))
                    ->options([
                        'payable' => __('Payable'),
                        'receivable' => __('Receivable'),
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('printLedger')
                    ->label(__('Print Ledger'))
                    ->icon('heroicon-o-printer')
                    ->url(fn(InventorySupplier $record) => route('inventory-suppliers.ledger', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('addPayment')
                    ->label(__('Add Payment'))
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('paid_amount')
                            ->label(__('Paid Amount'))
                            ->prefix('AFN ')
                            ->numeric()
                            ->minValue(0.01)
                            ->required(),
                        Forms\Components\DatePicker::make('purchase_date')
                            ->label(__('Payment Date'))
                            ->jalali()
                            ->locale('fa')
                            ->default(now())
                            ->required(),
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
                    ->modalSubmitActionLabel(__('Save Payment'))
                    ->action(function (InventorySupplier $record, array $data): void {
                        InventoryPurchase::create([
                            'purchase_no' => InventoryPurchase::generatePurchaseNo(),
                            'inventory_supplier_id' => $record->id,
                            'purchase_date' => $data['purchase_date'],
                            'subtotal' => 0,
                            'discount_percent' => 0,
                            'discount_amount' => 0,
                            'total_amount' => 0,
                            'paid_amount' => $data['paid_amount'],
                            'payment_method' => $data['payment_method'],
                            'balance' => 0,
                            'status' => 'paid',
                            'notes' => $data['notes'] ?? null,
                        ]);

                        Notification::make()
                            ->title(__('Payment added successfully'))
                            ->success()
                            ->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PurchasesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventorySuppliers::route('/'),
            'create' => Pages\CreateInventorySupplier::route('/create'),
            'view' => Pages\ViewInventorySupplier::route('/{record}'),
            'edit' => Pages\EditInventorySupplier::route('/{record}/edit'),
        ];
    }
}
