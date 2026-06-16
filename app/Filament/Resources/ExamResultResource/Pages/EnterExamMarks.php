<?php

namespace App\Filament\Resources\ExamResultResource\Pages;

use App\Filament\Resources\ExamResultResource;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\StudentClass;
use App\Models\SchoolClass;
use App\Models\Branch;
use App\Models\User;
use App\Models\ExamResult;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;

class EnterExamMarks extends Page
{
    protected static string $resource = ExamResultResource::class;
    protected static string $view = 'filament.resources.exam-result-resource.pages.enter-exam-marks';
    protected static ?string $slug = 'enter';

    public ?int $branch_id = null;
    public ?int $class_id = null;
    public ?int $exam_id = null;
    public ?int $subject_id = null;
    public ?string $exam_type = null;

    public $students = [];
    public $marks = [];
    public $written_marks = [];
    public $recital_marks = [];
    public $homework_marks = [];
    public $class_activity_marks = [];
    public $mark_in_words = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('branch_id')
                ->label('Branch')
                ->options(Branch::pluck('branch_name', 'id'))
                ->reactive()
                ->afterStateUpdated(fn($state) => $this->class_id = null),

            Forms\Components\Select::make('class_id')
                ->label('Class')
                ->options(fn() => $this->branch_id
                    ? SchoolClass::where('branch_id', $this->branch_id)->pluck('class_name', 'id')
                    : [])
                ->reactive(),

            Forms\Components\Select::make('exam_id')
                ->label('Exam')
                ->options(fn() => $this->class_id
                    ? Exam::where('class_id', $this->class_id)->pluck('name', 'id')
                    : [])
                ->reactive()
                ->afterStateUpdated(function($state) {
                    $this->exam_type = null;
                    if ($state) {
                        $exam = Exam::find($state);
                        $this->exam_type = $exam?->exam_type;
                    }
                })
                ->required(),

            Forms\Components\Select::make('subject_id')
                ->label('Subject')
                ->options(fn() => $this->class_id
                    ? Subject::where('school_class_id', $this->class_id)->pluck('name', 'id')
                    : [])
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($state) => $this->loadStudents()),
        ])
            ->columns(2)
         ;
    }

    public function loadStudents(): void
    {
        $this->students = [];

        if (!$this->class_id) return;

        $studentIds = StudentClass::where('class_id', $this->class_id)
            ->where('status', 'active')
            ->pluck('student_id');

        $this->students = User::whereIn('id', $studentIds)->get();

        foreach ($this->students as $student) {
            $existing = ExamResult::where('student_id', $student->id)
                ->where('class_id', $this->class_id)
                ->when($this->exam_id, fn($q) => $q->where('exam_id', $this->exam_id))
                ->when($this->subject_id, fn($q) => $q->where('subject_id', $this->subject_id))
                ->first();

            $this->written_marks[$student->id] = $existing?->written_marks ?? null;
            $this->recital_marks[$student->id] = $existing?->recital_marks ?? null;
            $this->homework_marks[$student->id] = $existing?->homework_marks ?? null;
            $this->class_activity_marks[$student->id] = $existing?->class_activity_marks ?? null;
            $this->mark_in_words[$student->id] = $existing?->mark_in_words ?? null;

            // Calculate total marks from individual components
            $this->marks[$student->id] = $this->calculateTotalMarks($student->id);
        }
    }

    public function getMaxMarks(): int
    {
        return match($this->exam_type) {
            'mid_term' => 40,
            'final' => 60,
            default => 100
        };
    }

    public function calculateTotalMarks($studentId): float
    {
        $writtenScore = (float) ($this->written_marks[$studentId] ?? 0);
        $recitalScore = (float) ($this->recital_marks[$studentId] ?? 0);
        $homeworkScore = (float) ($this->homework_marks[$studentId] ?? 0);
        $activityScore = (float) ($this->class_activity_marks[$studentId] ?? 0);

        return $writtenScore + $recitalScore + $homeworkScore + $activityScore;
    }

    public function updated($property): void
    {
        // Check if any of the mark properties were updated
        if (str_contains($property, 'written_marks.') ||
            str_contains($property, 'recital_marks.') ||
            str_contains($property, 'homework_marks.') ||
            str_contains($property, 'class_activity_marks.')) {

            // Extract student ID from property name (e.g., "written_marks.123" -> "123")
            $studentId = explode('.', $property)[1];

            // Recalculate total marks for this student
            $this->marks[$studentId] = $this->calculateTotalMarks($studentId);
        }
    }

    public function save(): void
    {
        if (!$this->exam_id || !$this->subject_id || !$this->class_id) {
            Notification::make()->title('Please complete all selections')->danger()->send();
            return;
        }

        $maxMarks = $this->getMaxMarks();
        $hasValidationErrors = false;

        // Validate marks before saving
        foreach ($this->students as $student) {
            $totalScore = $this->calculateTotalMarks($student->id);

            if ($totalScore > $maxMarks) {
                Notification::make()
                    ->title("Total marks for {$student->name} exceeds maximum allowed ({$maxMarks})")
                    ->danger()
                    ->send();
                $hasValidationErrors = true;
            }
        }

        if ($hasValidationErrors) {
            return;
        }

        foreach ($this->students as $student) {
            $writtenScore = (float) ($this->written_marks[$student->id] ?? null);
            $recitalScore = (float) ($this->recital_marks[$student->id] ?? null);
            $homeworkScore = (float) ($this->homework_marks[$student->id] ?? null);
            $activityScore = (float) ($this->class_activity_marks[$student->id] ?? null);
            $markInWords = (string) ($this->mark_in_words[$student->id] ?? null);

            // Calculate total marks automatically
            $totalScore = $this->calculateTotalMarks($student->id);

            ExamResult::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'exam_id' => $this->exam_id,
                    'subject_id' => $this->subject_id,
                ],
                [
                    'marks' => $totalScore,
                    'written_marks' => $writtenScore,
                    'recital_marks' => $recitalScore,
                    'homework_marks' => $homeworkScore,
                    'class_activity_marks' => $activityScore,
                    'mark_in_words' => $markInWords,
                    'class_id' => $this->class_id,
                ],
            );
        }

        Notification::make()->title('Exam results saved successfully')->success()->send();
    }
}
