@php /** @var \App\Filament\Clusters\AllReports\Pages\ExamResultsReport $this */ @endphp

<x-filament::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Exam Results Report</h2>

            <form wire:submit="generateReport" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Branch <span class="text-red-500">*</span>
                        </label>
                        <select
                            wire:model.live="branch_id"
                            id="branch_id"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                            required
                        >
                            <option value="">Select Branch...</option>
                            @foreach(\App\Models\Branch::orderBy('branch_name')->get() as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Class (Optional)
                        </label>
                        <select
                            wire:model.live="class_id"
                            id="class_id"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                        >
                            <option value="">All Classes</option>
                            @if($this->branch_id)
                                @foreach(\App\Models\SchoolClass::where('branch_id', $this->branch_id)->orderBy('class_name')->get() as $class)
                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="exam_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Exam Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            wire:model.live="exam_type"
                            id="exam_type"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                            required
                        >
                            <option value="mid_term">Midterm Exam</option>
                            <option value="final">Final Exam</option>
                        </select>
                        @error('exam_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Academic Year <span class="text-red-500">*</span>
                        </label>
                        <select
                            wire:model.live="academic_year"
                            id="academic_year"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                            required
                        >
                            @for($i = 1400; $i <= 1450; $i++)
                                <option value="{{ $i }}" @if($i == \Morilog\Jalali\Jalalian::now()->getYear()) selected @endif>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('academic_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-25"
                        @if(!$this->branch_id) disabled @endif
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        @if($this->branch_id)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Report Preview</h3>
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Exam results report will be generated with the selected criteria:
                    </p>
                    <ul class="text-sm text-gray-600 dark:text-gray-300 mt-2 space-y-1">
                        <li><strong>Branch:</strong> {{ \App\Models\Branch::find($this->branch_id)?->branch_name ?? 'Not selected' }}</li>
                        <li><strong>Class:</strong> {{ $this->class_id ? \App\Models\SchoolClass::find($this->class_id)?->class_name : 'All Classes' }}</li>
                        <li><strong>Exam Type:</strong> {{ ucfirst($this->exam_type) }}</li>
                        <li><strong>Academic Year:</strong> {{ $this->academic_year }}</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</x-filament::page>
