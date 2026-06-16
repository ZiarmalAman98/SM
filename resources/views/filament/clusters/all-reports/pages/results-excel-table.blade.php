<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Export Class Exam Result</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Export student results data to Excel format with customizable filters for branch and class.
            </p>

            <form wire:submit="exportResults">
                {{ $this->form }}
            </form>
        </div>
    </div>
</x-filament-panels::page>
