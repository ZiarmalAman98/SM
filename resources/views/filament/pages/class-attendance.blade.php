@php use Illuminate\Support\Carbon; @endphp

<x-filament-panels::page>
    {{-- Filters --}}
    <div class="flex flex-wrap items-end gap-4 mb-6">
        {{-- Branch Dropdown --}}
        <div class=" sm:w-64">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Branch</label>
            <select wire:model="selectedBranchId" wire:change="$refresh" wire:loading.attr="disabled"
                class="w-full mt-1 border-gray-300 rounded-lg dark:border-gray-700 dark:bg-gray-800">
                <option value="">Choose a branch</option>
                @foreach ($branches as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Class Dropdown --}}
        <div class=" sm:w-64">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Class</label>
            <select wire:model="selectedClassId" wire:change="$refresh" wire:loading.attr="disabled"
                class="w-full mt-1 border-gray-300 rounded-lg dark:border-gray-700 dark:bg-gray-800"
                @if (empty($classes)) disabled @endif>
                <option value="">Choose a class</option>
                @foreach ($classes as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Loading Indicator --}}
    @if ($isLoading)
        <div class="mb-4 text-center">
            <x-filament::loading-indicator class="w-5 h-5" />
        </div>
    @endif

    {{-- Month Navigation --}}
    <div class="mb-4 text-center">
        <x-filament::button wire:click="previousMonth" wire:loading.attr="disabled">
            ← {{ $dates['previous'] }}
        </x-filament::button>
        <x-filament::button color="success" disabled>
            {{ $dates['current'] }}
        </x-filament::button>
        <x-filament::button wire:click="nextMonth" wire:loading.attr="disabled">
            {{ $dates['next'] }} →
        </x-filament::button>
    </div>

    {{-- Attendance Table --}}
    @if (!empty($students))
        <x-filament-tables::container style="overflow: auto">
            <table class="w-full divide-y divide-gray-200 table-auto fi-ta-table text-start">
                <thead class="divide-y divide-gray-200 dark:divide-white/5">
                    <tr>
                        <x-filament-tables::header-cell>Student</x-filament-tables::header-cell>
                        @foreach (range(1, $totalDaysInMonth) as $day)
                            <x-filament-tables::header-cell
                                class="text-center">{{ $day }}</x-filament-tables::header-cell>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                    @foreach ($userAttendance as $userId => $days)
                        <x-filament-tables::row class="!mb-2 border-b border-gray-200 dark:border-gray-700">
                            <x-filament-tables::cell>
                                {{ $students[$userId] ?? 'Unknown Student' }}
                            </x-filament-tables::cell>

                            @foreach ($days as $day => $entry)
                                @php
                                    $isDisabled = $entry['disabled'] ?? false;
                                    $leaveSickData = $userLeaveSick[$userId][$day] ?? [];
                                @endphp
                                <x-filament-tables::cell class="py-3 text-center">
                                    <div class="flex flex-col items-center gap-2" x-data="{
                                        morning: @entangle('userAttendance.' . $userId . '.' . $day . '.morning'),
                                        afternoon: @entangle('userAttendance.' . $userId . '.' . $day . '.afternoon'),
                                        isLeave: @entangle('userLeaveSick.' . $userId . '.' . $day . '.is_leave'),
                                        isSick: @entangle('userLeaveSick.' . $userId . '.' . $day . '.is_sick'),
                                        disabled: {{ $isDisabled ? 'true' : 'false' }}
                                    }">
                                        {{-- Morning --}}
                                        <div x-bind:class="{
                                            'cursor-pointer': !disabled && !isSick && !isLeave,
                                            'opacity-60': disabled || isSick || isLeave,
                                            'inline-flex items-center justify-center w-7 h-7 border rounded text-sm font-semibold': true,
                                            'bg-white border-gray-300 text-gray-800 dark:text-white': morning === null,
                                            'bg-green-100 border-green-600 text-green-800 dark:bg-green-500 dark:text-white': morning ===
                                                true,
                                            'bg-red-100 border-red-600 text-red-700 dark:bg-red-500 dark:text-white': morning ===
                                                false
                                        }"
                                            x-on:click="if (!disabled && !isSick && !isLeave) morning = morning === true ? false : (morning === false ? null : true)"
                                            title="Morning">
                                            <template x-if="morning === null"><span>&nbsp;</span></template>
                                            <template x-if="morning === true"><span>✓</span></template>
                                            <template x-if="morning === false"><span>×</span></template>
                                        </div>

                                        {{-- Afternoon --}}
                                        <div x-bind:class="{
                                            'cursor-pointer': !disabled && !isSick && !isLeave,
                                            'opacity-60': disabled || isSick || isLeave,
                                            'inline-flex items-center justify-center w-7 h-7 border rounded text-sm font-semibold': true,
                                            'bg-white border-gray-300 text-gray-800 dark:text-white': afternoon ===
                                                null,
                                            'bg-green-100 border-green-600 text-green-800 dark:bg-green-500 dark:text-white': afternoon ===
                                                true,
                                            'bg-red-100 border-red-600 text-red-700 dark:bg-red-500 dark:text-white': afternoon ===
                                                false
                                        }"
                                            x-on:click="if (!disabled && !isSick && !isLeave) afternoon = afternoon === true ? false : (afternoon === false ? null : true)"
                                            title="Afternoon">
                                            <template x-if="afternoon === null"><span>&nbsp;</span></template>
                                            <template x-if="afternoon === true"><span>✓</span></template>
                                            <template x-if="afternoon === false"><span>×</span></template>
                                        </div>

                                        {{-- Leave/Sick Options --}}
                                        <div class="flex flex-col gap-1 mt-1">
                                            {{-- Sick Option --}}
                                            <button x-bind:class="{
                                                'cursor-pointer': !disabled,
                                                'opacity-50 cursor-not-allowed': disabled,
                                                'px-2 py-1 text-xs font-medium rounded border transition-all duration-200 w-full': true,
                                                'bg-white border-gray-300 text-gray-600 hover:border-orange-300 hover:text-orange-600': !isSick && !disabled,
                                                'bg-orange-100 border-orange-500 text-orange-700': isSick,
                                                'bg-gray-100 border-gray-200 text-gray-400': disabled
                                            }"
                                                x-on:click="if (!disabled) {
                                                    isSick = !isSick;
                                                    if (isSick) {
                                                        isLeave = false;
                                                        morning = false;
                                                        afternoon = false;
                                                    }
                                                }"
                                                title="Mark as Sick">
                                                Sick
                                            </button>

                                            {{-- Leave Option --}}
                                            <button x-bind:class="{
                                                'cursor-pointer': !disabled,
                                                'opacity-50 cursor-not-allowed': disabled,
                                                'px-2 py-1 text-xs font-medium rounded border transition-all duration-200 w-full': true,
                                                'bg-white border-gray-300 text-gray-600 hover:border-blue-300 hover:text-blue-600': !isLeave && !disabled,
                                                'bg-blue-100 border-blue-500 text-blue-700': isLeave,
                                                'bg-gray-100 border-gray-200 text-gray-400': disabled
                                            }"
                                                x-on:click="if (!disabled) {
                                                    isLeave = !isLeave;
                                                    if (isLeave) {
                                                        isSick = false;
                                                        morning = false;
                                                        afternoon = false;
                                                    }
                                                }"
                                                title="Mark as Leave">
                                                Leave
                                            </button>
                                        </div>
                                    </div>
                                </x-filament-tables::cell>
                            @endforeach
                        </x-filament-tables::row>
                    @endforeach
                </tbody>
            </table>
        </x-filament-tables::container>

        {{-- Save Button --}}
        <div class="mt-4 text-center">
            <x-filament::button wire:click="saveAttendance" wire:loading.attr="disabled">
                <span wire:loading.remove>Save Attendance</span>
                <span wire:loading>Saving...</span>
            </x-filament::button>
        </div>
    @else
        <div class="mt-6 text-center text-gray-500 dark:text-gray-400">
            @if ($selectedBranchId && empty($classes))
                No classes found for selected branch.
            @elseif($selectedClassId && empty($students))
                No active students found in selected class.
            @else
                Please select a branch and class to view attendance.
            @endif
        </div>
    @endif
</x-filament-panels::page>
