<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryCategoryResource\Pages;
use App\Models\InventoryCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryCategoryResource extends Resource
{
    protected static ?string $model = InventoryCategory::class;
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('Inventory');
    }

    public static function getNavigationLabel(): string
    {
        return __('Categories');
    }

    public static function getModelLabel(): string
    {
        return __('Category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Categories');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('Category Information'))
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label(__('Description'))
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('Name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('products_count')->counts('products')->label(__('Products'))->sortable(),
                Tables\Columns\TextColumn::make('description')->label(__('Description'))->limit(50)->toggleable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventoryCategories::route('/'),
            'create' => Pages\CreateInventoryCategory::route('/create'),
            'edit' => Pages\EditInventoryCategory::route('/{record}/edit'),
        ];
    }
}
