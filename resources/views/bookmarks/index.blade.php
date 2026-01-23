<x-layouts.app>
    <x-slot name="title">
        {{ __('Bookmarks') }}
    </x-slot>

    <div class="mx-auto w-full max-w-6xl px-4 py-12">
        <div class="flex items-center justify-between">
            <div>
                <x-heading.h2 class="text-primary-900">{{ __('Bookmarks by Category') }}</x-heading.h2>
                <p class="text-sm text-neutral-500">{{ __('Browse bookmarks you have already categorized.') }}</p>
            </div>
            <a class="btn btn-primary" href="{{ route('home') }}">{{ __('Back to Inbox') }}</a>
        </div>

        @if ($categories->isEmpty())
            <div class="mt-8 rounded-2xl border border-dashed border-neutral-200 bg-neutral-50 p-8 text-center">
                <p class="text-lg font-medium text-neutral-700">{{ __('No categories yet.') }}</p>
                <p class="mt-2 text-sm text-neutral-500">{{ __('Create a category and swipe right on a bookmark to add it here.') }}</p>
            </div>
        @endif

        <div class="mt-8 grid gap-6 lg:grid-cols-2">
            @foreach ($categories as $category)
                <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-neutral-900">{{ $category->name }}</h3>
                        <span class="text-xs text-neutral-400">
                            {{ $category->bookmarks->count() }} {{ __('items') }}
                        </span>
                    </div>

                    @if ($category->bookmarks->isEmpty())
                        <p class="mt-4 text-sm text-neutral-500">{{ __('No bookmarks in this category yet.') }}</p>
                    @else
                        <ul class="mt-4 space-y-3">
                            @foreach ($category->bookmarks as $bookmark)
                                <li class="rounded-lg border border-neutral-100 px-4 py-3">
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
                                    @if ($bookmark->folder_path)
                                        <p class="mt-1 text-xs text-neutral-400">
                                            {{ __('Folder:') }} {{ $bookmark->folder_path }}
                                        </p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
