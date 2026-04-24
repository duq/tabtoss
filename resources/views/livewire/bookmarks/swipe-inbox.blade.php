<div
    class="mx-auto w-full max-w-6xl"
    x-data="bookmarkInbox(@js([
        'manageCategoriesUrl' => route('filament.dashboard.resources.bookmark-categories.index'),
    ]))"
    x-on:bookmark-deleted.window="handleBookmarksDeleted($event.detail?.id ? [$event.detail.id] : [])"
    x-on:bookmarks-deleted.window="handleBookmarksDeleted($event.detail?.ids ?? [])"
>
    <div class="rounded-3xl border border-neutral-200 bg-white p-4 sm:p-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2 text-sm text-neutral-600">
                    <span class="rounded-full border border-neutral-200 bg-neutral-50 px-2.5 py-1 tabular-nums">{{ __('Imported:') }} {{ $importedCount }} {{ __('bookmarks') }}</span>
                    <span class="rounded-full border border-neutral-200 bg-neutral-50 px-2.5 py-1 tabular-nums">{{ __('Current bookmarks:') }} {{ $bookmarksCount }}</span>
                    <span class="rounded-full border border-neutral-200 bg-neutral-50 px-2.5 py-1 tabular-nums">{{ __('Showing:') }} {{ $visibleBookmarksCount }}</span>
                    <span class="rounded-full border border-neutral-200 bg-neutral-50 px-2.5 py-1 tabular-nums">{{ __('Down:') }} {{ $downBookmarksCount }}</span>
                    <span class="rounded-full border border-neutral-200 bg-neutral-50 px-2.5 py-1 tabular-nums">{{ __('Selected:') }} {{ $selectedBookmarksCount }}</span>
                </div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" class="rounded-full border px-3 py-1 text-sm font-medium tabular-nums" :class="activeFilter === 'all' ? 'border-neutral-900 bg-neutral-900 text-white' : 'border-neutral-300 bg-white text-neutral-700'" @click="setFilter('all')">
                        {{ __('All') }} ({{ $visibleBookmarksCount }})
                    </button>
                    <button type="button" class="rounded-full border px-3 py-1 text-sm font-medium tabular-nums" :class="activeFilter === 'down' ? 'border-neutral-900 bg-neutral-900 text-white' : 'border-neutral-300 bg-white text-neutral-700'" @click="setFilter('down')">
                        {{ __('Down') }} ({{ $downBookmarksCount }})
                    </button>
                    <button type="button" class="rounded-full border px-3 py-1 text-sm font-medium tabular-nums" :class="activeFilter === 'unchecked' ? 'border-neutral-900 bg-neutral-900 text-white' : 'border-neutral-300 bg-white text-neutral-700'" @click="setFilter('unchecked')">
                        {{ __('Unchecked') }} ({{ $uncheckedBookmarksCount }})
                    </button>
                    <button type="button" class="rounded-full border px-3 py-1 text-sm font-medium tabular-nums" :class="activeFilter === 'ai-labeled' ? 'border-neutral-900 bg-neutral-900 text-white' : 'border-neutral-300 bg-white text-neutral-700'" @click="setFilter('ai-labeled')">
                        {{ __('AI-labeled') }} ({{ $aiLabeledBookmarksCount }})
                    </button>
                </div>

                <div class="mt-4 grid gap-3 lg:grid-cols-3 lg:items-start">
                    <div class="min-w-0 rounded-2xl border border-neutral-200 p-4">
                        <label class="mb-2 block text-sm font-semibold text-neutral-800" for="bookmark-domain-search">
                            {{ __('Search by domain') }}
                        </label>
                        <div class="relative">
                            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-neutral-400" aria-hidden="true">
                                <circle cx="11" cy="11" r="6" fill="none" stroke="currentColor" stroke-width="1.8"></circle>
                                <path d="m20 20-4.2-4.2" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"></path>
                            </svg>
                            <input
                                type="text"
                                id="bookmark-domain-search"
                                name="domain_search"
                                class="h-10 w-full rounded-xl border border-neutral-300 bg-white pl-10 pr-3 text-sm text-neutral-900 placeholder:text-neutral-400 focus:border-neutral-500 focus:outline-none"
                                placeholder="{{ __('Search by domain (e.g. youtube.com)') }}"
                                x-model.live="searchQuery"
                                @pointerdown.stop
                            />
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center rounded-xl bg-neutral-900 px-3 py-2 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900"
                                @click="$refs.fileInput.click()"
                            >
                                {{ __('Import') }}
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700"
                                wire:click="applyAiLabelsManual"
                                @pointerdown.stop
                            >
                                {{ __('AI labels') }}
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700"
                                wire:click="checkUrlStatuses"
                                @pointerdown.stop
                            >
                                {{ __('Check URLs') }}
                            </button>
                            <a class="inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100" href="{{ route('bookmarks.index') }}">
                                {{ __('Dashboard') }}
                            </a>
                            <a class="inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100" :href="manageCategoriesUrl">
                                {{ __('Categories') }}
                            </a>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-neutral-200 p-4">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-semibold text-neutral-800">{{ __('Bulk cleanup') }}</h4>
                                <p class="mt-1 text-sm text-neutral-500 text-pretty">
                                    {{ __('Select down websites and remove them in one pass.') }}
                                </p>
                            </div>
                            <span class="rounded-full border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-sm font-medium tabular-nums text-neutral-700">
                                {{ $selectedBookmarksCount }}
                            </span>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="inline-flex items-center rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700"
                                wire:click="selectAllDownBookmarks"
                                @pointerdown.stop
                            >
                                {{ __('Select down') }}
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700 disabled:cursor-not-allowed disabled:opacity-50"
                                wire:click="clearSelectedBookmarks"
                                @pointerdown.stop
                                @disabled($selectedBookmarksCount === 0)
                            >
                                {{ __('Clear') }}
                            </button>
                            <button
                                type="button"
                                @class([
                                    'inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium disabled:cursor-not-allowed disabled:opacity-50',
                                    'border border-neutral-300 bg-white text-neutral-700' => ! $confirmingBulkDelete,
                                    'border border-red-300 bg-red-50 text-red-700' => $confirmingBulkDelete,
                                ])
                                wire:click="requestDeleteSelectedBookmarks"
                                @pointerdown.stop
                                @disabled($selectedBookmarksCount === 0)
                            >
                                {{ $confirmingBulkDelete ? __('Confirm delete') : __('Delete selected') }}
                            </button>
                            @if ($confirmingBulkDelete)
                                <button
                                    type="button"
                                    class="inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100"
                                    wire:click="cancelDeleteSelectedBookmarks"
                                    @pointerdown.stop
                                >
                                    {{ __('Cancel') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-2xl border border-neutral-200 p-4">
                        <h4 class="text-sm font-semibold text-neutral-800">{{ __('Bulk categorize') }}</h4>
                        <p class="mt-1 text-sm text-neutral-500 text-pretty">
                            {{ __('Apply one category to all matching bookmarks from a domain.') }}
                        </p>

                        <div class="mt-3 grid gap-2">
                            <select id="bulk-domain-select" name="bulk_domain" aria-label="{{ __('Select domain') }}" class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none" wire:model="bulkDomain" @pointerdown.stop>
                                <option value="">{{ __('Select domain') }}</option>
                                @foreach ($this->domainOptions as $option)
                                    <option value="{{ $option['domain'] }}">
                                        {{ $option['domain'] }} ({{ $option['count'] }})
                                    </option>
                                @endforeach
                            </select>

                            <select id="bulk-category-select" name="bulk_category_id" aria-label="{{ __('Select category') }}" class="h-10 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none" wire:model="bulkCategoryId" @pointerdown.stop>
                                <option value="">{{ __('Select category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-xl bg-neutral-900 px-3 py-2 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900"
                                wire:click="bulkAssignCategory"
                                @pointerdown.stop
                            >
                                {{ __('Apply') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <input
                type="file"
                class="hidden"
                x-ref="fileInput"
                accept=".html,text/html"
                @change="handleFile($event)"
            />
        </div>
    </div>

    <template x-if="importStatus">
        <div class="mt-4 rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-900">
            <span x-text="importStatus"></span>
        </div>
    </template>

    <div
        class="fixed left-1/2 top-6 z-50 w-full max-w-sm -translate-x-1/2 rounded-2xl border border-neutral-200 bg-white px-4 py-3 text-center text-sm font-medium text-neutral-800 shadow-sm"
        x-show="showToast"
        x-transition.opacity
        x-cloak
    >
        <span x-text="toastMessage"></span>
    </div>

    @if ($aiLabelStatus)
        <div class="mt-4 rounded-lg border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-700">
            {{ $aiLabelStatus }}
        </div>
    @endif

    <div class="mt-6 space-y-4">
        <div class="space-y-3">
            @if ($bookmarks->isEmpty())
                <div class="flex h-full flex-col items-center justify-center rounded-3xl border border-dashed border-neutral-300 bg-neutral-50 p-8 text-center">
                    <p class="text-lg font-medium tracking-tight text-neutral-800 text-balance">{{ __('No bookmarks loaded yet.') }}</p>
                    <p class="mt-2 text-base text-neutral-500 text-pretty">{{ __('Import a bookmarks HTML file to get started.') }}</p>
                    <div class="mt-3 flex flex-wrap items-center justify-center gap-3 text-sm">
                        <a class="inline-flex items-center rounded-xl border border-neutral-300 bg-white px-3 py-2 font-medium text-neutral-700" href="https://support.google.com/chrome/answer/96816" target="_blank" rel="noopener noreferrer">
                            {{ __('Chrome export guide') }}
                        </a>
                        <a class="inline-flex items-center rounded-xl border border-neutral-300 bg-white px-3 py-2 font-medium text-neutral-700" href="https://support.mozilla.org/en-US/kb/export-firefox-bookmarks-to-backup-or-transfer" target="_blank" rel="noopener noreferrer">
                            {{ __('Firefox export guide') }}
                        </a>
                    </div>
                </div>
            @else
                @foreach ($bookmarks as $bookmark)
                    @php
                        $bookmarkHost = preg_replace('/^www\./', '', parse_url($bookmark->url, PHP_URL_HOST) ?? $bookmark->url);
                    @endphp
                    <div
                        class="relative rounded-2xl border border-neutral-200 bg-white px-4 py-4"
                        wire:key="bookmark-{{ $bookmark->id }}"
                        :style="draggingId === {{ $bookmark->id }} ? dragStyle : ''"
                        x-show="isBookmarkVisible({{ $bookmark->id }}, @js($bookmark->url), {{ $bookmark->url_status === null ? 'null' : $bookmark->url_status }}, @js($bookmark->ai_label))"
                        x-transition.opacity
                        @pointerdown="startDrag($event, {{ $bookmark->id }})"
                        @pointerup="endDrag({{ $bookmark->id }})"
                        @pointercancel="cancelDrag()"
                    >
                        <div class="grid gap-4 lg:grid-cols-2 lg:items-start">
                            <div class="min-w-0 space-y-3">
                                <div class="flex items-start pt-0.5">
                                    <label class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-2 text-sm text-neutral-600" for="bookmark-select-{{ $bookmark->id }}">
                                        <input
                                            type="checkbox"
                                            id="bookmark-select-{{ $bookmark->id }}"
                                            name="selected_bookmark_ids[]"
                                            aria-label="{{ __('Select bookmark') }}"
                                            class="size-5 rounded border-neutral-300 text-neutral-900 focus:ring-neutral-500 sm:size-4"
                                            value="{{ $bookmark->id }}"
                                            wire:model.live="selectedBookmarkIds"
                                            @pointerdown.stop
                                        />
                                        <span>{{ __('Select') }}</span>
                                    </label>
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full border border-neutral-200 bg-neutral-50 px-2.5 py-1 text-sm font-medium text-neutral-600">
                                        {{ $bookmarkHost }}
                                    </span>

                                    @if ($bookmark->ai_label)
                                        <span class="rounded-full border border-sky-200 bg-sky-50 px-2.5 py-1 text-sm font-medium text-sky-700">
                                            {{ $bookmark->ai_label }}
                                        </span>
                                    @endif

                                    <span
                                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-sm font-medium
                                        @if ($bookmark->url_status === 200)
                                            border border-emerald-200 bg-emerald-50 text-emerald-700
                                        @elseif (in_array($bookmark->url_status, [0, 404, 500], true))
                                            border border-red-200 bg-red-50 text-red-700
                                        @else
                                            border border-amber-200 bg-amber-50 text-amber-700
                                        @endif"
                                    >
                                        @if ($bookmark->url_status === 200)
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            {{ __('Live') }}
                                        @elseif (in_array($bookmark->url_status, [0, 404, 500], true))
                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            {{ __('Down') }}
                                        @else
                                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                            {{ __('Checking') }}
                                        @endif
                                    </span>
                                </div>

                                <h3 class="mt-2 text-base font-medium text-neutral-900">
                                    {{ $bookmark->title ?? $bookmark->url }}
                                </h3>

                                <a
                                    class="mt-1 block truncate text-sm text-neutral-600 underline decoration-neutral-300 underline-offset-2"
                                    href="{{ $bookmark->url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @pointerdown.stop
                                >
                                    {{ $bookmark->url }}
                                </a>

                                @if ($bookmark->folder_path)
                                    <p class="mt-2 truncate text-sm text-neutral-500">
                                        <span class="font-medium text-neutral-600">{{ __('Folder:') }}</span> {{ $bookmark->folder_path }}
                                    </p>
                                @endif

                                <div class="grid gap-2">
                                    <select
                                        id="bookmark-category-{{ $bookmark->id }}"
                                        name="selected_categories[{{ $bookmark->id }}]"
                                        aria-label="{{ __('Select category') }}"
                                        class="h-9 w-full rounded-xl border border-neutral-300 bg-white px-3 text-sm text-neutral-900 focus:border-neutral-500 focus:outline-none"
                                        wire:model="selectedCategories.{{ $bookmark->id }}"
                                        @pointerdown.stop
                                    >
                                        <option value="">{{ __('Select category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            type="button"
                                            class="inline-flex h-8 min-h-8 items-center rounded-lg bg-neutral-900 px-3 text-sm font-medium text-white"
                                            wire:click="categorize({{ $bookmark->id }})"
                                            @pointerdown.stop
                                        >
                                            {{ __('Keep') }}
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 min-h-8 items-center rounded-lg border border-red-300 bg-red-50 px-3 text-sm font-medium text-red-700"
                                            wire:click="markDeleted({{ $bookmark->id }})"
                                            @pointerdown.stop
                                        >
                                            {{ __('Delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                @if ($bookmark->url_status === 200)
                                    <a
                                        href="{{ $bookmark->url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="block overflow-hidden rounded-xl border border-neutral-200 bg-neutral-50 aspect-[3/2]"
                                        @pointerdown.stop
                                    >
                                        <img
                                            src="https://s.wordpress.com/mshots/v1/{{ urlencode($bookmark->url) }}?w=960"
                                            alt="{{ __('Website preview') }}"
                                            class="h-full w-full object-cover"
                                            loading="lazy"
                                            referrerpolicy="no-referrer"
                                            onerror="this.closest('a')?.classList.add('hidden')"
                                        />
                                    </a>
                                @else
                                    <div class="flex aspect-[3/2] items-center justify-center rounded-xl border border-dashed border-neutral-300 bg-neutral-50 px-4 text-center text-sm text-neutral-500">
                                        {{ __('Thumbnail appears when status is live.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="rounded-3xl border border-neutral-200 bg-white p-5">
            <h4 class="text-sm font-semibold text-neutral-800">{{ __('How it works') }}</h4>
            <ul class="mt-3 space-y-2 text-sm text-neutral-600" role="list">
                <li>{{ __('Import your bookmarks HTML file to load them here.') }}</li>
                <li>{{ __('Pick a category and click categorize to keep them organized.') }}</li>
                <li>{{ __('Swipe left to delete, swipe right to pick a category.') }}</li>
                <li>{{ __('Use the dashboard to manage categories anytime.') }}</li>
            </ul>
        </div>

        @if ($bookmarksPaginator->hasPages())
            @php
                $currentPage = $bookmarksPaginator->currentPage();
                $lastPage = $bookmarksPaginator->lastPage();
                $startPage = max(1, $currentPage - 1);
                $endPage = min($lastPage, $currentPage + 1);
            @endphp
            <div class="rounded-2xl border border-neutral-200 bg-white px-4 py-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-sm text-neutral-600 tabular-nums">
                        {{ __('Showing') }} {{ $bookmarksPaginator->firstItem() ?? 0 }}-{{ $bookmarksPaginator->lastItem() ?? 0 }}
                        {{ __('of') }} {{ $bookmarksPaginator->total() }}
                    </p>

                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex items-center rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700 disabled:cursor-not-allowed disabled:opacity-50"
                            wire:click="previousPage"
                            @disabled($bookmarksPaginator->onFirstPage())
                        >
                            {{ __('Previous') }}
                        </button>

                        <div class="hidden items-center gap-1 sm:flex">
                            @foreach (range($startPage, $endPage) as $page)
                                <button
                                    type="button"
                                    @class([
                                        'inline-flex min-w-9 items-center justify-center rounded-lg border px-3 py-2 text-sm font-medium tabular-nums',
                                        'border-neutral-900 bg-neutral-900 text-white' => $page === $currentPage,
                                        'border-neutral-300 bg-white text-neutral-700' => $page !== $currentPage,
                                    ])
                                    wire:click="gotoPage({{ $page }})"
                                >
                                    {{ $page }}
                                </button>
                            @endforeach
                        </div>

                        <button
                            type="button"
                            class="inline-flex items-center rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700 disabled:cursor-not-allowed disabled:opacity-50"
                            wire:click="nextPage"
                            @disabled(! $bookmarksPaginator->hasMorePages())
                        >
                            {{ __('Next') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-neutral-950/40 p-4"
        x-show="showCategoryPicker"
        x-transition.opacity
        x-cloak
        @keydown.escape.window="closeCategoryPicker()"
    >
        <div class="w-full max-w-md rounded-3xl border border-neutral-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold tracking-tight text-neutral-900 text-balance">{{ __('Choose a category') }}</h3>
                <button type="button" class="inline-flex size-8 items-center justify-center rounded-lg text-neutral-500 hover:bg-neutral-100" @click="closeCategoryPicker()">✕</button>
            </div>

            @if ($categories->isEmpty())
                <p class="mt-4 text-sm text-neutral-500">
                    {{ __('No categories yet. Create one in the dashboard.') }}
                </p>
            @else
                <div class="mt-4 grid gap-2">
                    @foreach ($categories as $category)
                        <button
                            type="button"
                            class="inline-flex items-center justify-start rounded-xl border border-neutral-300 bg-white px-3 py-2 text-sm font-medium text-neutral-700"
                            @click="selectCategory({{ $category->id }})"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" class="inline-flex items-center rounded-xl px-3 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100" @click="closeCategoryPicker()">
                    {{ __('Cancel') }}
                </button>
                <a class="inline-flex items-center rounded-xl bg-neutral-900 px-3 py-2 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900" :href="manageCategoriesUrl">
                    {{ __('Manage Categories') }}
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function bookmarkInbox({ manageCategoriesUrl }) {
        return {
            manageCategoriesUrl,
            importStatus: null,
            searchQuery: '',
            hiddenIds: [],
            toastMessage: '',
            showToast: false,
            toastTimeout: null,
            showCategoryPicker: false,
            activeFilter: 'all',
            pendingBookmarkId: null,
            draggingId: null,
            dragStartX: 0,
            dragStartY: 0,
            dragDeltaX: 0,
            threshold: 90,
            showToastMessage(message) {
                this.toastMessage = message;
                this.showToast = true;
                if (this.toastTimeout) {
                    clearTimeout(this.toastTimeout);
                }
                this.toastTimeout = setTimeout(() => {
                    this.showToast = false;
                }, 3000);
            },
            handleBookmarksDeleted(ids) {
                if (! Array.isArray(ids) || ids.length === 0) {
                    return;
                }

                ids.forEach((id) => {
                    if (! this.hiddenIds.includes(id)) {
                        this.hiddenIds.push(id);
                    }
                });

                this.showToastMessage(
                    ids.length === 1
                        ? '{{ __('Bookmark deleted') }}'
                        : `${ids.length} {{ __('bookmarks deleted') }}`
                );
            },

            normalizeDomain(value) {
                const trimmedValue = (value ?? '').trim().toLowerCase();
                if (! trimmedValue) {
                    return '';
                }

                try {
                    const candidate = /^https?:\/\//i.test(trimmedValue) ? trimmedValue : `https://${trimmedValue}`;

                    return new URL(candidate).hostname.replace(/^www\./, '');
                } catch (error) {
                    return trimmedValue
                        .replace(/^https?:\/\//i, '')
                        .replace(/^www\./, '')
                        .split('/')[0];
                }
            },

            matchesSearch(url) {
                const query = this.normalizeDomain(this.searchQuery);
                if (! query) {
                    return true;
                }

                const normalizedUrl = (url ?? '').toLowerCase();
                if (normalizedUrl.includes(query)) {
                    return true;
                }

                const domain = this.normalizeDomain(url);

                return domain === query || domain.endsWith(`.${query}`);
            },

            setFilter(filter) {
                this.activeFilter = filter;
            },

            matchesQuickFilter(urlStatus, aiLabel) {
                if (this.activeFilter === 'down') {
                    return [0, 404, 500].includes(urlStatus);
                }

                if (this.activeFilter === 'unchecked') {
                    return urlStatus === null;
                }

                if (this.activeFilter === 'ai-labeled') {
                    return !! aiLabel;
                }

                return true;
            },

            isBookmarkVisible(id, url, urlStatus, aiLabel) {
                if (this.hiddenIds.includes(id)) {
                    return false;
                }

                return this.matchesSearch(url) && this.matchesQuickFilter(urlStatus, aiLabel);
            },

            async handleFile(event) {
                const file = event.target.files[0];
                if (! file) {
                    return;
                }
                this.importStatus = '{{ __('Reading bookmarks...') }}';

                const text = await file.text();
                const parsed = this.parseBookmarkFile(text);
                if (parsed.length === 0) {
                    this.importStatus = '{{ __('No valid bookmarks found in this file.') }}';
                    return;
                }

                const batchSize = 200;
                let imported = 0;
                this.importStatus = '{{ __('Importing bookmarks and queueing website status checks...') }}';
                for (let i = 0; i < parsed.length; i += batchSize) {
                    const batch = parsed.slice(i, i + batchSize);
                    imported += await this.$wire.importBookmarks(batch);
                }

                this.importStatus = '{{ __('Imported') }} ' + imported + ' {{ __('bookmarks. Website checks are running in the background.') }}';
                await this.$wire.$refresh();
                event.target.value = '';
            },

            parseBookmarkFile(contents) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(contents, 'text/html');
                const detectedBrowser = this.detectBrowser(doc);
                const rootList = doc.querySelector('dl');
                if (! rootList) {
                    return [];
                }

                const items = [];
                const walk = (container, path) => {
                    const children = Array.from(container.children);
                    for (let i = 0; i < children.length; i += 1) {
                        const node = children[i];
                        const tagName = node.tagName;

                        if (tagName === 'DT') {
                            const link = node.querySelector(':scope > a');
                            const folder = node.querySelector(':scope > h3');
                            const nestedList = node.querySelector(':scope > dl');

                            if (link) {
                                const url = link.getAttribute('href');
                                if (! url) {
                                    continue;
                                }
                                items.push({
                                    title: link.textContent.trim(),
                                    url,
                                    folder_path: path.length ? path.join(' / ') : null,
                                    browser: detectedBrowser,
                                });
                                continue;
                            }

                            if (folder) {
                                if (nestedList) {
                                    walk(nestedList, [...path, folder.textContent.trim()]);
                                } else {
                                    let next = null;
                                    for (let j = i + 1; j < children.length; j += 1) {
                                        if (children[j].tagName === 'DL') {
                                            next = children[j];
                                            break;
                                        }
                                        if (children[j].tagName === 'DT') {
                                            break;
                                        }
                                    }
                                    if (next) {
                                        walk(next, [...path, folder.textContent.trim()]);
                                    }
                                }
                            }
                            continue;
                        }

                        if (tagName === 'DL' || tagName === 'P') {
                            walk(node, path);
                        }
                    }
                };

                walk(rootList, []);
                return items;
            },

            detectBrowser(doc) {
                const title = doc.querySelector('title')?.textContent ?? '';
                if (title.toLowerCase().includes('firefox')) {
                    return 'firefox';
                }
                if (title.toLowerCase().includes('chrome')) {
                    return 'chrome';
                }
                return null;
            },

            startDrag(event, bookmarkId) {
                if (event.target.closest('button, select, option, a, input, label')) {
                    return;
                }
                this.draggingId = bookmarkId;
                this.dragStartX = event.clientX;
                this.dragStartY = event.clientY;
                this.dragDeltaX = 0;
                event.currentTarget.setPointerCapture(event.pointerId);
                event.currentTarget.addEventListener('pointermove', this.handleDrag);
            },

            handleDrag(event) {
                this.dragDeltaX = event.clientX - this.dragStartX;
            },

            async endDrag(bookmarkId) {
                if (this.draggingId !== bookmarkId) {
                    return;
                }
                const deltaX = this.dragDeltaX;
                this.resetDrag();

                if (deltaX < -this.threshold) {
                    await this.$wire.markDeleted(bookmarkId);
                    await this.$wire.$refresh();
                } else if (deltaX > this.threshold) {
                    this.openCategoryPicker(bookmarkId);
                }
            },

            cancelDrag() {
                this.resetDrag();
            },

            resetDrag() {
                this.draggingId = null;
                this.dragDeltaX = 0;
            },

            get dragStyle() {
                return `transform: translateX(${this.dragDeltaX}px) rotate(${this.dragDeltaX / 16}deg);`;
            },

            openCategoryPicker(bookmarkId) {
                this.pendingBookmarkId = bookmarkId;
                this.showCategoryPicker = true;
            },

            closeCategoryPicker() {
                this.showCategoryPicker = false;
                this.pendingBookmarkId = null;
            },

            async selectCategory(categoryId) {
                if (! this.pendingBookmarkId) {
                    return;
                }
                const bookmarkId = this.pendingBookmarkId;
                this.closeCategoryPicker();
                await this.$wire.assignCategory(bookmarkId, categoryId);
                await this.$wire.$refresh();
            },

        };
    }
</script>
