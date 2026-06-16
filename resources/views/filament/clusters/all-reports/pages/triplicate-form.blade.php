<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Triplicate Form Generator</h2>
            <p class="text-gray-600 mb-6">Select branch, class, student and academic year to generate a triplicate form with exam results and attendance.</p>
            
            {{ $this->form }}
        </div>
    </div>
</x-filament-panels::page>