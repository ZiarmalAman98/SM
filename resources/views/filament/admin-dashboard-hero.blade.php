@if (request()->routeIs('filament.admin.pages.dashboard'))
<div class="mb-6 overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <div class="relative px-5 py-6 sm:px-7">
        <div class="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-sky-100/70 blur-2xl dark:bg-sky-500/10"></div>
        <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="mb-1 text-sm font-medium text-sky-600 dark:text-sky-400">{{ __('School Administration') }}</p>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white sm:text-3xl">
                    {{ __('Welcome back, Admin') }}
                </h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    {{ __('Manage students, academics, attendance and finance from one place.') }}
                </p>
                <p class="mt-3 text-xs font-medium text-slate-400 dark:text-slate-500">
                    {{ now()->format('l, d M Y') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ \App\Filament\Resources\StudentResource::getUrl('create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-sky-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-600">
                    <x-heroicon-m-user-plus class="h-4 w-4" />
                    {{ __('Add Student') }}
                </a>
                <a href="{{ \App\Filament\Resources\TeacherResource::getUrl('create') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                    <x-heroicon-m-academic-cap class="h-4 w-4" />
                    {{ __('Add Teacher') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endif