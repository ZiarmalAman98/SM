<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;
use Filament\Forms\Components\TextInput;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('generateExam')
                ->label(__('Generate Exam'))
                ->icon('heroicon-o-document-text')
                ->modalHeading(__('Generate Exam'))
                ->form([
                    Forms\Components\Section::make()
                        ->columns(3)
                        ->schema([
                            Forms\Components\TextInput::make('exam_name')
                                ->label(__('Exam Name'))
                                ->required(),

                            Forms\Components\TextInput::make('teacher_name')
                                ->label(__('Teacher Name'))
                                ->required(),

                            Forms\Components\Select::make('subject_id')
                                ->label(__('Subject'))
                                ->relationship('subject', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\Select::make('difficulty_id')
                                ->label(__('Difficulty Level'))
                                ->relationship('difficulty', 'level')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Forms\Components\Select::make('language_id')
                                ->label(__('Language'))
                                ->relationship('language', 'language')
                                ->searchable()
                                ->preload()
                                ->required(),



                            DatePicker::make('exam_date')
                                ->label(__('Exam Date'))
                                ->jalali()  // Now works!
                                ->required()
                                ->placeholder(__('Select the end date')),

                            Forms\Components\TextInput::make('duration')
                                ->label(__('Exam Duration (in minutes)'))
                                ->numeric()
                                ->required(),

                            Forms\Components\TextInput::make('total_score')
                                ->label(__('Total Score'))
                                ->numeric()
                                ->required(),

                            Forms\Components\Repeater::make('question_types')
                                ->label(__('Question Type Distribution'))
                                ->columnSpanFull()
                                ->columns(3)
                                ->schema([
                                    Forms\Components\Select::make('type')
                                        ->label(__('Type'))
                                        ->options([
                                            'multiple_choice' => __('Multiple Choice'),
                                            'true_false' => __('True/False'),
                                            'written' => __('Written'),
                                            'fill_in_the_blank' => __('Fill in the Blank'),
                                        ])
                                        ->required(),

                                    Forms\Components\TextInput::make('amount')
                                        ->label(__('Number of Questions'))
                                        ->numeric()
                                        ->required(),

                                    Forms\Components\TextInput::make('score')
                                        ->label(__('Score for This Section'))
                                        ->numeric()
                                        ->required(),
                                ])
                                ->minItems(1)
                                ->addActionLabel(__('Add Type')),
                        ])
                ])
                ->action(fn(array $data) => $this->generatePrintableExam($data))
                ->modalSubmitActionLabel(__('Generate'))
                ->closeModalByClickingAway(false),
        ];
    }

    protected function generatePrintableExam(array $data)
    {
        // Convert data including question_types to query parameters
        $queryParams = http_build_query([
            'exam_name' => $data['exam_name'],
            'teacher_name' => $data['teacher_name'],
            'exam_date' => $data['exam_date'],
            'duration' => $data['duration'],
            'total_score' => $data['total_score'],
            'subject_id' => $data['subject_id'],
            'difficulty_id' => $data['difficulty_id'],
            'language_id' => $data['language_id'],
            'question_types' => json_encode($data['question_types']), // Convert to JSON for URL
        ]);

        return redirect()->away(url('/exam/print?' . $queryParams));
    }
}
