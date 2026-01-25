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
</x-layouts.app>
