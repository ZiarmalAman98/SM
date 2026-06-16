<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomeSourceResource\Pages;
use App\Filament\Resources\IncomeSourceResource\RelationManagers\IncomesRelationManager;
use App\Models\IncomeSource;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IncomeSourceResource extends Resource
{
    protected static ?string $model = IncomeSource::class;

  public static function getNavigationGroup(): string
{
    return __('Finance');
}

public static function getNavigationLabel(): string
{
    return __('Income Sources');
}

public static function getModelLabel(): string
{
    return __('Income Source');
}

public static function getPluralModelLabel(): string
{
    return __('Income Sources');
}

public static function getLabel(): string
{
    return __('Income Source');
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('Source Name'))
                        ->required()
                        ->maxLength(255)
                        ->placeholder(__('Enter income source name'))
                        ->columnSpanFull(),
                ])
                ->description(__("Income Source Form"))
                ->collapsed(false)
                ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Source Name'))
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
            ]);
    }

    public static function getRelations(): array
    {
        return [
            IncomesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomeSources::route('/'),
            'create' => Pages\CreateIncomeSource::route('/create'),
            'view' => Pages\ViewIncomeSource::route('/{record}'),
            'edit' => Pages\EditIncomeSource::route('/{record}/edit'),
        ];
    }
}
