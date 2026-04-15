<div x-data="themeToggle()" {{ $attributes->merge(['class' => 'flex items-center']) }}>
    <button
        type="button"
        class="btn btn-ghost btn-circle border border-neutral-200 bg-white/85 text-primary-900 shadow-sm backdrop-blur hover:bg-white dark:border-white/10 dark:bg-white/10 dark:text-white dark:hover:bg-white/15"
        @click="toggleTheme()"
        :aria-label="isDark ? '{{ __('Switch to light mode') }}' : '{{ __('Switch to dark mode') }}'"
        :title="isDark ? '{{ __('Switch to light mode') }}' : '{{ __('Switch to dark mode') }}'"
    >
        <svg x-show="!isDark" x-cloak viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
            <path
                d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
            />
        </svg>

        <svg x-show="isDark" x-cloak viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
            <circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="1.8" />
            <path
                d="M12 2v2.5M12 19.5V22M4.93 4.93l1.77 1.77M17.3 17.3l1.77 1.77M2 12h2.5M19.5 12H22M4.93 19.07l1.77-1.77M17.3 6.7l1.77-1.77"
                fill="none"
                stroke="currentColor"
                stroke-linecap="round"
                stroke-width="1.8"
            />
        </svg>
    </button>
</div>
