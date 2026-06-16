<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Qesmat-kunanda Seh Parcha</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                Select branch, class, student, and academic year to export individual student result data.
            </p>

            <form wire:submit="exportStudentResult">
                {{ $this->form }}
            </form>
        </div>
    </div>
</x-filament-panels::page>
