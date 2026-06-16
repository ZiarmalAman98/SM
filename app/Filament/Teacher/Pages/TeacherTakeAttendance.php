<?php

namespace App\Filament\Teacher\Pages;

use App\Models\Subject;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TeacherTakeAttendance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.teacher.pages.teacher-take-attendance';
    protected static ?string $navigationGroup = 'Attendance';
    protected ?string $maxContentWidth = 'full';

    public ?string $selectedDate = null;
    public array $subjects = [];
    public ?int $selectedSubjectId = null;
    public array $users = [];
    public array $userAttendance = [];
    public int $totalDaysInMonth = 0;

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m');

        // Only get subjects for the currently logged-in teacher
        $this->subjects = Subject::where('teacher_id', Auth::id())
            ->pluck('name', 'id')
            ->toArray();
    }

    public function updatedSelectedSubjectId(?int $subjectId): void
    {
        $this->users = [];
        $this->userAttendance = [];

        if (filled($subjectId)) {
            $subject = Subject::with('schoolClass.studentClasses.student')->find($subjectId);

            $students = $subject?->schoolClass?->studentClasses
                ->where('status', 'active')
                ->pluck('student')
                ->filter()
                ->unique('id');

            // Build a simple [userId => userName] array
            $this->users = $students->pluck('name', 'id')->toArray();

            // Get attendance for the selected subject and date range
            $this->getUserAttendance();
        }
    }

    public function getDates(): array
    {
        $current = Carbon::parse($this->selectedDate);

        return [
            'previous' => $current->copy()->subMonthNoOverflow()->format('F Y'),
            'current'  => $current->format('F Y'),
            'next'     => $current->copy()->addMonthNoOverflow()->format('F Y'),
        ];
    }

    public function previousMonth(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subMonthNoOverflow()->format('Y-m');
        $this->getUserAttendance();
    }

    public function nextMonth(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addMonthNoOverflow()->format('Y-m');
        $this->getUserAttendance();
    }

    private function getUserAttendance(): void
    {
        $start = Carbon::parse($this->selectedDate)->startOfMonth();
        $end   = Carbon::parse($this->selectedDate)->endOfMonth();

        $userIds = array_keys($this->users);

        // Pull the attendances for these users for the selected month & subject
        $users = User::whereIn('id', $userIds)
            ->with(['attendances' => function ($query) use ($start, $end) {
                $query->whereBetween('date', [$start, $end])
                    ->where('subject_id', $this->selectedSubjectId);
            }])
            ->get();

        $details = [];

        // For each user, fill in day => state
        foreach ($users as $user) {
            // Eager loaded attendances for this month
            $attendanceCollection = $user->attendances;

            // For each day in the month
            $currentDay = $start->copy();
            while ($currentDay->lte($end)) {
                // Check if there's an attendance record
                $attendance = $attendanceCollection->firstWhere('date', $currentDay->format('Y-m-d'));

                // We can store tri-state as:
                // null => no record
                // true => present
                // false => absent
                if (! $attendance) {
                    $details[$user->id][$currentDay->day] = null;
                } else {
                    // For simplicity, assume "status = true => present" and "status = false => absent".
                    // If your table only has present (true) or no row at all, you might adapt this logic.
                    $details[$user->id][$currentDay->day] = $attendance->status ? true : false;
                }

                $currentDay->addDay();
            }
        }

        $this->userAttendance = $details;
        $this->totalDaysInMonth = $end->day;
    }

    public function saveAttendance(): void
    {
        foreach ($this->userAttendance as $userId => $days) {
            $user = User::find($userId);

            foreach ($days as $day => $triState) {
                $date = Carbon::parse($this->selectedDate)->setDay((int) $day)->format('Y-m-d');

                // triState can be null, true, or false
                if ($triState === true) {
                    // Present => create/update record with status = true
                    $user->attendances()->updateOrCreate(
                        [
                            'teacher_id' => Auth::id(),
                            'subject_id' => $this->selectedSubjectId,
                            'date'       => $date,
                        ],
                        ['status' => true]
                    );
                } elseif ($triState === false) {
                    // Absent => create/update record with status = false
                    $user->attendances()->updateOrCreate(
                        [
                            'teacher_id' => Auth::id(),
                            'subject_id' => $this->selectedSubjectId,
                            'date'       => $date,
                        ],
                        ['status' => false]
                    );
                } else {
                    // null => remove attendance record if it exists
                    $user->attendances()
                        ->where('teacher_id', Auth::id())
                        ->where('subject_id', $this->selectedSubjectId)
                        ->where('date', $date)
                        ->delete();
                }
            }
        }

        Notification::make()
            ->title('Attendance saved successfully.')
            ->success()
            ->send();
    }
}
