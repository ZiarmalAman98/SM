<?php

namespace App\Filament\Resources\EvaluationResponseResource\Pages;

use App\Filament\Resources\EvaluationResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;
use Filament\Actions\Action;

class ListEvaluationResponses extends ListRecords
{
    protected static string $resource = EvaluationResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('print')
                ->label('Print Report')
                ->icon('heroicon-o-printer')
                ->form([
                    Forms\Components\Select::make('teacher_id')
                        ->label('Teacher')
                        ->relationship('teacher', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false)
                        ->placeholder('All Teachers'),

                    Forms\Components\Select::make('student_id')
                        ->label('Student')
                        ->relationship('student', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->placeholder('All Students'),

                    Forms\Components\Select::make('academic_year')
                        ->label('Academic Year')
                        ->options(
                            \App\Models\EvaluationResponse::query()
                                ->select('academic_year')
                                ->distinct()
                                ->orderByDesc('academic_year')
                                ->pluck('academic_year', 'academic_year')
                                ->filter(fn($year) => $year !== null)
                                ->toArray()
                        )
                        ->native(false)
                        ->placeholder('All Years'),
                ])
                ->action(function (array $data) {
                    return redirect()->route('evaluation.print', [
                        'teacher_id' => $data['teacher_id'],
                        'student_id' => $data['student_id'],
                        'academic_year' => $data['academic_year'],
                    ]);
                })
                ->modalHeading('Print Evaluation Report')
                ->modalSubmitActionLabel('Generate'),
        ];
    }
}
