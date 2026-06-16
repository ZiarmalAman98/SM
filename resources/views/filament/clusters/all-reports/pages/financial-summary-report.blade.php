@php /** @var \App\Filament\Clusters\AllReports\Pages\FinancialSummaryReport $this */ @endphp

<x-filament::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Financial Summary Report</h2>

            <form wire:submit.prevent="generateReport" class="space-y-4">
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
                        <label for="report_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Report Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            wire:model.live="report_type"
                            id="report_type"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                            required
                        >
                            <option value="monthly">Monthly Report</option>
                            <option value="quarterly">Quarterly Report</option>
                            <option value="yearly">Yearly Report</option>
                            <option value="custom">Custom Date Range</option>
                        </select>
                        @error('report_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($this->report_type === 'yearly')
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Year <span class="text-red-500">*</span>
                        </label>
                        <select
                            wire:model.live="year"
                            id="year"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                        >
                            @for($i = 1400; $i <= 1450; $i++)
                                <option value="{{ $i }}" @if($i == \Morilog\Jalali\Jalalian::now()->getYear()) selected @endif>{{ $i }}</option>
                            @endfor
                        </select>
                        @error('year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endif

                    @if($this->report_type === 'custom')
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Start Date (Jalali) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model.live="start_date"
                            id="start_date"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400 jalali-date"
                            placeholder="YYYY-MM-DD (Jalali)"
                            required
                        >
                        @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @endif
                </div>

                @if($this->report_type === 'custom')
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        End Date (Jalali) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model.live="end_date"
                        id="end_date"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400 jalali-date"
                        placeholder="YYYY-MM-DD (Jalali)"
                        required
                    >
                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <a
                        href="{{ $this->branch_id ? $this->getReportUrl() : '#' }}"
                        target="_blank"
                        class="inline-flex items-center px-4 py-2 rounded-lg font-semibold text-sm text-white transition ease-in-out duration-150 {{ $this->branch_id ? '' : 'pointer-events-none opacity-25' }}"
                        style="background-color: #06b6d4;"
                        onmouseover="this.style.backgroundColor='#0891b2'"
                        onmouseout="this.style.backgroundColor='#06b6d4'"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Generate Report
                    </a>
                </div>
            </form>
        </div>

        @if($this->branch_id)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Report Preview</h3>
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Financial summary report will be generated with the selected criteria:
                    </p>
                    <ul class="text-sm text-gray-600 dark:text-gray-300 mt-2 space-y-1">
                        <li><strong>Branch:</strong> {{ \App\Models\Branch::find($this->branch_id)?->branch_name ?? 'Not selected' }}</li>
                        <li><strong>Class:</strong> {{ $this->class_id ? \App\Models\SchoolClass::find($this->class_id)?->class_name : 'All Classes' }}</li>
                        <li><strong>Report Type:</strong> {{ ucfirst($this->report_type) }}</li>
                        @if($this->report_type === 'yearly')
                            <li><strong>Year:</strong> {{ $this->year }} (Jalali)</li>
                        @elseif($this->report_type === 'custom')
                            <li><strong>Date Range:</strong> {{ $this->start_date }} to {{ $this->end_date }} (Jalali)</li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif
    </div>
</x-filament::page>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Jalali date pickers
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.jalali-date', {
            locale: 'fa',
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'Y-m-d',
            onChange: function(selectedDates, dateStr, instance) {
                // Update Livewire model
                if (instance.element.id === 'start_date') {
                    @this.set('start_date', dateStr);
                } else if (instance.element.id === 'end_date') {
                    @this.set('end_date', dateStr);
                }
            }
        });
    }
});
</script>
