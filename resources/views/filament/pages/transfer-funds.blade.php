<x-filament::page>
    <x-filament::card>
        <div class="space-y-4">
            <div class="text-xl font-bold">{{ __('Daily Transfer') }}</div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <x-filament::card>
                    <div class="text-sm text-gray-500">{{ __('Today\'s Income') }}</div>
                    <div class="text-2xl font-bold">AFN {{ number_format($todayIncome, 2) }}</div>
                    <div class="mt-1 text-xs text-gray-500">{{ __('Income + invoice payments') }}</div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-sm text-gray-500">{{ __('Today\'s Expenses') }}</div>
                    <div class="text-2xl font-bold">AFN {{ number_format($todayExpense, 2) }}</div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-sm text-gray-500">{{ __('Cash To Transfer') }}</div>
                    <div class="text-2xl font-bold">AFN {{ number_format($todayNet, 2) }}</div>
                    <div class="mt-1 text-xs text-gray-500">{{ __('Income − expenses') }}</div>
                </x-filament::card>

                <x-filament::card>
                    <div class="text-sm text-gray-500">{{ __('Status') }}</div>
                    <div class="text-lg">
                        @if ($todayNet > 0)
                            <span class="text-yellow-500">{{ __('Pending Transfer') }}</span>
                        @else
                            <span class="text-green-500">{{ __('Already Transferred') }}</span>
                        @endif
                    </div>
                </x-filament::card>
            </div>
        </div>
    </x-filament::card>
</x-filament::page>
