@php
    use Filament\Support\Enums\MaxWidth;
@endphp

<footer
    @class([
        'fi-footer my-3 flex flex-wrap items-center justify-center text-sm text-gray-500 dark:text-gray-400',
        'border-t border-gray-200 p-3 text-center dark:border-gray-700' => $footerPosition === 'sidebar' || $footerPosition === 'sidebar.footer' || $borderTopEnabled === true,
        'fi-sidebar' => $footerPosition === 'sidebar' || $footerPosition === 'sidebar.footer',
        'mx-auto w-full px-4 md:px-6 lg:px-8' => $footerPosition === 'footer',
        match ($maxContentWidth ??= (filament()->getMaxContentWidth() ?? MaxWidth::SevenExtraLarge)) {
            MaxWidth::ExtraSmall, 'xs' => 'max-w-xs',
            MaxWidth::Small, 'sm' => 'max-w-sm',
            MaxWidth::Medium, 'md' => 'max-w-md',
            MaxWidth::Large, 'lg' => 'max-w-lg',
            MaxWidth::ExtraLarge, 'xl' => 'max-w-xl',
            MaxWidth::TwoExtraLarge, '2xl' => 'max-w-2xl',
            MaxWidth::ThreeExtraLarge, '3xl' => 'max-w-3xl',
            MaxWidth::FourExtraLarge, '4xl' => 'max-w-4xl',
            MaxWidth::FiveExtraLarge, '5xl' => 'max-w-5xl',
            MaxWidth::SixExtraLarge, '6xl' => 'max-w-6xl',
            MaxWidth::SevenExtraLarge, '7xl' => 'max-w-7xl',
            MaxWidth::Full, 'full' => 'max-w-full',
            MaxWidth::MinContent, 'min' => 'max-w-min',
            MaxWidth::MaxContent, 'max' => 'max-w-max',
            MaxWidth::FitContent, 'fit' => 'max-w-fit',
            MaxWidth::Prose, 'prose' => 'max-w-prose',
            MaxWidth::ScreenSmall, 'screen-sm' => 'max-w-screen-sm',
            MaxWidth::ScreenMedium, 'screen-md' => 'max-w-screen-md',
            MaxWidth::ScreenLarge, 'screen-lg' => 'max-w-screen-lg',
            MaxWidth::ScreenExtraLarge, 'screen-xl' => 'max-w-screen-xl',
            MaxWidth::ScreenTwoExtraLarge, 'screen-2xl' => 'max-w-screen-2xl',
            default => $maxContentWidth,
        } => $footerPosition === 'footer',
    ])
>
    <a href="https://afghancosmos.com" target="_blank" rel="noopener noreferrer"
        class="inline-flex items-center justify-center gap-2 font-medium text-gray-600 transition hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
        <span>&copy; {{ now()->format('Y') }} Made By</span>
        <img src="{{ asset('schools/cosmos.png') }}" alt="Afghan Cosmos IT & Solutions"
            class="h-6 w-6 rounded-full bg-white object-contain p-0.5 ring-1 ring-gray-200 dark:ring-gray-700">
        <span>Afghan Cosmos IT &amp; Solutions</span>
    </a>

    @if($loadTime)
        <span class="ms-2">{{ $loadTime }}s</span>
    @endif
</footer>
