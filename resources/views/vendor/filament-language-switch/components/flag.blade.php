@foreach ($locales as $locale => $label)
    <x-filament::dropdown.item
        :color="($locale === app()->getLocale()) ? 'primary' : 'gray'"
        tag="button"
        wire:click="changeLocale('{{ $locale }}')"
    >
        {!! $label !!}
    </x-filament::dropdown.item>
@endforeach
