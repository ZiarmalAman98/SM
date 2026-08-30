<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResponseResource\Pages;
use App\Filament\Resources\EvaluationResponseResource\RelationManagers;
use App\Models\EvaluationResponse;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;

class EvaluationResponseResource extends Resource
{
    protected static ?string $model = EvaluationResponse::class;
    // protected static ?string $navigationGroup = 'Evaluations';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?int $navigationSort = 2;
    public static function getLabel(): string
    {
        return __('Evaluation Response');
    }

    public static function getModelLabel(): string
    {
        return __('Evaluation Response');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Evaluation Responses');
    }

    public static function getNavigationLabel(): string
    {
        return __('Evaluation Responses');
    }

    public static function getNavigationGroup(): string
    {
        return __('Evaluations');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('teacher_id')
                                ->relationship('teacher', 'name')
                                ->label(__('Teacher'))
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->required(),

                            Forms\Components\Select::make('student_id')
                                ->relationship('student', 'name')
                                ->label(__('Student'))
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->placeholder(__('Anonymous')),

                            Forms\Components\Select::make('evaluation_question_id')
                                ->relationship('question', 'question_text')
                                ->label(__('Question'))
                                ->preload()
                                ->native(false)
                                ->searchable()
                                ->required(),

                            Forms\Components\Select::make('rating')
                                ->label(__('Rating'))
                                ->options([
                                    1 => __('1 - Very Poor'),
                                    2 => __('2 - Poor'),
                                    3 => __('3 - Average'),
                                    4 => __('4 - Good'),
                                    5 => __('5 - Excellent'),
                                ])
                                ->native(false)
                                ->required(),
                        ]),

                    Forms\Components\Textarea::make('comment')
                        ->label(__('Comment'))
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('academic_year')
                        ->label(__('Academic Year'))
                        ->default(date('Y'))
                        ->numeric()
                        ->required()
                        ->columnSpanFull(),
                ])
                ->columns(1),
        ]);
    }


    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.name')
                    ->label(__('Teacher'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->default(__('Anonymous'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('question.question_text')
                    ->label(__('Question'))
                    ->wrap()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->question->question_text),

                // ⭐️ Rating with stars
                TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->formatStateUsing(function ($state) {
                        return str_repeat('⭐', $state);
                    })
                    ->sortable(),

                TextColumn::make('academic_year')
                    ->label(__('Year'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Submitted'))
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter by Teacher
                SelectFilter::make('teacher_id')
                    ->relationship('teacher', 'name')
                    ->label(__('Teacher'))
                    ->preload()
                    ->searchable()
                    ->native(false),

                // Filter by Student
                SelectFilter::make('student_id')
                    ->relationship('student', 'name')
                    ->label(__('Student'))
                    ->preload()
                    ->searchable()
                    ->native(false),

                // Filter by Academic Year
                SelectFilter::make('academic_year')
                    ->options(
                        EvaluationResponse::query()
                            ->select('academic_year')
                            ->distinct()
                            ->orderByDesc('academic_year')
                            ->pluck('academic_year', 'academic_year')
                            ->toArray()
                    )
                    ->label(__('Year'))
                    ->native(false),

                // Filter by Question
                SelectFilter::make('evaluation_question_id')
                    ->relationship('question', 'question_text')
                    ->label(__('Question'))
                    ->preload()
                    ->searchable()
                    ->native(false),
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
            'index' => Pages\ListEvaluationResponses::route('/'),
            'create' => Pages\CreateEvaluationResponse::route('/create'),
            'edit' => Pages\EditEvaluationResponse::route('/{record}/edit'),
        ];
    }
}
