@php /** @var \App\Filament\Clusters\AllReports\Pages\StudentInformationReport $this */ @endphp

<x-filament::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Student Information Report</h2>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Branch
                        </label>
                        <select
                            wire:model.live="branch_id"
                            id="branch_id"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                            required
                        >
                            <option value="">Choose a branch...</option>
                            @foreach(\App\Models\Branch::orderBy('branch_name')->get() as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Class
                        </label>
                        <select
                            wire:model.live="class_id"
                            id="class_id"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                            required
                        >
                            <option value="">Choose a class...</option>
                            @foreach($this->classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Student Status
                        </label>
                        <select
                            wire:model.live="status"
                            id="status"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                        >
                            <option value="active">Active Students</option>
                            <option value="inactive">Inactive Students</option>
                            <option value="all">All Students</option>
                        </select>
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Gender Filter
                        </label>
                        <select
                            wire:model.live="gender"
                            id="gender"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                        >
                            <option value="">All Genders</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>

                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Report Type
                        </label>
                        <select
                            wire:model.live="report_type"
                            id="report_type"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                        >
                            <option value="detailed">Detailed Report</option>
                            <option value="summary">Summary Report</option>
                            <option value="contact">Contact Information</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        wire:click="generateReport"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-25"
                        @if(!$branch_id || !$class_id) disabled @endif
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>
        </div>

        @if($branch_id && $class_id)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Report Preview</h3>
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Student information report will be generated with the selected criteria:
                    </p>
                    <ul class="mt-2 text-sm text-gray-600 dark:text-gray-300 space-y-1">
                        <li>• Branch: {{ \App\Models\Branch::find($branch_id)?->branch_name ?? 'N/A' }}</li>
                        <li>• Class: {{ \App\Models\SchoolClass::find($class_id)?->class_name ?? 'N/A' }}</li>
                        <li>• Status: {{ ucfirst($status) }}</li>
                        <li>• Gender: {{ $gender ? ucfirst($gender) : 'All' }}</li>
                        <li>• Report Type: {{ ucfirst($report_type) }}</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</x-filament::page>
