<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockTypeResource\Pages;
use App\Models\StockType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class StockTypeResource extends Resource
{
    protected static ?string $model = StockType::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function getNavigationGroup(): string
{
    return __('Inventory Management');
}

public static function getNavigationLabel(): string
{
    return __('Stock Types');
}

public static function getModelLabel(): string
{
    return __('Stock Type');
}

public static function getPluralModelLabel(): string
{
    return __('Stock Types');
}

public static function getLabel(): string
{
    return __('Stock Type');
}

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label(__('Name'))
                ->required()
                ->columnSpanFull()
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->label(__('Description'))
                ->columnSpanFull(),
        ]);
}

  public static function table(Table $table): Table
{
    return $table
        ->defaultSort('created_at', 'desc')
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label(__('Name'))
                ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label(__('Created At'))
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->label(__('Updated At'))
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\Filter::make('recent')
                ->label(__('Recent'))
                ->query(fn (Builder $query) => $query->where('created_at', '>=', now()->subMonth())),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
            ExportBulkAction::make()
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ]);
}

    public static function getRelations(): array
    {
        return [
            // Define relations here, e.g.,
            // RelationManagers\StockItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockTypes::route('/'),
            'create' => Pages\CreateStockType::route('/create'),
            'view' => Pages\ViewStockType::route('/{record}'),
            'edit' => Pages\EditStockType::route('/{record}/edit'),
        ];
    }

    
    public static function getNavigationSort(): ?int
    {
        return 2;
    }
}
