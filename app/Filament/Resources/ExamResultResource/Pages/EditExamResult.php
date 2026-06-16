<?php

namespace App\Filament\Resources\ExamResultResource\Pages;

use App\Filament\Resources\ExamResultResource;
use App\Models\User;
use App\Notifications\StatusChanged;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;
use Filament\Forms;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Exports\ClassResultsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentYearResultExport;
use Filament\Resources\Pages\ViewRecord;


class EditExamResult extends EditRecord
{
    protected static string $resource = ExamResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            // Actions\Action::make('print-exam-result')
            //     ->label('Print Report')
            //     ->icon('heroicon-o-printer')
            //     ->form([
            //         Forms\Components\Select::make('student_id')
            //             ->label('Student')
            //             ->options(\App\Models\User::where('type', 'student')->pluck('name', 'id'))
            //             ->searchable()
            //             ->placeholder('Select student')
            //             ->preload()
            //             ->required(),

            //         Forms\Components\Select::make('class_id')
            //             ->label('Class')
            //             ->options(SchoolClass::query()->pluck('class_name', 'id'))
            //             ->searchable()
            //             ->placeholder('Select class')
            //             ->preload()
            //             ->required(),
            //     ])
            //     ->action(function (array $data) {
            //         return redirect()->route('examResult.print', [
            //             'student_id' => $data['student_id'] ?? null,
            //             'class_id'   => $data['class_id'] ?? null,
            //         ]);
            //     })
            //     ->modalHeading('Print Exam Result')
            //     ->modalSubmitActionLabel('Generate'),

            // // Export all results for a class
            // Actions\Action::make('exportClassResults')
            //     ->label('Export Class Results')
            //     ->icon('heroicon-o-arrow-down-tray')
            //     ->form([
            //         Forms\Components\Select::make('branch_id')
            //             ->label('Branch')
            //             ->options(fn() => \App\Models\Branch::pluck('branch_name', 'id')->toArray())
            //             ->reactive()
            //             ->required(),

            //         Forms\Components\Select::make('class_id')
            //             ->label('Class')
            //             ->options(function (callable $get) {
            //                 $branchId = $get('branch_id');
            //                 if (! $branchId) {
            //                     return [];
            //                 }
            //                 return SchoolClass::where('branch_id', $branchId)
            //                     ->pluck('class_name', 'id')
            //                     ->toArray();
            //             })
            //             ->required(),
            //     ])
            //     ->action(function (array $data) {
            //         $classId = $data['class_id'];
            //         $class   = SchoolClass::findOrFail($classId);

            //         return Excel::download(
            //             new ClassResultsExport($classId, $class->class_name),
            //             'class_results_' . $class->class_name . '.xlsx'
            //         );
            //     }),

            // // ✅ Export one student's last year results
            // Actions\Action::make('export_student_last_year')
            //     ->label(__('Export Student Result'))
            //     ->icon('heroicon-m-arrow-down-tray')
            //     ->form([
            //         Forms\Components\Select::make('student_id')
            //             ->label(__('Student'))
            //             ->options(function () {
            //                 $ids = \App\Models\StudentClass::where('status', 'active')->pluck('student_id');
            //                 return \App\Models\User::whereIn('id', $ids)
            //                     ->pluck('name', 'id')
            //                     ->toArray();
            //             })
            //             ->searchable()
            //             ->preload()
            //             ->required(),

            //         Forms\Components\TextInput::make('year')
            //             ->label(__('Shamsi Year (e.g., 1403)'))
            //             ->numeric()
            //             ->default(fn() => \Morilog\Jalali\Jalalian::fromCarbon(now())->getYear() - 1)
            //             ->required(),
            //     ])
            //     ->action(function (array $data) {
            //         $studentId = (int) $data['student_id'];
            //         $year      = (int) $data['year'];

            //         return (new StudentYearResultExport($studentId, $year))
            //             ->download("student-{$studentId}-{$year}.xlsx");
            //     }),

            // Actions\Action::make('printSubjectByClass')
            //     ->label(__('Print Subject Result'))
            //     ->icon('heroicon-o-printer')
            //     ->form([
            //         Forms\Components\Select::make('branch_id')
            //             ->label(__('Branch'))
            //             ->options(fn() => \App\Models\Branch::pluck('branch_name', 'id')->toArray())
            //             ->searchable()
            //             ->preload()
            //             ->reactive()
            //             ->required(),

            //         Forms\Components\Select::make('class_id')
            //             ->label(__('Class'))
            //             ->options(function (callable $get) {
            //                 $branchId = $get('branch_id');
            //                 if (!$branchId) return [];
            //                 return SchoolClass::where('branch_id', $branchId)
            //                     ->pluck('class_name', 'id')
            //                     ->toArray();
            //             })
            //             ->searchable()
            //             ->preload()
            //             ->reactive()
            //             ->required(),

            //         Forms\Components\Select::make('subject_id')
            //             ->label(__('Subject'))
            //             ->options(function (callable $get) {
            //                 $classId = $get('class_id');
            //                 if (! $classId) return [];
            //                 return \App\Models\Subject::whereHas('schoolClass', fn($q) => $q->where('school_class_id', $classId))
            //                     ->orderBy('name')
            //                     ->pluck('name', 'id')
            //                     ->toArray();
            //             })
            //             ->searchable()
            //             ->preload()
            //             ->required(),

            //         // ✅ NEW: Exam Type
            //         Forms\Components\Select::make('exam_type')
            //             ->label(__('Exam Type'))
            //             ->options([
            //                 'mid_term' => __('Mid Term'),
            //                 'final'    => __('Final'),
            //             ])
            //             ->required(),
            //     ])
            //     ->action(function (array $data) {
            //         return redirect()->route('examResults.subject.print', [
            //             'branch_id'  => (int) $data['branch_id'],
            //             'class_id'   => (int) $data['class_id'],
            //             'subject_id' => (int) $data['subject_id'],
            //             'exam_type'  => (string) $data['exam_type'],
            //         ]);
            //     })
            //     ->modalHeading(__('Print One Subject (Class)'))
            //     ->modalSubmitActionLabel(__('Generate')),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        try {
            $student = User::find($data['student_id']);

            if ($student) {
                $appUrl = env('APP_URL');

                // Proceed only if not running on localhost
                if (!in_array($appUrl, ['http://127.0.0.1:8000', 'http://localhost'])) {
                    $subject = $data['subject'] ?? 'the subject';
                    $message = "Your exam score for {$subject} has been updated.";
                    $student->notify(new StatusChanged($message));
                }
            }
        } catch (\Exception $e) {
            Log::error('Notification sending failed: ' . $e->getMessage());
        }

        return $data;
    }
}
