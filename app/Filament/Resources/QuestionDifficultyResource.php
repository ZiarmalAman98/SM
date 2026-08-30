<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionDifficultyResource\Pages;
use App\Filament\Resources\QuestionDifficultyResource\RelationManagers;
use App\Models\QuestionDifficulty;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class QuestionDifficultyResource extends Resource
{
    protected static ?string $model = QuestionDifficulty::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;
public static function getNavigationLabel(): string
{
    return __('Questions Difficulties');
}

public static function getModelLabel(): string
{
    return __('Question Difficulty');
}

public static function getPluralModelLabel(): string
{
    return __('Questions Difficulties');
}

public static function getNavigationGroup(): string
{
    return __('Question Bank');
}


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('level')
                            ->required()
                            ->label(__('Level'))
                            ->columnSpanFull()
                            ->maxLength(255),
                    ])
                    ->description(__('Question Difficulty Form'))
                    ->collapsed(false)
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('level')
                    ->label(__('Level'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('questions_count')
                    ->label(__('Questions Count'))
                    ->counts('questions'),

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
            ->filters([
                // Filters can be added here with proper localization if needed
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View')),

                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('Delete Selected')),

                    ExportBulkAction::make()
                        ->label(__('Export Selected')),
                ])->label(__('Bulk Actions')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Relations can be added here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionDifficulties::route('/'),
            'create' => Pages\CreateQuestionDifficulty::route('/create'),
            'view' => Pages\ViewQuestionDifficulty::route('/{record}'),
            'edit' => Pages\EditQuestionDifficulty::route('/{record}/edit'),
        ];
    }
}
