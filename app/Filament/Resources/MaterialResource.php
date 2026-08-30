<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Filament\Resources\MaterialResource\RelationManagers;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('Employee Allocations');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getNavigationLabel(): string
    {
        return __('Materials');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getModelLabel(): string
    {
        return __('Material');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Materials');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('stock_type_id')
                    ->label(__('Stock Type'))
                    ->createOptionForm(fn(Form $form) => StockTypeResource::form($form))
                    ->relationship('stockType', 'name')
                    ->native(false)
                    ->required()
                    ->placeholder(__('Select stock type')),

                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->maxLength(255)
                    ->placeholder(__('Enter material name')),

                Forms\Components\Textarea::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull()
                    ->placeholder(__('Enter description')),

                Forms\Components\TextInput::make('stock_quantity')
                    ->label(__('Stock Quantity'))
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->placeholder(__('Enter quantity')),

                Forms\Components\TextInput::make('reorder_level')
                    ->label(__('Reorder Level'))
                    ->required()
                    ->numeric()
                    ->default(10)
                    ->placeholder(__('Enter reorder level')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('stockType.name')
                    ->label(__('Stock Type'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label(__('Stock Quantity'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('reorder_level')
                    ->label(__('Reorder Level'))
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View')),
                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete Selected')),
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'view' => Pages\ViewMaterial::route('/{record}'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}
