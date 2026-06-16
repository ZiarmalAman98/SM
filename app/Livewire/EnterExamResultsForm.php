<div class="p-4 mx-auto text-black bg-white shadow-sm dark:bg-gray-900 dark:text-white rounded-lg"
    style="max-width: 1500px;">
    <div class="flex flex-col items-start justify-end gap-4 mb-8 md:flex-row md:items-center border-b pb-4">
        <!-- Save Button -->
        <button wire:click='save' type="button"
            class="text-gray-800 dark:text-gray-200 bg-blue-600 dark:bg-blue-700 rounded-lg px-5 py-2.5 text-sm font-semibold transition-colors duration-300 ease-in-out">
            <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ __('Save') }}
            </span>
        </button>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2">
        <div>
            <label class="block mb-2 font-medium">Select Exam</label>
            <select wire:model.live="exam_id" class="w-full p-2 border rounded">
                <option value="">-- Select Exam --</option>
                @foreach ($exams as $exam)
                <option value="{{ $exam->id }}">{{ $exam->name }} ({{ $exam->exam_type }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block mb-2 font-medium">Select Class</label>
            <select wire:model.live="class_id" class="w-full p-2 border rounded">
                <option value="">-- Select Class --</option>
                @foreach ($classes as $class)
                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if (session()->has('message'))
    <div class="p-4 mb-4 text-green-800 bg-green-200 rounded">
        {{ session('message') }}
    </div>
    @endif

    @if (!empty($students))
    <form wire:submit.prevent="save">
        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">Unique ID</th>
                        <th class="p-2">Student Name</th>
                        <th class="p-2">Father Name</th>
                        <th class="p-2">Marks</th>
                        <th class="p-2">Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                    @php
                    $mark = $marks[$student->id] ?? null;
                    $grade = \App\Models\GradeSystem::where('from', '<=', $mark) ->where('to', '>=', $mark)
                        ->value('title');
                        @endphp
                        <tr>
                            <td class="p-2">{{ $student->unique_id }}</td>
                            <td class="p-2">{{ $student->name }}</td>
                            <td class="p-2">{{ $student->father_name }}</td>
                            <td class="p-2">
                                <input type="number" wire:model.defer="marks.{{ $student->id }}"
                                    class="w-24 p-1 border rounded" min="0" max="100">
                            </td>
                            <td class="p-2">{{ $grade ?? '-' }}</td>
                        </tr>
                        @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-right">
            <button type="submit" class="px-6 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
                Save Results
            </button>
        </div>
    </form>
    @elseif ($exam_id && $class_id)
    <div class="p-4 mt-4 text-yellow-800 bg-yellow-100 rounded">
        No students found in the selected class.
    </div>
    @endif
</div>
