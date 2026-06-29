<?php

namespace App\Filament\Clusters\AllReports\Pages;

use App\Filament\Clusters\AllReports;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\StudentClass;
use App\Models\User;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class GenerateExamCard extends Page
{
    protected static string $view = 'filament.clusters.all-reports.pages.generate-exam-card';

    protected static ?string $cluster = AllReports::class;

    protected static ?string $navigationLabel = 'Exam Card';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 6;

    public ?int $class_id = null;

    public ?int $student_id = null;

    public ?string $exam_type = 'mid_term';

    public ?int $exam_id = null;

    public array $studentOptions = [];

    public array $examOptions = [];

    public array $studentOptionsByClass = [];

    public array $examOptionsByClassType = [];

    protected function getHeaderActions(): array
    {
        return [];
    }

    #[Computed]
    public function classes()
    {
        return SchoolClass::orderBy('class_name')->get();
    }

    public function mount(): void
    {
        $this->studentOptionsByClass = StudentClass::query()
            ->with('student')
            ->whereHas('student', fn ($query) => $query->where('type', 'student'))
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderByDesc('id')
            ->get()
            ->groupBy('class_id')
            ->map(function ($rows) {
                return $rows
                    ->pluck('student')
                    ->filter(fn ($student) => $student instanceof User && $student->type === 'student')
                    ->unique('id')
                    ->sortBy(fn (User $student) => trim(($student->name ?? '') . ' ' . ($student->last_name ?? '')))
                    ->mapWithKeys(fn (User $student) => [
                        $student->id => trim(($student->name ?? '') . ' ' . ($student->last_name ?? '')) ?: 'Student #' . $student->id,
                    ])
                    ->all();
            })
            ->all();

        $this->examOptionsByClassType = Exam::query()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn (Exam $exam) => $exam->class_id . '|' . $exam->exam_type)
            ->map(function ($rows) {
                return $rows
                    ->mapWithKeys(fn (Exam $exam) => [
                        $exam->id => trim(($exam->name ?? 'Exam') . ' - ' . ($exam->date ?? '')),
                    ])
                    ->all();
            })
            ->all();

        $this->syncDependentOptions();
    }

    protected function syncDependentOptions(): void
    {
        $this->studentOptions = $this->class_id
            ? ($this->studentOptionsByClass[$this->class_id] ?? [])
            : [];

        if ($this->student_id && ! array_key_exists($this->student_id, $this->studentOptions)) {
            $this->student_id = null;
        }

        $examKey = $this->class_id && $this->exam_type
            ? $this->class_id . '|' . $this->exam_type
            : null;

        $this->examOptions = $examKey
            ? ($this->examOptionsByClassType[$examKey] ?? [])
            : [];

        if ($this->exam_id && ! array_key_exists($this->exam_id, $this->examOptions)) {
            $this->exam_id = null;
        }
    }

    public function updated(string $property): void
    {
        if ($property === 'class_id') {
            $this->student_id = null;
            $this->exam_id = null;
            $this->syncDependentOptions();
        }

        if ($property === 'exam_type') {
            $this->exam_id = null;
            $this->syncDependentOptions();
        }
    }

    public function generate(): void
    {
        $validated = $this->validate([
            'class_id' => ['required', 'exists:school_classes,id'], 
            'student_id' => ['required', 'exists:users,id'],
            'exam_type' => ['required', 'in:mid_term,final'],
            'exam_id' => ['nullable', 'exists:exams,id'],
        ]);

        $this->redirect(route('exam-card.print', $validated), navigate: false);
    }
}
