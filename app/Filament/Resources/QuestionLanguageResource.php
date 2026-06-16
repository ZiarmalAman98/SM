<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionLanguageResource\Pages;
use App\Models\QuestionLanguage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Filament\Forms\Components\Section;

class QuestionLanguageResource extends Resource
{
    protected static ?string $model = QuestionLanguage::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): string
{
    return __('Question Bank');
}

public static function getNavigationLabel(): string
{
    return __('Question Languages');
}

public static function getModelLabel(): string
{
    return __('Question Language');
}

public static function getPluralModelLabel(): string
{
    return __('Question Languages');
}

public static function getLabel(): string
{
    return __('Question Language');
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('language')
                            ->required()
                            ->label(__('Language'))
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])
                    ->description(__('Question Language Form'))
                    ->collapsed(false)
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('language')
                    ->label(__('Language'))
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
                //
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionLanguages::route('/'),
            'create' => Pages\CreateQuestionLanguage::route('/create'),
            'view' => Pages\ViewQuestionLanguage::route('/{record}'),
            'edit' => Pages\EditQuestionLanguage::route('/{record}/edit'),
        ];
    }
}