@php
    use Illuminate\Support\Carbon;
@endphp

<x-filament-panels::page>
    <x-slot name="header">
        <h1 class="text-2xl font-bold tracking-tight">Take Attendance</h1>
    </x-slot>

    {{-- Subject Dropdown --}}
    <div class="w-64 mb-6">
        <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Select Subject
        </label>
        <select
            id="subject"
            wire:model.live="selectedSubjectId"
            class="fi-input w-full mt-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800"
            {{ empty($subjects) ? 'disabled' : '' }}
        >
            <option value="">Choose a subject</option>
            @foreach ($subjects as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Month Navigation --}}
    <div class="text-center mb-4">
        <x-filament::button wire:click="previousMonth">
            ← {{ $this->getDates()['previous'] }}
        </x-filament::button>

        <x-filament::button disabled color="success">
            {{ $this->getDates()['current'] }}
        </x-filament::button>

        <x-filament::button wire:click="nextMonth">
            {{ $this->getDates()['next'] }} →
        </x-filament::button>
    </div>

    {{-- Attendance Table --}}
    @if ($selectedSubjectId && count($users))
        <x-filament-tables::container style="overflow: auto">
            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start">
                <thead>
                    <tr>
                        <x-filament-tables::header-cell>Student</x-filament-tables::header-cell>
                        @foreach (range(1, $totalDaysInMonth) as $day)
                            <x-filament-tables::header-cell class="text-center">
                                {{ $day }}
                            </x-filament-tables::header-cell>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white">
                    @foreach ($userAttendance as $userId => $days)
                        <x-filament-tables::row>
                            <x-filament-tables::cell>
                                {{ $users[$userId] ?? 'Unknown' }}
                            </x-filament-tables::cell>

                            @foreach ($days as $day => $triState)
                            @php
                                $attendanceDate = \Illuminate\Support\Carbon::parse($this->selectedDate)->setDay((int)$day);
                                // If $attendanceDate is strictly less than today's date, it’s in the past
                                $isDisabled = $attendanceDate->lt(\Carbon\Carbon::today());
                            @endphp


                                <x-filament-tables::cell class="text-center">
                                    <!--
                                        Alpine tri-state container
                                        We'll store the current cell's state in an Alpine variable
                                        that is "entangled" with userAttendance[userId][day].
                                    -->
                                    <div
                                        x-data="{
                                            state: @entangle('userAttendance.' . $userId . '.' . $day)
                                        }"
                                        {{-- We use :class to set a base style, plus dynamic background colors --}}
                                        :class="{
                                            'cursor-pointer': {{ $isDisabled ? 'false' : 'true' }},
                                            'opacity-60': {{ $isDisabled ? 'true' : 'false' }},
                                            
                                            // Base styles
                                            'inline-flex items-center justify-center w-8 h-8 border rounded': true,
                                            
                                            // Background colors depending on state
                                            'bg-white border-gray-300': state === null,
                                            'bg-green-500 border-green-600': state === true,
                                            'bg-red-500 border-red-600': state === false,
                                        }"
                                        {{-- If not disabled, cycle state on click --}}
                                        @click="
                                            if ({{ $isDisabled ? 'true' : 'false' }}) return;

                                            if (state === null) {
                                                state = true;
                                            } else if (state === true) {
                                                state = false;
                                            } else {
                                                state = null;
                                            }
                                        "
                                    >
                                        <!-- If state is null, show blank -->
                                        <template x-if="state === null">
                                            <span>&nbsp;</span>
                                        </template>

                                        <!-- If state is true, show checkmark -->
                                        <template x-if="state === true">
                                            <span class="text-red font-bold">✓</span>
                                        </template>

                                        <!-- If state is false, show X -->
                                        <template x-if="state === false">
                                            <span class="text-red font-bold">×</span>
                                        </template>
                                    </div>
                                </x-filament-tables::cell>
                            @endforeach
                        </x-filament-tables::row>
                    @endforeach
                </tbody>
            </table>
        </x-filament-tables::container>

        <div class="text-center mt-4">
            <x-filament::button wire:click="saveAttendance" color="primary">
                Save Attendance
            </x-filament::button>
        </div>
    @else
        <div class="text-center text-gray-500 dark:text-gray-400 mt-6">
            Please select a subject to view attendance.
        </div>
    @endif

</x-filament-panels::page>
