@php
    $totalBookmarks = $categories->sum(fn ($category) => $category->bookmarks->count());

    $categoryPayload = $categories->map(function ($category) {
        return [
            'name' => $category->name,
            'bookmarks' => $category->bookmarks->map(function ($bookmark) {
                $host = parse_url($bookmark->url, PHP_URL_HOST) ?? $bookmark->url;
                $host = preg_replace('/^www\./', '', $host);

                return [
                    'id' => $bookmark->id,
                    'title' => $bookmark->title ?? $bookmark->url,
                    'url' => $bookmark->url,
                    'host' => $host,
                    'folderPath' => $bookmark->folder_path,
                    'updatedAt' => optional($bookmark->updated_at)->timestamp ?? 0,
                ];
            })->values()->all(),
        ];
    })->values()->all();
@endphp

<x-layouts.app>
    <x-slot name="title">
        {{ __('Bookmarks Dashboard') }}
    </x-slot>

    <div
        class="mx-auto w-full max-w-6xl px-4 py-10 sm:py-12"
        x-data="bookmarkBoard(@js($categoryPayload))"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-primary-600">
                    <span class="h-2 w-2 rounded-full bg-primary-500"></span>
                    {{ __('Bookmarks') }}
                </div>
                <div>
                    <x-heading.h2 class="text-primary-900">{{ __('Bookmarks Dashboard') }}</x-heading.h2>
                    <p class="mt-2 max-w-2xl text-sm text-neutral-500">
                        {{ __('A cleaner grouped view for browsing your saved links without the kanban clutter.') }}
                    </p>
                </div>
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

        <div class="mt-6 grid gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-neutral-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-400">{{ __('Visible bookmarks') }}</p>
                <p class="mt-2 text-2xl font-semibold text-primary-900" x-text="visibleBookmarkCount()"></p>
            </div>
            <div class="rounded-2xl border border-neutral-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-400">{{ __('Active categories') }}</p>
                <p class="mt-2 text-2xl font-semibold text-primary-900" x-text="filteredCategories().length"></p>
            </div>
            <div class="rounded-2xl border border-neutral-200 bg-white px-4 py-3 shadow-sm">
                <p class="text-xs uppercase tracking-[0.2em] text-neutral-400">{{ __('Total saved') }}</p>
                <p class="mt-2 text-2xl font-semibold text-primary-900">{{ $totalBookmarks }}</p>
            </div>
        </div>

        <div class="sticky top-0 z-20 -mx-4 mt-6 border-y border-neutral-200 bg-white/95 px-4 py-3 shadow-sm backdrop-blur">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1.8fr),minmax(0,1fr),minmax(0,1fr),auto]">
                <label class="form-control">
                    <span class="mb-1 text-xs font-semibold uppercase tracking-[0.2em] text-neutral-400">{{ __('Search') }}</span>
                    <input
                        type="text"
                        class="input input-bordered w-full"
                        x-model="searchQuery"
                        placeholder="{{ __('Search title, URL, or host') }}"
                    />
                </label>

                <label class="form-control">
                    <span class="mb-1 text-xs font-semibold uppercase tracking-[0.2em] text-neutral-400">{{ __('Category') }}</span>
                    <select class="select select-bordered w-full" x-model="activeCategory">
                        <option value="">{{ __('All categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->name }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="form-control">
                    <span class="mb-1 text-xs font-semibold uppercase tracking-[0.2em] text-neutral-400">{{ __('Sort') }}</span>
                    <select class="select select-bordered w-full" x-model="sortBy">
                        <option value="recent">{{ __('Recently updated') }}</option>
                        <option value="name">{{ __('Category name') }}</option>
                        <option value="count">{{ __('Most bookmarks') }}</option>
                    </select>
                </label>

                <label class="flex items-end gap-3 rounded-xl border border-neutral-200 px-4 py-3">
                    <input type="checkbox" class="checkbox checkbox-sm" x-model="showEmptyCategories" />
                    <span class="text-sm text-neutral-600">{{ __('Show empty categories') }}</span>
                </label>
            </div>
        </div>

        <div class="mt-6 grid gap-4 2xl:grid-cols-2">
            <template x-if="filteredCategories().length === 0">
                <div class="rounded-2xl border border-dashed border-neutral-300 bg-neutral-50 px-6 py-12 text-center">
                    <p class="text-lg font-semibold text-neutral-700">{{ __('No bookmarks match your filters.') }}</p>
                    <p class="mt-2 text-sm text-neutral-500">{{ __('Try a different search term or switch back to all categories.') }}</p>
                </div>
            </template>

            <template x-for="category in filteredCategories()" :key="category.name">
                <section class="overflow-hidden rounded-2xl border border-neutral-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between gap-4 border-b border-neutral-200 bg-neutral-50/80 px-4 py-3">
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-neutral-400">{{ __('Category') }}</p>
                            <div class="mt-1 flex items-center gap-2">
                                <h3 class="truncate text-base font-semibold text-neutral-900" x-text="category.name"></h3>
                                <span
                                    class="rounded-full bg-white px-2 py-0.5 text-xs font-medium text-neutral-500 ring-1 ring-inset ring-neutral-200"
                                    x-text="category.filteredBookmarks.length"
                                ></span>
                            </div>
                        </div>

                        <span class="hidden text-xs text-neutral-400 sm:inline" x-text="categorySummary(category.filteredBookmarks.length)"></span>
                    </div>

                    <div class="divide-y divide-neutral-100">
                        <template x-if="category.filteredBookmarks.length === 0">
                            <div class="px-4 py-6 text-sm text-neutral-500">
                                {{ __('No bookmarks in this category yet.') }}
                            </div>
                        </template>

                        <template x-for="bookmark in category.filteredBookmarks" :key="bookmark.id">
                            <a
                                :href="bookmark.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group flex items-start gap-3 px-4 py-3 transition hover:bg-primary-50/60"
                            >
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-sm font-semibold text-primary-600">
                                    <span x-text="bookmarkInitial(bookmark)"></span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-neutral-900" x-text="bookmark.title"></p>

                                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs">
                                                <span class="rounded-full bg-neutral-100 px-2 py-1 font-medium text-neutral-600" x-text="bookmark.host"></span>

                                                <template x-if="bookmark.folderPath">
                                                    <span class="truncate text-neutral-400" x-text="bookmark.folderPath"></span>
                                                </template>
                                            </div>
                                        </div>

                                        <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 shrink-0 text-neutral-300 transition group-hover:text-primary-500" aria-hidden="true">
                                            <path d="M7 17 17 7M9 7h8v8" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                                        </svg>
                                    </div>

                                    <p class="mt-2 truncate text-xs text-neutral-500 group-hover:text-neutral-700" x-text="bookmark.url"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                </section>
            </template>
        </div>
    </div>

    <script>
        function bookmarkBoard(categories) {
            return {
                categories,
                searchQuery: '',
                activeCategory: '',
                sortBy: 'recent',
                showEmptyCategories: false,
                normalize(value) {
                    return (value ?? '').toString().trim().toLowerCase();
                },
                bookmarkMatches(bookmark) {
                    const query = this.normalize(this.searchQuery);
                    if (! query) {
                        return true;
                    }

                    return [
                        this.normalize(bookmark.title),
                        this.normalize(bookmark.url),
                        this.normalize(bookmark.host),
                        this.normalize(bookmark.folderPath),
                    ].some((value) => value.includes(query));
                },
                filteredCategories() {
                    const activeCategory = this.normalize(this.activeCategory);

                    return this.categories
                        .filter((category) => {
                            if (! activeCategory) {
                                return true;
                            }

                            return this.normalize(category.name) === activeCategory;
                        })
                        .map((category) => {
                            const filteredBookmarks = category.bookmarks.filter((bookmark) => this.bookmarkMatches(bookmark));

                            return {
                                ...category,
                                filteredBookmarks,
                                latestUpdate: filteredBookmarks.reduce((latest, bookmark) => Math.max(latest, bookmark.updatedAt ?? 0), 0),
                            };
                        })
                        .filter((category) => {
                            if (category.filteredBookmarks.length > 0) {
                                return true;
                            }

                            return ! this.searchQuery && this.showEmptyCategories;
                        })
                        .sort((first, second) => {
                            if (this.sortBy === 'name') {
                                return first.name.localeCompare(second.name);
                            }

                            if (this.sortBy === 'count') {
                                return second.filteredBookmarks.length - first.filteredBookmarks.length
                                    || first.name.localeCompare(second.name);
                            }

                            return second.latestUpdate - first.latestUpdate
                                || first.name.localeCompare(second.name);
                        });
                },
                visibleBookmarkCount() {
                    return this.filteredCategories().reduce((count, category) => count + category.filteredBookmarks.length, 0);
                },
                categorySummary(count) {
                    return count === 1
                        ? '{{ __('1 bookmark') }}'
                        : `${count} {{ __('bookmarks') }}`;
                },
                bookmarkInitial(bookmark) {
                    const source = bookmark.host || bookmark.title || bookmark.url || '?';

                    return source.charAt(0).toUpperCase();
                },
            };
        }
    </script>
</x-layouts.app>
