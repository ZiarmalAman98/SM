<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryStockMovementResource\Pages;
use App\Models\InventoryStockMovement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;

class InventoryStockMovementResource extends Resource
{
    protected static ?string $model = InventoryStockMovement::class;
    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): string
    {
        return __('Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Stock History');
    }

    public static function getModelLabel(): string
    {
        return __('Stock History');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Stock History');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Manual Adjustment'))
                ->schema([
                    Forms\Components\Select::make('inventory_product_id')
                        ->label(__('Product'))
                        ->relationship('product', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('type')
                        ->label(__('Type'))
                        ->options([
                            'adjustment_in' => __('Adjustment In'),
                            'adjustment_out' => __('Adjustment Out'),
                            'return_in' => __('Return In'),
                            'return_out' => __('Return Out'),
                        ])
                        ->native(false)
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->label(__('Quantity'))
                        ->numeric()
                        ->minValue(0.01)
                        ->required(),
                    Forms\Components\DatePicker::make('movement_date')
                        ->label(__('Date'))
                        ->jalali()
                        ->locale('fa')
                        ->default(now())
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label(__('Notes'))
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with('product'))
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label(__('Product'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->label(__('Type'))->badge()->sortable(),
                Tables\Columns\TextColumn::make('quantity')->label(__('Quantity'))->numeric(2)->sortable(),
                Tables\Columns\TextColumn::make('movement_date')
                    ->label(__('Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')->label(__('Notes'))->limit(50)->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options([
                        'purchase_in' => __('Purchase In'),
                        'sale_out' => __('Sale Out'),
                        'adjustment_in' => __('Adjustment In'),
                        'adjustment_out' => __('Adjustment Out'),
                        'return_in' => __('Return In'),
                        'return_out' => __('Return Out'),
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn(InventoryStockMovement $record) => str_starts_with($record->type, 'adjustment') || str_starts_with($record->type, 'return')),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn(InventoryStockMovement $record) => str_starts_with($record->type, 'adjustment') || str_starts_with($record->type, 'return')),
            ])
            ->bulkActions([])
            ->defaultSort('movement_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryStockMovements::route('/'),
            'create' => Pages\CreateInventoryStockMovement::route('/create'),
            'edit' => Pages\EditInventoryStockMovement::route('/{record}/edit'),
        ];
    }
}
