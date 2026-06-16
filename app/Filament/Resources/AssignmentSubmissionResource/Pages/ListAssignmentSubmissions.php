<?php

namespace App\Filament\Resources\AssignmentSubmissionResource\Pages;

use App\Filament\Resources\AssignmentSubmissionResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;

class ListAssignmentSubmissions extends ListRecords
{
    protected static string $resource = AssignmentSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('Print Submissions')
                ->label('Print Assignment')
                ->icon('heroicon-o-printer')
                ->form([
                    Forms\Components\Select::make('assignment_id')
                        ->label('Assignment')
                        ->native(false)
                        ->relationship('assignment', 'title')
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label('Submission Status')
                        ->native(false)
                        ->options([
                            'pending' => 'Pending',
                            'reviewed' => 'Reviewed',
                            'graded' => 'Graded',
                        ])
                        ->placeholder('All')
                ])
                ->action(function (array $data) {
                    return redirect()->route('assignments.submissions.print', [
                        'assignment_id' => $data['assignment_id'],
                        'status' => $data['status'],
                    ]);
                })
                ->modalHeading('Print Assignment Submissions')
                ->modalSubmitActionLabel('Generate'),
        ];
    }
}
