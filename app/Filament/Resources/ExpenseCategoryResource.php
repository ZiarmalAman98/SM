<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseCategoryResource\Pages;
use App\Filament\Resources\ExpenseCategoryResource\RelationManagers\ExpensesRelationManager;
use App\Models\ExpenseCategory;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ExpenseCategoryResource extends Resource
{
    protected static ?string $model = ExpenseCategory::class;
    protected static ?int $navigationSort = 3;




public static function getLabel(): string
{
    return __('Expense Category');
}

public static function getModelLabel(): string
{
    return __('Expense Category');
}

public static function getPluralModelLabel(): string
{
    return __('Expense Categories');
}

public static function getNavigationLabel(): string
{
    return __('Expense Categories');
}

public static function getNavigationGroup(): string
{
    return __('Finance');
}
    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->required()
                    ->columnSpanFull()
                    ->placeholder(__('Enter category name'))
                    ->maxLength(255),
            ])
            ->description(__("Expense Category Form"))
            ->collapsed(false)
            ->columns(2)
        ]);
}

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label(__('Name'))
                ->searchable()
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
        ->defaultSort('created_at', 'desc')
        ->actions([
            Tables\Actions\ViewAction::make()
                ->label(__('View')),
            Tables\Actions\EditAction::make()
                ->label(__('Edit')),
            Tables\Actions\DeleteAction::make()
                ->label(__('Delete')),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make()
                ->label(__('Delete Selected')),
        ]);
}

    public static function getRelations(): array
    {
        return [
            ExpensesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenseCategories::route('/'),
            'create' => Pages\CreateExpenseCategory::route('/create'),
            'view' => Pages\ViewExpenseCategory::route('/{record}'),
            'edit' => Pages\EditExpenseCategory::route('/{record}/edit'),
        ];
    }
}
