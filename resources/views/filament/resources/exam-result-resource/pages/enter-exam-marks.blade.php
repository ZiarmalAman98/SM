<x-filament::page>
    <div class="space-y-6">
        {{-- Header --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="px-6 py-6 border-b border-gray-200 dark:border-white/10 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-gray-800 dark:to-gray-700 rounded-t-xl">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-primary-100 dark:bg-primary-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Student Results Management
                        </h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Select branch, class, exam, and subject to enter detailed student marks
                        </p>
                    </div>
                </div>
            </div>

            {{-- Filter Form --}}
            <div class="px-6 py-6 bg-gray-50/50 dark:bg-gray-800/50">
                {{ $this->form }}
            </div>
        </div>

        {{-- Student Marks Entry --}}
        @if ($students && count($students))
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <form wire:submit.prevent="save">
                    <div class="px-6 pt-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Enter Marks for Students
                            </h3>
                            @if($exam_type)
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Max Total Marks:</span>
                                    <span class="px-3 py-1 bg-primary-100 text-primary-800 rounded-lg dark:bg-primary-900/20 dark:text-primary-400 font-semibold">
                                        {{ $exam_type === 'mid_term' ? '40' : ($exam_type === 'final' ? '60' : '100') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                       <div class="w-full overflow-x-auto border border-gray-200 dark:border-white/10 rounded-lg shadow-sm">
    <table class="w-full text-sm divide-y divide-gray-200 dark:divide-white/10">
        <thead class="text-xs font-semibold text-center text-gray-700 uppercase bg-gradient-to-r from-primary-50 to-primary-100 dark:from-gray-800 dark:to-gray-700 dark:text-gray-200">
            <tr>
                <th class="px-6 py-4 text-left">Student Information</th>
                <th class="px-4 py-4 text-center">Activity<br><span class="text-xs font-normal text-gray-500 dark:text-gray-400"></span></th>
                <th class="px-4 py-4 text-center">Written<br><span class="text-xs font-normal text-gray-500 dark:text-gray-400"></span></th>
                <th class="px-4 py-4 text-center">Recital<br><span class="text-xs font-normal text-gray-500 dark:text-gray-400"></span></th>
                <th class="px-4 py-4 text-center">Homework<br><span class="text-xs font-normal text-gray-500 dark:text-gray-400"></span></th>
                <th class="px-4 py-4 text-center bg-primary-100 dark:bg-primary-900/20">Total Marks<br><span class="text-xs font-normal text-primary-600 dark:text-primary-400"></span></th>
                <th class="px-4 py-4 text-center">Mark in Words</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-white/10">
            @foreach ($students as $student)
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <div class="font-semibold text-gray-900 dark:text-white">
                                {{ $student->name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Father: {{ $student->father_name ?? 'N/A' }}
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-4">
                        <div class="flex flex-col items-center">
                            <input
                                type="number"
                                wire:model.live="class_activity_marks.{{ $student->id }}"
                                class="w-20 px-3 py-2 text-sm text-center text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-white/20 dark:text-white dark:focus:ring-primary-400 dark:focus:border-primary-400"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="0"
                            />
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-col items-center">
                            <input
                                type="number"
                                wire:model.live="written_marks.{{ $student->id }}"
                                class="w-20 px-3 py-2 text-sm text-center text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-white/20 dark:text-white dark:focus:ring-primary-400 dark:focus:border-primary-400"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="0"
                            />
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-col items-center">
                            <input
                                type="number"
                                wire:model.live="recital_marks.{{ $student->id }}"
                                class="w-20 px-3 py-2 text-sm text-center text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-white/20 dark:text-white dark:focus:ring-primary-400 dark:focus:border-primary-400"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="0"
                            />
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-col items-center">
                            <input
                                type="number"
                                wire:model.live="homework_marks.{{ $student->id }}"
                                class="w-20 px-3 py-2 text-sm text-center text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-white/20 dark:text-white dark:focus:ring-primary-400 dark:focus:border-primary-400"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="0"
                            />
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-col items-center">
                            @php
                                $totalMarks = $marks[$student->id] ?? 0;
                                $maxMarks = $exam_type === 'mid_term' ? 40 : ($exam_type === 'final' ? 60 : 100);
                                $isOverLimit = $totalMarks > $maxMarks;
                            @endphp
                            <input
                                type="number"
                                wire:model="marks.{{ $student->id }}"
                                class="w-20 px-3 py-2 text-sm text-center border rounded-lg shadow-sm font-semibold {{ $isOverLimit ? 'text-red-900 bg-red-100 border-red-300 dark:bg-red-900 dark:border-red-500 dark:text-red-100' : 'text-gray-900 bg-primary-50 border-primary-200 dark:bg-gray-800 dark:border-gray-600 dark:text-white' }}"
                                readonly
                                placeholder="0"
                            />
                            @if($isOverLimit)
                                <div class="text-xs text-red-600 dark:text-red-400 mt-1 font-medium">
                                    Exceeds {{ $maxMarks }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-col items-center">
                            <input
                                type="text"
                                wire:model.defer="mark_in_words.{{ $student->id }}"
                                class="w-32 px-3 py-2 text-sm text-center text-gray-900 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-800 dark:border-white/20 dark:text-white dark:focus:ring-primary-400 dark:focus:border-primary-400"
                                placeholder="e.g. Eighty Five"
                            />
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

                    </div>

                    {{-- Footer Buttons --}}
                    <div class="px-6 py-6 border-t border-gray-200 dark:border-white/10 bg-gradient-to-r from-gray-50/50 to-primary-50/50 dark:from-gray-800/50 dark:to-gray-700/50 rounded-b-xl">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">{{ count($students) }}</span> students found
                            </div>
                            <div class="flex space-x-3">
                                {{-- <button type="button"
                                    class="fi-btn fi-btn-color-gray fi-btn-size-sm inline-flex items-center justify-center gap-1.5 rounded-lg font-semibold outline-none transition duration-75 focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-70 fi-btn-color-gray fi-btn-size-sm bg-white text-gray-950 shadow-sm ring-1 ring-gray-950/10 hover:bg-gray-50 focus-visible:ring-primary-500 dark:bg-gray-900 dark:text-white dark:ring-white/20 dark:hover:bg-gray-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Reset
                                </button> --}}
                                <x-filament::button type="submit"
                                    class="fi-btn fi-btn-color-primary fi-btn-size-sm inline-flex items-center justify-center gap-1.5 rounded-lg font-semibold outline-none transition duration-75 focus-visible:ring-2 disabled:pointer-events-none disabled:opacity-70 fi-btn-color-primary fi-btn-size-sm bg-primary-600 text-white shadow-sm ring-1 ring-primary-600/10 hover:bg-primary-500 focus-visible:ring-primary-500 dark:bg-primary-500 dark:text-white dark:ring-primary-500/10 dark:hover:bg-primary-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Save Results
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif

    </div>
</x-filament::page>
