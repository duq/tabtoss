<x-layouts.app>
    <x-slot name="title">
        {{ __('Bookmark Inbox') }}
    </x-slot>

    <div class="mx-auto w-full max-w-6xl px-4 py-12">
        <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <x-heading.h2 class="text-primary-900">{{ __('Bookmark Inbox') }}</x-heading.h2>
                <p class="text-sm text-neutral-500">{{ __('Import bookmarks and categorize them.') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a class="btn btn-ghost" href="{{ route('bookmarks.index') }}">
                    {{ __('View Dashboard') }}
                </a>
                <a class="btn btn-primary" href="{{ route('filament.dashboard.resources.bookmark-categories.index') }}">
                    {{ __('Manage Categories') }}
                </a>
            </div>
        </div>

        <livewire:bookmarks.swipe-inbox />
    </div>

    <button
        type="button"
        class="fixed bottom-6 right-6 z-50 flex h-12 w-12 items-center justify-center rounded-full bg-primary-600 text-white shadow-lg transition hover:bg-primary-700"
        x-data="{ visible: false }"
        x-init="window.addEventListener('scroll', () => { visible = window.scrollY > 200; })"
        x-show="visible"
        x-transition.opacity
        x-cloak
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        aria-label="{{ __('Scroll to top') }}"
    >
        <svg viewBox="0 0 24 24" class="h-5 w-5" aria-hidden="true">
            <path d="M12 5l-7 7h4v7h6v-7h4z" fill="currentColor"></path>
        </svg>
    </button>
</x-layouts.app>
