<x-layouts.app>
    <x-slot name="title">
        {{ __('Bookmarks Dashboard') }}
    </x-slot>

    <div class="mx-auto w-full max-w-7xl px-4 py-12">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <x-heading.h2 class="text-primary-900">{{ __('Bookmarks Dashboard') }}</x-heading.h2>
                <p class="text-sm text-neutral-500">{{ __('A kanban view of your categorized bookmarks.') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a class="btn btn-ghost" href="{{ route('bookmarks.inbox') }}">
                    {{ __('Go to Inbox') }}
                </a>
                <a class="btn btn-primary" href="{{ route('filament.dashboard.resources.bookmark-categories.index') }}">
                    {{ __('Manage Categories') }}
                </a>
            </div>
        </div>

        <div class="mt-8 overflow-x-auto">
            <div class="flex gap-4 pb-4">
                @foreach ($categories as $category)
                    <div class="w-72 flex-shrink-0 rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-neutral-800">{{ $category->name }}</h3>
                            <span class="text-xs text-neutral-400">{{ $category->bookmarks->count() }}</span>
                        </div>

                        <div class="mt-4 space-y-3">
                            @forelse ($category->bookmarks as $bookmark)
                                <div class="rounded-xl border border-neutral-100 bg-neutral-50 px-3 py-2">
                                    <p class="text-sm font-medium text-neutral-800">
                                        {{ $bookmark->title ?? $bookmark->url }}
                                    </p>
                                    <a
                                        class="break-all text-xs text-primary-600 hover:underline"
                                        href="{{ $bookmark->url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        {{ $bookmark->url }}
                                    </a>
                                </div>
                            @empty
                                <p class="text-xs text-neutral-400">{{ __('No bookmarks yet.') }}</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
