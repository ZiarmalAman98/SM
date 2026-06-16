<x-filament::page>     
    <x-filament::card>
        <div class="space-y-4">
            <div class="text-xl font-bold">Daily Transfer</div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-filament::card>
                    <div class="text-sm text-gray-500">Today's Income</div>
                    <div class="text-2xl font-bold">؋ {{ number_format($todayIncome, 2) }}</div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-sm text-gray-500">Destination</div>
                    <div class="text-lg">{{ $destination }}</div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-sm text-gray-500">Status</div>
                    <div class="text-lg">
                        @if ($todayIncome > 0)
                            <span class="text-yellow-500">Pending Transfer</span>
                        @else
                            <span class="text-green-500">Already Transferred</span>
                        @endif
                    </div>
                </x-filament::card>
            </div>

            
        </div>
    </x-filament::card>
</x-filament::page>
