<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Branch;
use Filament\Pages\Page;
use App\Models\SchoolClass;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Carbon;
use App\Models\ClassAttendance;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;

class BranchClassAttendance extends Page
{
    use InteractsWithFormActions;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static string $view = 'filament.pages.class-attendance';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return __('Attendance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Class Attendance');
    }

    public ?string $selectedDate = null;
    public ?int $selectedBranchId = null;
    public ?int $selectedClassId = null;

    public array $branches = [];
    public array $classes = [];
    public array $students = [];
    public array $userAttendance = [];
    public array $userLeaveSick = [];
    public int $totalDaysInMonth = 1;
    public bool $isLoading = false;

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m');
        $this->loadBranches();
    }

    protected function loadBranches(): void
    {
        $this->branches = Branch::query()
            ->orderBy('branch_name')
            ->pluck('branch_name', 'id')
            ->toArray();
    }

    public function updatedSelectedBranchId($value): void
    {
        $this->saveAttendanceIfNeeded();
        $this->reset(['classes', 'selectedClassId', 'students', 'userAttendance', 'userLeaveSick']);

        if (empty($value)) return;

        $this->isLoading = true;
        $this->loadClasses();
        $this->isLoading = false;
    }

    protected function loadClasses(): void
    {
        $this->classes = SchoolClass::query()
            ->where('branch_id', $this->selectedBranchId)
            ->orderBy('class_name')
            ->pluck('class_name', 'id')
            ->toArray();

        $this->reset(['selectedClassId', 'students', 'userAttendance', 'userLeaveSick']);
    }

    public function updatedSelectedClassId($value): void
    {
        $this->saveAttendanceIfNeeded();
        $this->reset(['students', 'userAttendance', 'userLeaveSick']);

        if (empty($value)) return;

        $this->isLoading = true;
        $this->loadStudents();
        $this->isLoading = false;
    }

    protected function loadStudents(): void
    {
        $this->students = User::query()
            ->where('type', 'student')
            ->whereHas(
                'studentClasses',
                fn($q) => $q
                    ->where('class_id', $this->selectedClassId)
                    ->where('status', 'active')
            )
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->getUserAttendance();
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
        $this->selectedDate = Carbon::parse($this->selectedDate)
            ->subMonthNoOverflow()
            ->format('Y-m');

        $this->getUserAttendance();
    }

    public function nextMonth(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)
            ->addMonthNoOverflow()
            ->format('Y-m');

        $this->getUserAttendance();
    }

    protected function getUserAttendance(): void
    {
        if (empty($this->selectedClassId) || empty($this->students)) {
            $this->userAttendance = [];
            return;
        }

        $startDate = Carbon::parse($this->selectedDate)->startOfMonth();
        $endDate = Carbon::parse($this->selectedDate)->endOfMonth();
        $today = Carbon::today();

        $attendanceRecords = ClassAttendance::query()
            ->where('class_id', $this->selectedClassId)
            ->where('branch_id', $this->selectedBranchId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(['student_id', fn($item) => Carbon::parse($item->date)->day]);

        $details = [];
        $leaveSickDetails = [];

        foreach ($this->students as $studentId => $name) {
            $date = $startDate->copy();

            while ($date->lte($endDate)) {
                $day = $date->day;
                $record = data_get($attendanceRecords, "$studentId.$day.0", null);

                if ($record) {
                    $isLeave = (bool) $record->is_leave;
                    $isSick = (bool) $record->is_sick;

                    // If sick or leave, mark as absent
                    if ($isLeave || $isSick) {
                        $morning = false;
                        $afternoon = false;
                    } else {
                        $morning = (bool) $record->morning;
                        $afternoon = (bool) $record->afternoon;
                    }
                } elseif ($date->isToday()) {
                    $yesterday = $date->copy()->subDay();
                    $yesterdayDay = $yesterday->day;
                    $yesterdayRecord = data_get($attendanceRecords, "$studentId.$yesterdayDay.0", null);
                    $morning = $yesterdayRecord?->status === 'present' ? true : ($yesterdayRecord?->status === 'absent' ? false : null);
                    $afternoon = $morning;
                    $isLeave = false;
                    $isSick = false;
                } else {
                    $morning = null;
                    $afternoon = null;
                    $isLeave = false;
                    $isSick = false;
                }

                $details[$studentId][$day] = [
                    'morning' => $morning,
                    'afternoon' => $afternoon,
                    'disabled' => $date->lt($today),
                ];

                $leaveSickDetails[$studentId][$day] = [
                    'is_leave' => $isLeave,
                    'is_sick' => $isSick,
                    'disabled' => $date->lt($today),
                ];

                $date->addDay();
            }
        }

        $this->userAttendance = $details;
        $this->userLeaveSick = $leaveSickDetails;
        $this->totalDaysInMonth = $endDate->day;
    }

    public function saveAttendance(): void
    {
        $this->saveAttendanceIfNeeded();

        Notification::make()
            ->title('Attendance saved successfully!')
            ->success()
            ->send();
    }

    protected function saveAttendanceIfNeeded(): void
    {
        if (empty($this->userAttendance) || empty($this->selectedClassId) || empty($this->selectedBranchId)) {
            return;
        }

        foreach ($this->userAttendance as $studentId => $days) {
            foreach ($days as $day => $entry) {
                $morning = $entry['morning'] ?? null;
                $afternoon = $entry['afternoon'] ?? null;

                if ($morning === null && $afternoon === null) continue;

                $status = match ([$morning, $afternoon]) {
                    [true, true] => 'present',
                    [false, false] => 'absent',
                    default => 'partial',
                };

                $date = Carbon::parse($this->selectedDate)->setDay($day)->format('Y-m-d');

                $existing = ClassAttendance::where('student_id', $studentId)
                    ->where('class_id', $this->selectedClassId)
                    ->where('branch_id', $this->selectedBranchId)
                    ->where('date', $date)
                    ->first();

                // Get leave/sick data for this day
                $leaveSickData = $this->userLeaveSick[$studentId][$day] ?? [];
                $isLeave = $leaveSickData['is_leave'] ?? false;
                $isSick = $leaveSickData['is_sick'] ?? false;

                // If sick or leave is selected, force attendance to absent
                if ($isLeave || $isSick) {
                    $morning = false;
                    $afternoon = false;
                    $status = 'absent';
                }

                $attendanceData = [
                    'student_id' => $studentId,
                    'class_id' => $this->selectedClassId,
                    'branch_id' => $this->selectedBranchId,
                    'date' => $date,
                    'morning' => $morning,
                    'afternoon' => $afternoon,
                    'status' => $status,
                    'is_leave' => $isLeave,
                    'is_sick' => $isSick,
                ];

                if (!$existing) {
                    ClassAttendance::create($attendanceData);
                } else {
                    $existing->update($attendanceData);
                }
            }
        }

        Notification::make()
            ->title('Attendance auto-saved.')
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'branches' => $this->branches,
            'classes' => $this->classes,
            'students' => $this->students,
            'userAttendance' => $this->userAttendance,
            'userLeaveSick' => $this->userLeaveSick,
            'dates' => $this->getDates(),
            'totalDaysInMonth' => $this->totalDaysInMonth,
            'isLoading' => $this->isLoading,
        ];
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->hasRole('super_admin');
    }
}
