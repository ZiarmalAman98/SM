<?php

namespace App\Filament\Pages;

use App\Models\Subject;
use App\Models\User;
use App\Notifications\StatusChanged;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Morilog\Jalali\Jalalian;
use App\Models\SchoolClass;


class Attendance extends Page
{
    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('super_admin'); // ✅ change 'admin' to your desired role
    }
    public static function getNavigationGroup(): string
    {
        return __('Attendance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Student Attendance');
    }

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static string $view = 'filament.pages.attendance';
    protected ?string $maxContentWidth = 'full';

    public ?string $selectedDate = null;

    /** @var array<int, string> */
    public array $teachers = [];

    public ?int $selectedTeacherId = null;

    /** @var array<int, string> */
    public array $subjects = [];

    public ?int $selectedSubjectId = null;

    // /** @var array<int, string> */
    // public array $users = [];

    // After:
    /** @var array<int, array{id:int,name:string,father_name:?string}> */
    public array $users = [];

    /** @var array<int, array<int, array{status: bool|null, disabled: bool}>> */
    public array $userAttendance = [];

    public int $totalDaysInMonth = 1;

    // Add these properties after the existing ones
    /** @var array<int, string> */
    public array $classes = [];


public ?int $selectedClassId = null;

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m');

        $this->teachers = User::where('type', 'teacher')
            ->pluck('name', 'id')
            ->toArray();

        $this->loadClasses(); // Add this line
        $this->selectedTeacherId = array_key_first($this->teachers);
        $this->loadSubjects($this->selectedTeacherId);
    }

    public function loadSubjects($teacherId): void
    {
        $this->subjects = [];
        $this->selectedSubjectId = null;
        $this->users = [];
        $this->userAttendance = [];

        if (filled($teacherId)) {
            $teacher = User::with('subjects')->find($teacherId);
            $this->subjects = $teacher?->subjects
                ->pluck('name', 'id')
                ->toArray();
        }
    }

    // Add this method after the existing methods
    public function loadClasses(): void
    {
        $this->classes = \App\Models\SchoolClass::pluck('class_name', 'id')->toArray();
        $this->selectedClassId = array_key_first($this->classes) ?: null;
    }

    public function updatedSelectedSubjectId($subjectId): void
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

            // $this->users = $students->pluck('name', 'id')->toArray();
            $this->users = $students
                ->mapWithKeys(fn ($s) => [
                    $s->id => [
                        'id' => $s->id,
                        'name' => $s->name,
                        'father_name' => $s->father_name ?? null,
                    ],
                ])
                ->toArray();

            $this->getUserAttendance();
        }
    }

    // Add this method after the loadClasses method
    public function updatedSelectedClassId($classId): void
    {
        $this->subjects = [];
        $this->selectedSubjectId = null;
        $this->users = [];
        $this->userAttendance = [];

        if (filled($classId)) {
            $this->subjects = \App\Models\Subject::where('school_class_id', $classId)
                ->pluck('name', 'id')
                ->toArray();
        }
    }

    public function getDates(): array
    {
        $current = Carbon::parse($this->selectedDate);

        return [
            'previous' => Jalalian::fromCarbon($current->copy()->subMonthNoOverflow())->format('%d %B %Y'),
            'current' => Jalalian::fromCarbon($current)->format('%B %Y'),
            'next' => Jalalian::fromCarbon($current->copy()->addMonthNoOverflow())->format('%B %Y'),
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
        $end = Carbon::parse($this->selectedDate)->endOfMonth();

        $userIds = array_keys($this->users);

        $users = User::whereIn('id', $userIds)
            ->with([
                'attendances' => function ($query) use ($start, $end) {
                    $query->whereBetween('date', [$start, $end])
                        ->where('subject_id', $this->selectedSubjectId);
                }
            ])
            ->get();

        $details = [];

        foreach ($users as $user) {
            $date = $start->copy();
            while ($date->lte($end)) {
                $attendance = $user->attendances->firstWhere('date', $date->format('Y-m-d'));

                $details[$user->id][$date->day] = [
                    'status' => $attendance ? ($attendance->status ? true : false) : null,
                    'disabled' => $date->lt(Carbon::today()),
                ];

                $date->addDay();
            }
        }

        $this->userAttendance = $details;
        $this->totalDaysInMonth = $end->day;
    }



    public function saveAttendance(): void
    {
        foreach ($this->userAttendance as $userId => $days) {
            $user = User::find($userId);

            foreach ($days as $day => $entry) {
                $status = $entry['status'] ?? null;
                $date = Carbon::parse($this->selectedDate)->setDay($day)->format('Y-m-d');

                if ($status === true || $status === false) {
                    // Try to find existing attendance record
                    $attendance = $user->attendances()->where([
                        ['teacher_id', $this->selectedTeacherId ?? Auth::id()],
                        ['subject_id', $this->selectedSubjectId],
                        ['date', $date]
                    ])->first();

                    $isNew = false;
                    if (!$attendance) {
                        // Create new attendance record if none exists
                        $attendance = $user->attendances()->make([
                            'teacher_id' => $this->selectedTeacherId ?? Auth::id(),
                            'subject_id' => $this->selectedSubjectId,
                            'date' => $date,
                            'status' => $status,
                        ]);
                        $attendance->save();
                        $isNew = true;
                    } else {
                        // Update existing attendance
                        $attendance->status = $status;
                        $attendance->save();
                    }

                    // Send notification only on create, not update
                    if ($isNew) {
                        try {
                            $appUrl = env('APP_URL');
                            if (!in_array($appUrl, ['http://127.0.0.1:8000', 'http://localhost'])) {
                                $message = $status ? 'You were marked present on ' . $date . '.' : 'You were marked absent on ' . $date . '.';
                                $user->notify(new StatusChanged($message));
                            }
                        } catch (\Exception $e) {
                            Log::error('Failed to send attendance notification: ' . $e->getMessage());
                        }
                    }
                } elseif ($status === null) {
                    $user->attendances()
                        ->where('date', $date)
                        ->where('subject_id', $this->selectedSubjectId)
                        ->where('teacher_id', $this->selectedTeacherId ?? Auth::id())
                        ->delete();
                }
            }
        }

        Notification::make()
            ->title('Attendance saved successfully.')
            ->success()
            ->send();
    }



    protected function getViewData(): array
    {
        return [
            'classes' => $this->classes, // Add this line
            'teachers' => $this->teachers,
            'subjects' => $this->subjects,
            'selectedClassId' => $this->selectedClassId, // Add this line
            'selectedTeacherId' => $this->selectedTeacherId,
            'selectedSubjectId' => $this->selectedSubjectId,
            'dates' => $this->getDates(),
            'users' => $this->users,
            'userAttendance' => $this->userAttendance,
            'totalDaysInMonth' => $this->totalDaysInMonth,
        ];
    }


}
