<x-filament-panels::page>
    <div class="flex items-end gap-4 mb-6">
        {{-- Employee Dropdown --}}
        <div class="w-64">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Select Employee
            </label>
            <select wire:change="loadAttendanceData"
                wire:model="selectedEmployeeId"
                class="fi-input w-full mt-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                <option value="">Choose an employee</option>
                @foreach ($employees as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Month Navigation --}}
    <div class="text-center mb-4">
        <x-filament::button wire:click="previousMonth">← {{ $dates['previous'] }}</x-filament::button>
        <x-filament::button color="success" disabled>{{ $dates['current'] }}</x-filament::button>
        <x-filament::button wire:click="nextMonth">{{ $dates['next'] }} →</x-filament::button>
    </div>

    @if ($selectedEmployeeId && count($users))
        <x-filament-tables::container style="overflow: auto">
            <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start">
                <thead class="divide-y divide-gray-200 dark:divide-white/5">
                    <x-filament-tables::header-cell>Employee</x-filament-tables::header-cell>
                    @foreach (range(1, $totalDaysInMonth) as $day)
                        <x-filament-tables::header-cell class="text-center">{{ $day }}</x-filament-tables::header-cell>
                    @endforeach
                </thead>
                <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white">
                    @foreach ($userAttendance as $userId => $days)
                        <x-filament-tables::row>
                            <x-filament-tables::cell>{{ $users[$userId] ?? 'Unknown Employee' }}</x-filament-tables::cell>

                            @foreach ($days as $day => $entry)
                                <x-filament-tables::cell class="text-center">
                                    <div
                                        x-data="{ state: @entangle('userAttendance.' . $userId . '.' . $day . '.status') }"
                                        :class="{
                                            'cursor-pointer': !{{ $entry['disabled'] ? 'true' : 'false' }},
                                            'opacity-60': {{ $entry['disabled'] ? 'true' : 'false' }},
                                            'inline-flex items-center justify-center w-8 h-8 border rounded text-sm font-semibold': true,
                                            'bg-white border-gray-300 text-gray-800 dark:text-white': state === null,
                                            'bg-green-100 border-green-600 text-green-800 dark:bg-green-500 dark:text-white': state === true,
                                            'bg-red-100 border-red-600 text-red-700 dark:bg-red-500 dark:text-white': state === false,
                                        }"
                                        @click="
                                            if ({{ $entry['disabled'] ? 'true' : 'false' }}) return;
                                            if (state === null) {
                                                state = true;
                                            } else if (state === true) {
                                                state = false;
                                            } else {
                                                state = null;
                                            }
                                        "
                                    >
                                        <template x-if="state === null"><span>&nbsp;</span></template>
                                        <template x-if="state === true"><span>✓</span></template>
                                        <template x-if="state === false"><span>×</span></template>
                                    </div>
                                </x-filament-tables::cell>
                            @endforeach
                        </x-filament-tables::row>
                    @endforeach
                </tbody>
            </table>
        </x-filament-tables::container>

        <div class="text-center mt-4">
            <x-filament::button wire:click="saveAttendance" wire:loading.attr="disabled">
                <span wire:loading.remove>Save Attendance</span>
                <span wire:loading>Saving...</span>
            </x-filament::button>
        </div>
    @else
        <div class="text-center text-gray-500 dark:text-gray-400 mt-6">
            Please select an employee to view attendance.
        </div>
    @endif
</x-filament-panels::page>
