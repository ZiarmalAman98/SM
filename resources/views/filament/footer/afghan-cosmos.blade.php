<footer class="fi-footer mx-auto my-3 w-full max-w-7xl border-t border-gray-200 px-4 py-3 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400 md:px-6 lg:px-8">
    <a href="https://afghancosmos.com" target="_blank" rel="noopener noreferrer"
        class="inline-flex items-center justify-center gap-2 font-medium text-gray-600 transition hover:text-primary-600 dark:text-gray-300 dark:hover:text-primary-400">
        <span>&copy; {{ now()->format('Y') }}</span>
        <img src="{{ asset('schools/cosmos.png') }}" alt="Afghan Cosmos IT & Solutions"
            class="h-6 w-6 rounded-full bg-white object-contain p-0.5 ring-1 ring-gray-200 dark:ring-gray-700">
        <span>Afghan Cosmos IT &amp; Solutions</span>
    </a>
</footer>
