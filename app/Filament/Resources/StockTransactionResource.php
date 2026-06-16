<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockTransactionResource\Pages;
use App\Filament\Resources\StockTransactionResource\RelationManagers;
use App\Models\StockTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Morilog\Jalali\Jalalian;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class StockTransactionResource extends Resource
{
    protected static ?string $model = StockTransaction::class;

    public static function getNavigationGroup(): string
    {
        return __('Inventory Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Stock Transactions');
    }

    public static function getModelLabel(): string
    {
        return __('Stock Transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Stock Transactions');
    }

    public static function getLabel(): string
    {
        return __('Stock Transaction');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('material_id')
                ->relationship('material', 'name')
                ->label(__('Material'))
                ->required()
                ->native(false)
                ->createOptionForm([
                    // Add your MaterialResource form fields here
                    Forms\Components\TextInput::make('name')
                        ->label(__('Name'))
                        ->required(),
                    // Add other material fields as needed
                ])
                ->placeholder(__('Select material')),

            Forms\Components\Select::make('transaction_type')
                ->label(__('Transaction Type'))
                ->native(false)
                ->options([
                    'add' => __('Add'),
                    'sell' => __('Sell'),
                    'issue' => __('Issue'),
                    'return' => __('Return'),
                ])
                ->required()
                ->placeholder(__('Select transaction type')),

            Forms\Components\TextInput::make('quantity')
                ->label(__('Quantity'))
                ->required()
                ->numeric()
                ->minValue(1)
                ->placeholder(__('Enter quantity')),

            Forms\Components\DatePicker::make('date')
                ->label(__('Date'))
                ->jalali()
                ->locale('fa')
                ->default(now())
                ->required()
                ->placeholder(__('Select date')),

            Forms\Components\RichEditor::make('details')
                ->label(__('Details'))
                ->columnSpanFull()
                ->placeholder(__('Enter transaction details')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('material.name')
                    ->label(__('Material'))
                    ->sortable()
                    ->searchable()
                    ->tooltip(fn($record) => $record->material->name ?? __('Not specified')),

                Tables\Columns\TextColumn::make('transaction_type')
                    ->label(__('Type'))
                    ->formatStateUsing(fn($state) => [
                        'add' => __('Add'),
                        'sell' => __('Sell'),
                        'issue' => __('Issue'),
                        'return' => __('Return'),
                    ][$state] ?? $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('Quantity'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d'))
                    ->sortable()
                    ->tooltip(fn($record) => Jalalian::fromDateTime($record->date)->format('Y/m/d')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip(__('View')),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip(__('Edit')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete Selected')),
                ExportBulkAction::make()
                    ->label(__('Export Selected')),
            ])
            ->emptyStateHeading(__('No transactions found'))
            ->emptyStateDescription(__('Create your first stock transaction'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Transaction')),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockTransactions::route('/'),
            'create' => Pages\CreateStockTransaction::route('/create'),
            'view' => Pages\ViewStockTransaction::route('/{record}'),
            'edit' => Pages\EditStockTransaction::route('/{record}/edit'),
        ];
    }
}
