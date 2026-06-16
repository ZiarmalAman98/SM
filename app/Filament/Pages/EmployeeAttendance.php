<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Subject;
use Filament\Pages\Page;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;


class EmployeeAttendance extends Page
{
    public static function canAccess(): bool
    {
        return Auth::user()->hasRole('super_admin');
    }

    public static function getNavigationGroup(): string
    {
        return __('Attendance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Employee Attendance');
    }
    public static function getNavigationSort(): ?int
    {
        return 5;
    }

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static string $view = 'filament.pages.employee-attendance';
    protected ?string $maxContentWidth = 'full';


    public ?string $selectedDate = null;
    public array $employees = [];
    public ?int $selectedEmployeeId = null;
    public array $userAttendance = [];
    public int $totalDaysInMonth = 0;

    public function mount(): void
    {
        $this->selectedDate = now()->format('Y-m');
        $this->employees = User::where('type', 'staff')->orWhere('type', 'teacher')
            ->pluck('name', 'id')
            ->toArray();
        $this->selectedEmployeeId = array_key_first($this->employees);
        $this->loadAttendanceData();
    }

    public function loadAttendanceData(): void
    {
        $start = Carbon::parse($this->selectedDate)->startOfMonth();
        $end = Carbon::parse($this->selectedDate)->endOfMonth();

        $employeeIds = array_keys($this->employees);

        $employees = User::whereIn('id', $employeeIds)
            ->with([
                'employeeAttendances' => function ($query) use ($start, $end) {
                    $query->whereBetween('date', [$start, $end]);
                }
            ])
            ->get();

        $details = [];

        foreach ($employees as $employee) {
            $date = $start->copy();
            while ($date->lte($end)) {
                $attendance = $employee->employeeAttendances->firstWhere('date', $date->format('Y-m-d'));

                $details[$employee->id][$date->day] = [
                    'status' => $attendance ? ($attendance->status ? true : false) : null,
                    'disabled' => $date->lt(Carbon::today()),
                ];

                $date->addDay();
            }
        }

        $this->userAttendance = $details;
        $this->totalDaysInMonth = $end->day;
    }

    public function previousMonth(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->subMonthNoOverflow()->format('Y-m');
        $this->loadAttendanceData();
    }

    public function nextMonth(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate)->addMonthNoOverflow()->format('Y-m');
        $this->loadAttendanceData();
    }

    public function saveAttendance(): void
    {
        foreach ($this->userAttendance as $employeeId => $days) {
            $employee = User::find($employeeId);

            foreach ($days as $day => $entry) {
                $status = $entry['status'] ?? null;
                $date = Carbon::parse($this->selectedDate)->setDay($day)->format('Y-m-d');

                if ($status === true || $status === false) {
                    \App\Models\EmployeeAttendance::updateOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'date' => $date,
                        ],
                        ['status' => $status]
                    );
                } elseif ($status === null) {
                    \App\Models\EmployeeAttendance::where('date', $date)
                        ->where('employee_id', $employeeId)
                        ->delete();
                }
            }
        }

        Notification::make()
            ->title(__('Attendance saved successfully'))
            ->success()
            ->send();
    }

    protected function getViewData(): array
    {
        return [
            'employees' => $this->employees,
            'selectedEmployeeId' => $this->selectedEmployeeId,
            'dates' => $this->getDates(),
            'users' => $this->employees,
            'userAttendance' => $this->userAttendance,
            'totalDaysInMonth' => $this->totalDaysInMonth,
        ];
    }

    public function getDates(): array
    {
        $current = Carbon::parse($this->selectedDate);

        return [
            'previous' => Jalalian::fromCarbon($current->copy()->subMonthNoOverflow())->format(' %B %Y'),
            'current' => Jalalian::fromCarbon($current)->format('%B %Y'),
            'next' => Jalalian::fromCarbon($current->copy()->addMonthNoOverflow())->format('%B %Y'),
        ];
    }
}
