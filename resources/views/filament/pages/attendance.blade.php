@php
    use Illuminate\Support\Carbon;
@endphp

<x-filament-panels::page>

    {{-- Replace the existing dropdown section with this --}}
    <div class="flex items-end gap-4 mb-6">
        {{-- Class Dropdown --}}
        <div class="w-64">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Select Class
            </label>
            <select wire:model.live="selectedClassId"
                class="w-full mt-1 border-gray-300 rounded-lg fi-input dark:border-gray-700 dark:bg-gray-800">
                <option value="">Choose a class</option>
                @foreach ($classes as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Teacher Dropdown --}}
        <div class="w-64">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Select Teacher
            </label>
            <select wire:change="loadSubjects($event.target.value)"
                wire:model="selectedTeacherId"
                class="w-full mt-1 border-gray-300 rounded-lg fi-input dark:border-gray-700 dark:bg-gray-800">
                <option value="">Choose a teacher</option>
                @foreach ($teachers as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Subject Dropdown --}}
        <div class="w-64">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                Select Subject
            </label>
            <select wire:model.live="selectedSubjectId"
                class="w-full mt-1 border-gray-300 rounded-lg fi-input dark:border-gray-700 dark:bg-gray-800"
                {{ empty($subjects) ? 'disabled' : '' }}>
                <option value="">Choose a subject</option>
                @foreach ($subjects as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    



    {{-- Month navigation --}}
    <div class="mb-4 text-center">
        <x-filament::button wire:click="previousMonth">← {{ $dates['previous'] }}</x-filament::button>
        <x-filament::button color="success" disabled>{{ $dates['current'] }}</x-filament::button>
        <x-filament::button wire:click="nextMonth">{{ $dates['next'] }} →</x-filament::button>
    </div>

    @if ($selectedSubjectId && count($users))
        <x-filament-tables::container style="overflow: auto">
            <table class="w-full divide-y divide-gray-200 table-auto fi-ta-table text-start">
                <thead class="divide-y divide-gray-200 dark:divide-white/5">
                    {{-- Force left alignment with inline styles --}}
                    <x-filament-tables::header-cell style="text-align: left !important; padding-left: 16px;">Student ID</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell style="text-align: left !important; padding-left: 16px;">Student Name</x-filament-tables::header-cell>
                    <x-filament-tables::header-cell style="text-align: left !important; padding-left: 16px;">Father Name</x-filament-tables::header-cell>
                    
                    @foreach (range(1, $totalDaysInMonth) as $day)
                        <x-filament-tables::header-cell class="text-center">{{ $day }}</x-filament-tables::header-cell>
                    @endforeach
                </thead>
                <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white">
                    @foreach ($userAttendance as $userId => $days)
                        <x-filament-tables::row>
                            {{-- Student ID Column - Force Left Aligned --}}
                            <x-filament-tables::cell style="text-align: left !important; padding-left: 16px;" class="font-medium">
                                @php $userData = $users[$userId] ?? null; @endphp
                                {{ $userData['id'] ?? $userId }}
                            </x-filament-tables::cell>
                            
                            {{-- Student Name Column - Force Left Aligned --}}
                            <x-filament-tables::cell style="text-align: left !important; padding-left: 16px;" class="font-medium">
                                @php $userData = $users[$userId] ?? null; @endphp
                                {{ is_array($userData) ? ($userData['name'] ?? 'Unknown Student') : ($userData ?? 'Unknown Student') }}
                            </x-filament-tables::cell>
                            
                            {{-- Father Name Column - Force Left Aligned --}}
                            <x-filament-tables::cell style="text-align: left !important; padding-left: 16px;">
                                @php $userData = $users[$userId] ?? null; @endphp
                                {{ is_array($userData) ? ($userData['father_name'] ?? '-') : '-' }}
                            </x-filament-tables::cell>

                            {{-- Attendance days - Center Aligned (unchanged) --}}
                            @foreach ($days as $day => $entry)
                                @php
                                    $isDisabled = $entry['disabled'] ?? false;
                                @endphp
                                <x-filament-tables::cell class="text-center">
                                    <div
                                        x-data="{ state: @entangle('userAttendance.' . $userId . '.' . $day . '.status') }"
                                        :class="{
                                            'cursor-pointer': {{ $isDisabled ? 'false' : 'true' }},
                                            'opacity-60': {{ $isDisabled ? 'true' : 'false' }},
                                            'inline-flex items-center justify-center w-8 h-8 border rounded text-sm font-semibold': true,

                                            // Backgrounds & text colors
                                            'bg-white border-gray-300 text-gray-800 dark:text-white': state === null,
                                            'bg-green-100 border-green-600 text-green-800 dark:bg-green-500 dark:text-white': state === true,
                                            'bg-red-100 border-red-600 text-red-700 dark:bg-red-500 dark:text-white': state === false,
                                        }"
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

        <div class="mt-4 text-center">
            <x-filament::button wire:click="saveAttendance" wire:loading.attr="disabled">
                <span wire:loading.remove>Save Attendance</span>
                <span wire:loading>Saving...</span>
            </x-filament::button>
        </div>
    @else
        <div class="mt-6 text-center text-gray-500 dark:text-gray-400">
            Please select a subject to view attendance.
        </div>
    @endif

    {{-- Add custom CSS to ensure left alignment --}}
    <style>
        .force-left-align {
            text-align: left !important;
            padding-left: 16px !important;
        }
        [style*="text-align: left"] {
            text-align: left !important;
        }
    </style>
</x-filament-panels::page>