<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 3;

    /* ───────────── labels ───────────── */

    public static function getNavigationGroup(): string
    {
        return __('Question Bank');
    }
    public static function getNavigationLabel(): string
    {
        return __('Questions');
    }
    public static function getModelLabel(): string
    {
        return __('Question');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Questions');
    }
    public static function getLabel(): string
    {
        return __('Question');
    }

    /* ───────────────── form ───────────────── */

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('Multiple Questions'))
                ->schema([
                    Forms\Components\Repeater::make('questions')
                        ->label('')
                        ->schema([
                            /* common fields */
                            Forms\Components\Hidden::make('user_id')
                                ->default(auth()->user()?->id)
                                ->required(),

                            Forms\Components\Select::make('language_id')
                                ->label(__('Language'))
                                ->relationship('language', 'language')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm(fn(Form $form) => QuestionLanguageResource::form($form)),

                            Forms\Components\Select::make('subject_id')
                                ->label(__('Subject'))
                                ->options(
                                    Subject::with('schoolClass')->get()
                                        ->mapWithKeys(fn($subject) => [
                                            $subject->id => $subject->name .
                                                (optional($subject->schoolClass)->class_name
                                                    ? ' (' . $subject->schoolClass->class_name . ')'
                                                    : ''),
                                        ])
                                )
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\Select::make('difficulty_id')
                                ->label(__('Difficulty'))
                                ->relationship('difficulty', 'level')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm(fn(Form $form) => QuestionDifficultyResource::form($form)),

                            Forms\Components\Select::make('type')
                                ->label(__('Type'))
                                ->options([
                                    'multiple_choice'   => __('Multiple Choice'),
                                    'true_false'        => __('True/False'),
                                    'written'           => __('Written'),
                                    'fill_in_the_blank' => __('Fill in the Blank'),
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(
                                    fn($state, Forms\Set $set) =>
                                    $set('choices', static::generateDefaultChoices($state))
                                )
                                ->native(false),

                            Forms\Components\Textarea::make('question_text')
                                ->label(__('Question Text'))
                                ->required()
                                ->columnSpanFull(),

                            Forms\Components\Textarea::make('explanation')
                                ->label(__('Explanation'))
                                ->columnSpanFull(),

                            /* nested choices */
                            Forms\Components\Repeater::make('choices')
                                ->label(__('Answer Choices'))
                                ->schema([
                                    TextInput::make('choice_text')
                                        ->label(__('Choice Text'))
                                        ->required()
                                        ->columnSpan(2),

                                    Toggle::make('is_correct')
                                        ->label(__('Correct Answer'))
                                        ->default(false)
                                        ->onColor('success')
                                        ->offColor('danger')
                                        ->inline(false)
                                        ->columnSpan(1),
                                ])
                                ->columns(3)
                                ->columnSpanFull()
                                ->grid(2)
                                ->addActionLabel(__('Add Choice'))
                                ->visible(
                                    fn(Forms\Get $get) =>
                                    in_array($get('type'), ['multiple_choice', 'true_false'], true)
                                )
                                ->dehydrated()
                                ->mutateDehydratedStateUsing(
                                    fn($state) => array_values($state ?? [])
                                )
                                ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get) {
                                    /* FIXED: added missing parenthesis after empty() */
                                    if (empty($get('choices'))) {
                                        $set('choices', static::generateDefaultChoices($get('type')));
                                    }
                                }),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->addActionLabel(__('Add Another Question'))
                        ->itemLabel(
                            fn(array $state) =>
                            $state['question_text'] ?? __('New Question')
                        )
                        ->collapsible()
                        ->cloneable()
                        ->defaultItems(1),
                ])
                ->description(__('Create multiple questions at once'))
                ->collapsed(false),
        ]);
    }

    /* ───────────────── table (unchanged) ───────────────── */

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label(__('User'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('language.language')->label(__('Language'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('subject.name')->label(__('Subject'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('difficulty.level')->label(__('Difficulty'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('type')->label(__('Type'))->formatStateUsing(fn($state) => __($state)),
                Tables\Columns\TextColumn::make('created_at')->label(__('Created At'))->jalaliDateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label(__('Updated At'))->jalaliDateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('difficulty_id')->label(__('Difficulty'))->relationship('difficulty', 'level'),
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options([
                        'multiple_choice'   => __('Multiple Choice'),
                        'true_false'        => __('True/False'),
                        'written'           => __('Written'),
                        'fill_in_the_blank' => __('Fill in the Blank'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('View')),
                Tables\Actions\EditAction::make()->label(__('Edit')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected')),
                    ExportBulkAction::make()->label(__('Export Selected')),
                ])->label(__('Bulk Actions')),
            ]);
    }

    /* ─────────── relations & pages ─────────── */

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'view'   => Pages\ViewQuestion::route('/{record}'),
            'edit'   => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }

    /* ─────────── helpers ─────────── */

    protected static function generateDefaultChoices(string $type): array
    {
        return match ($type) {
            'true_false' => [
                ['choice_text' => __('True'),  'is_correct' => true],
                ['choice_text' => __('False'), 'is_correct' => false],
            ],
            'multiple_choice' => [
                ['choice_text' => '', 'is_correct' => true],
                ['choice_text' => '', 'is_correct' => false],
                ['choice_text' => '', 'is_correct' => false],
                ['choice_text' => '', 'is_correct' => false],
            ],
            default => [],
        };
    }

    protected static function ensureAtLeastOneCorrectAnswer(Forms\Set $set, Forms\Get $get): void
    {
        $choices = collect($get('choices'));
        $type    = $get('../type');

        if ($type === 'multiple_choice' && $choices->where('is_correct', true)->isEmpty()) {
            $set(
                'choices',
                $choices
                    ->map(function ($choice, $index) {
                        if ($index === 0) {
                            $choice['is_correct'] = true;
                        }
                        return $choice;
                    })
                    ->toArray()
            );
        }
    }
}
