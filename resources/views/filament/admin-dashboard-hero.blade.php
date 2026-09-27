@if (request()->routeIs('filament.admin.pages.dashboard'))
<div class="aman-dashboard-hero">
    <div class="aman-hero-inner">
        <div>
            <div class="aman-eyebrow">
                <x-heroicon-m-building-library class="h-4 w-4" />
                {{ __('School Administration') }}
            </div>

            <h1>{{ __('Welcome back, Admin') }}</h1>

            <p>{{ __('Manage students, academics, attendance and finance from one place.') }}</p>

            <div class="aman-date">
                {{ now()->format('l, d M Y') }}
            </div>
        </div>

        <div class="aman-actions">
            <a href="{{ \App\Filament\Resources\StudentResource::getUrl('create') }}"
               class="aman-action aman-action-primary">
                <x-heroicon-m-user-plus class="h-5 w-5" />
                {{ __('Add Student') }}
            </a>

            <a href="{{ \App\Filament\Resources\TeacherResource::getUrl('create') }}"
               class="aman-action aman-action-secondary">
                <x-heroicon-m-academic-cap class="h-5 w-5" />
                {{ __('Add Teacher') }}
            </a>
        </div>
    </div>
</div>
@endif