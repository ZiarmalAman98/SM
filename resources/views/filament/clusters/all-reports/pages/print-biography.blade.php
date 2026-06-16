@php /** @var \App\Filament\Clusters\AllReports\Pages\PrintBiography $this */ @endphp

<x-filament::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Print Biography</h2>

            <div class="space-y-4">
                <div class="w-full">
                    <label for="biography_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Select Biography
                    </label>
                    <select
                        wire:model.live="biography_id"
                        id="biography_id"
                        class="w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:focus:ring-blue-400"
                        required
                    >
                        <option value="">Choose a biography...</option>
                        @foreach(\App\Models\Biography::orderBy('name')->get() as $biography)
                            <option value="{{ $biography->id }}">{{ $biography->name }} - {{ $biography->father_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        wire:click="printBiography"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 dark:hover:bg-blue-600 focus:bg-blue-700 dark:focus:bg-blue-600 active:bg-blue-900 dark:active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-25"
                        @if(!$biography_id) disabled @endif
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Biography
                    </button>
                </div>
            </div>
        </div>

        @if($biography_id)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow dark:shadow-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Preview</h3>
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        Biography will be printed in a new window with the selected biography data.
                    </p>
                </div>
            </div>
        @endif
    </div>
</x-filament::page>
