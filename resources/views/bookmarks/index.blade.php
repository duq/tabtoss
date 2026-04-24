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
                    'urlStatus' => $bookmark->url_status,
                    'urlCheckedAt' => optional($bookmark->url_checked_at)?->toIso8601String(),
                    'updatedAt' => optional($bookmark->updated_at)->timestamp ?? 0,
                ];
            })->values()->all(),
        ];
    })->values()->all();
@endphp

<x-layouts.app>
    @php
        $primaryActionClass = 'inline-flex items-center rounded-xl bg-neutral-900 px-3 py-2 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900';
        $ghostActionClass = 'inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100';
        $controlClass = 'h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none';
    @endphp

    <x-slot name="title">
        {{ __('Bookmarks Dashboard') }}
    </x-slot>

    <div
        class="mx-auto w-full max-w-6xl px-4 py-10 sm:py-12"
        x-data="bookmarkBoard(@js($categoryPayload))"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full border border-neutral-200 bg-neutral-50 px-3 py-1 text-sm font-medium text-neutral-700">
                    <span class="size-2 rounded-full bg-neutral-700"></span>
                    {{ __('Bookmarks') }}
                </div>
                <div>
                    <x-heading.h2 class="tracking-tight text-neutral-900">{{ __('Bookmarks Dashboard') }}</x-heading.h2>
                    <p class="mt-2 max-w-2xl text-sm text-neutral-500 text-pretty">
                        {{ __('A cleaner grouped view for browsing your saved links without the kanban clutter.') }}
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a class="{{ $ghostActionClass }}" href="{{ route('bookmarks.inbox') }}">
                    {{ __('Go to Inbox') }}
                </a>
                <a class="{{ $primaryActionClass }}" href="{{ route('filament.dashboard.resources.bookmark-categories.index') }}">
                    {{ __('Manage Categories') }}
                </a>
            </div>
        </div>

        @if ($categories->isEmpty())
            <div class="mt-8 rounded-2xl border border-dashed border-neutral-300 bg-neutral-50 px-6 py-12 text-center">
                <p class="text-lg font-medium tracking-tight text-neutral-800 text-balance">{{ __('No categories yet.') }}</p>
                <p class="mt-2 text-base text-neutral-500 text-pretty">{{ __('Create your first category to organize bookmarks on this dashboard.') }}</p>
                <div class="mt-4">
                    <a class="{{ $primaryActionClass }}" href="{{ route('filament.dashboard.resources.bookmark-categories.index') }}">
                        {{ __('Create categories') }}
                    </a>
                </div>
            </div>
        @else
            <dl class="mt-6 grid rounded-2xl border border-neutral-200 bg-white sm:grid-cols-3 sm:divide-x sm:divide-neutral-200">
                <div class="px-4 py-3 not-sm:border-b not-sm:border-neutral-200">
                    <dt class="text-sm font-medium text-neutral-500">{{ __('Visible bookmarks') }}</dt>
                    <dd class="mt-1 text-2xl font-semibold tabular-nums text-neutral-900" x-text="visibleBookmarkCount()"></dd>
                </div>
                <div class="px-4 py-3 not-sm:border-b not-sm:border-neutral-200">
                    <dt class="text-sm font-medium text-neutral-500">{{ __('Active categories') }}</dt>
                    <dd class="mt-1 text-2xl font-semibold tabular-nums text-neutral-900" x-text="filteredCategories().length"></dd>
                </div>
                <div class="px-4 py-3">
                    <dt class="text-sm font-medium text-neutral-500">{{ __('Total saved') }}</dt>
                    <dd class="mt-1 text-2xl font-semibold tabular-nums text-neutral-900">{{ $totalBookmarks }}</dd>
                </div>
            </dl>

            <div class="sticky top-0 z-20 -mx-4 mt-6 border-y border-neutral-200 bg-white/95 px-4 py-3 backdrop-blur">
                <div class="grid gap-3 lg:grid-cols-4">
                    <label class="relative">
                        <span class="sr-only">{{ __('Search bookmarks') }}</span>
                        <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-neutral-400" aria-hidden="true">
                            <circle cx="11" cy="11" r="6" fill="none" stroke="currentColor" stroke-width="1.8"></circle>
                            <path d="m20 20-4.2-4.2" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"></path>
                        </svg>
                        <input
                            type="text"
                            class="h-10 w-full rounded-xl border border-neutral-300 bg-white pl-10 pr-3 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-neutral-500 focus:outline-none"
                            x-model="searchQuery"
                            placeholder="{{ __('Search title, URL, or host') }}"
                        />
                    </label>

                    <label>
                        <span class="sr-only">{{ __('Filter by category') }}</span>
                        <select class="{{ $controlClass }}" x-model="activeCategory">
                            <option value="">{{ __('All categories') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label>
                        <span class="sr-only">{{ __('Sort categories') }}</span>
                        <select class="{{ $controlClass }}" x-model="sortBy">
                            <option value="recent">{{ __('Recently updated') }}</option>
                            <option value="name">{{ __('Category name') }}</option>
                            <option value="count">{{ __('Most bookmarks') }}</option>
                        </select>
                    </label>

                    <label class="flex h-10 items-center gap-2 rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-600">
                        <input type="checkbox" class="size-4 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-500" x-model="showEmptyCategories" />
                        <span>{{ __('Show empty') }}</span>
                    </label>
                </div>
            </div>
            <div class="mt-2 flex justify-end">
                <button
                    type="button"
                    class="inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100 disabled:cursor-not-allowed disabled:opacity-50"
                    @click="clearFilters()"
                    :disabled="! hasActiveFilters()"
                >
                    {{ __('Clear filters') }}
                </button>
            </div>

            <div class="mt-6 space-y-6">
                <template x-if="filteredCategories().length === 0">
                    <div class="rounded-2xl border border-dashed border-neutral-300 bg-neutral-50 px-6 py-12 text-center">
                        <p class="text-lg font-medium tracking-tight text-neutral-800 text-balance">{{ __('No bookmarks match your filters.') }}</p>
                        <p class="mt-2 text-base text-neutral-500 text-pretty">{{ __('Try a different search term or clear filters.') }}</p>
                        <button
                            type="button"
                            class="mt-4 inline-flex items-center rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700"
                            @click="clearFilters()"
                        >
                            {{ __('Clear filters') }}
                        </button>
                    </div>
                </template>

                <template x-for="category in filteredCategories()" :key="category.name">
                    <section class="overflow-hidden rounded-2xl border border-neutral-200 bg-white">
                        <div class="flex items-center justify-between gap-4 border-b border-neutral-200 bg-white px-4 py-3">
                            <div class="min-w-0 flex items-center gap-2">
                                <h3 class="truncate text-base font-medium text-neutral-900" x-text="category.name"></h3>
                                <span class="rounded-full border border-neutral-200 bg-neutral-50 px-2 py-0.5 text-sm font-medium tabular-nums text-neutral-600" x-text="category.filteredBookmarks.length"></span>
                            </div>
                            <span class="text-sm text-neutral-500 tabular-nums" x-text="categorySummary(category.filteredBookmarks.length)"></span>
                        </div>

                        <ul role="list" class="divide-y divide-neutral-100">
                            <template x-if="category.filteredBookmarks.length === 0">
                                <li class="px-4 py-6 text-sm text-neutral-500">
                                    {{ __('No bookmarks in this category yet.') }}
                                </li>
                            </template>

                            <template x-for="bookmark in category.filteredBookmarks" :key="bookmark.id">
                                <li class="px-4 py-3">
                                    <div class="grid gap-4 lg:grid-cols-2 lg:items-start">
                                        <div class="flex items-start gap-3">
                                            <div class="flex size-10 shrink-0 items-center justify-center rounded-xl border border-neutral-200 bg-neutral-50 text-sm font-medium text-neutral-700">
                                                <span x-text="bookmarkInitial(bookmark)"></span>
                                            </div>

                                            <a :href="bookmark.url" target="_blank" rel="noopener noreferrer" class="min-w-0 flex-1">
                                                <p class="truncate text-sm font-medium text-neutral-900" x-text="bookmark.title"></p>
                                                <p class="mt-1 truncate text-sm text-neutral-500" x-text="bookmark.url"></p>
                                                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                                                    <span class="rounded-full border border-neutral-200 bg-neutral-50 px-2 py-1 font-medium text-neutral-600" x-text="bookmark.host"></span>
                                                    <span
                                                        class="inline-flex items-center gap-1 rounded-full px-2 py-1 font-medium"
                                                        :class="bookmarkStatusBadgeClass(bookmark.urlStatus)"
                                                    >
                                                        <span class="size-1.5 rounded-full" :class="bookmarkStatusDotClass(bookmark.urlStatus)"></span>
                                                        <span x-text="bookmarkStatusLabel(bookmark.urlStatus)"></span>
                                                    </span>
                                                    <template x-if="bookmark.folderPath">
                                                        <span class="truncate text-neutral-400" x-text="bookmark.folderPath"></span>
                                                    </template>
                                                </div>
                                                <p class="mt-2 text-sm text-neutral-400" x-text="bookmarkLastCheckedLabel(bookmark.urlCheckedAt)"></p>
                                            </a>

                                            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                                                <button type="button" class="inline-flex size-8 items-center justify-center rounded-lg text-neutral-500 hover:bg-neutral-100" @click="open = ! open" aria-label="{{ __('Row actions') }}">
                                                    <svg viewBox="0 0 24 24" class="size-4" aria-hidden="true">
                                                        <circle cx="5" cy="12" r="1.75" fill="currentColor"></circle>
                                                        <circle cx="12" cy="12" r="1.75" fill="currentColor"></circle>
                                                        <circle cx="19" cy="12" r="1.75" fill="currentColor"></circle>
                                                    </svg>
                                                </button>
                                                <div x-show="open" x-cloak class="absolute right-0 z-20 mt-1 w-40 rounded-xl border border-neutral-200 bg-white p-1 shadow-sm">
                                                    <a :href="bookmark.url" target="_blank" rel="noopener noreferrer" class="flex rounded-lg px-2 py-1.5 text-sm text-neutral-700 hover:bg-neutral-100">
                                                        {{ __('Open link') }}
                                                    </a>
                                                    <button type="button" class="flex w-full rounded-lg px-2 py-1.5 text-left text-sm text-neutral-700 hover:bg-neutral-100" @click="navigator.clipboard?.writeText(bookmark.url); open = false">
                                                        {{ __('Copy URL') }}
                                                    </button>
                                                    <a href="{{ route('filament.dashboard.resources.bookmark-categories.index') }}" class="flex rounded-lg px-2 py-1.5 text-sm text-neutral-700 hover:bg-neutral-100">
                                                        {{ __('Manage categories') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <template x-if="bookmark.urlStatus === 200">
                                                <a
                                                    :href="bookmark.url"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="block overflow-hidden rounded-xl border border-neutral-200 bg-neutral-50 aspect-[3/2]"
                                                >
                                                    <img
                                                        :src="`https://s.wordpress.com/mshots/v1/${encodeURIComponent(bookmark.url)}?w=960`"
                                                        alt="{{ __('Website preview') }}"
                                                        class="h-full w-full object-cover"
                                                        loading="lazy"
                                                        referrerpolicy="no-referrer"
                                                        onerror="this.closest('a')?.classList.add('hidden')"
                                                    />
                                                </a>
                                            </template>
                                            <template x-if="bookmark.urlStatus !== 200">
                                                <div class="flex aspect-[3/2] items-center justify-center rounded-xl border border-dashed border-neutral-300 bg-neutral-50 px-4 text-center text-sm text-neutral-500">
                                                    {{ __('Thumbnail appears when status is live.') }}
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </section>
                </template>
            </div>
        @endif
    </div>

    <script>
        function bookmarkBoard(categories) {
            return {
                categories,
                searchQuery: '',
                activeCategory: '',
                sortBy: 'recent',
                showEmptyCategories: false,
                clearFilters() {
                    this.searchQuery = '';
                    this.activeCategory = '';
                    this.sortBy = 'recent';
                    this.showEmptyCategories = false;
                },
                hasActiveFilters() {
                    return this.searchQuery.trim() !== '' || this.activeCategory !== '' || this.sortBy !== 'recent' || this.showEmptyCategories;
                },
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
                bookmarkStatusLabel(urlStatus) {
                    if (urlStatus === 200) {
                        return '{{ __('Up') }}';
                    }

                    if ([0, 404, 500].includes(urlStatus)) {
                        return '{{ __('Down') }}';
                    }

                    return '{{ __('Checking') }}';
                },
                bookmarkStatusBadgeClass(urlStatus) {
                    if (urlStatus === 200) {
                        return 'border border-emerald-200 bg-emerald-50 text-emerald-700';
                    }

                    if ([0, 404, 500].includes(urlStatus)) {
                        return 'border border-red-200 bg-red-50 text-red-700';
                    }

                    return 'border border-amber-200 bg-amber-50 text-amber-700';
                },
                bookmarkStatusDotClass(urlStatus) {
                    if (urlStatus === 200) {
                        return 'bg-emerald-500';
                    }

                    if ([0, 404, 500].includes(urlStatus)) {
                        return 'bg-red-500';
                    }

                    return 'bg-amber-500';
                },
                bookmarkLastCheckedLabel(urlCheckedAt) {
                    if (! urlCheckedAt) {
                        return '{{ __('Last checked: pending') }}';
                    }

                    const checkedAt = new Date(urlCheckedAt);
                    if (Number.isNaN(checkedAt.getTime())) {
                        return '{{ __('Last checked: unknown') }}';
                    }

                    const diffMs = Date.now() - checkedAt.getTime();
                    const diffMinutes = Math.floor(diffMs / 60000);

                    if (diffMinutes < 1) {
                        return '{{ __('Last checked: just now') }}';
                    }

                    if (diffMinutes < 60) {
                        return `{{ __('Last checked:') }} ${diffMinutes}m {{ __('ago') }}`;
                    }

                    const diffHours = Math.floor(diffMinutes / 60);
                    if (diffHours < 24) {
                        return `{{ __('Last checked:') }} ${diffHours}h {{ __('ago') }}`;
                    }

                    const diffDays = Math.floor(diffHours / 24);
                    return `{{ __('Last checked:') }} ${diffDays}d {{ __('ago') }}`;
                },
                bookmarkInitial(bookmark) {
                    const source = bookmark.host || bookmark.title || bookmark.url || '?';

                    return source.charAt(0).toUpperCase();
                },
            };
        }
    </script>
</x-layouts.app>
