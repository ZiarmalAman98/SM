<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationQuestionResource\Pages;
use App\Filament\Resources\EvaluationQuestionResource\RelationManagers;
use App\Filament\Resources\EvaluationQuestionResource\RelationManagers\ResponsesRelationManager;
use App\Models\EvaluationQuestion;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Morilog\Jalali\Jalalian;

class EvaluationQuestionResource extends Resource
{
    protected static ?string $model = EvaluationQuestion::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?int $navigationSort = 1;

    public static function getLabel(): string
    {
        return __('Evaluation Question');
    }

    public static function getModelLabel(): string
    {
        return __('Evaluation Question');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Evaluation Questions');
    }

    public static function getNavigationLabel(): string
    {
        return __('Evaluation Questions');
    }

    public static function getNavigationGroup(): string
    {
        return __('Evaluations');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('question_text')
                            ->label(__('Question'))
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255)
                            ->placeholder(__('Enter question text')),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Is Active'))
                            ->default(true)
                            ->columnSpanFull(),
                    ])
                    ->description(__('Evaluation Question Form'))
                    ->collapsed(false)
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question_text')
                    ->label(__('Question Text'))
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->question_text),

                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->tooltip(fn($record) => $record->created_at->diffForHumans()),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip(__('View')),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip(__('Edit')),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active Status'))
                    ->native(false)
                    ->trueLabel(__('Active'))
                    ->falseLabel(__('Inactive'))
                    ->placeholder(__('All')),
            ])
            ->emptyStateHeading(__('No questions found'))
            ->emptyStateDescription(__('Create your first evaluation question'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Question')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ResponsesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluationQuestions::route('/'),
            'create' => Pages\CreateEvaluationQuestion::route('/create'),
            'edit' => Pages\EditEvaluationQuestion::route('/{record}/edit'),
        ];
    }
}
