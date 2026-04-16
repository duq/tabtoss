<div
    class="mx-auto w-full max-w-6xl"
    x-data="bookmarkInbox(@js([
        'manageCategoriesUrl' => route('filament.dashboard.resources.bookmark-categories.index'),
    ]))"
    x-on:bookmark-deleted.window="handleBookmarksDeleted($event.detail?.id ? [$event.detail.id] : [])"
    x-on:bookmarks-deleted.window="handleBookmarksDeleted($event.detail?.ids ?? [])"
>
    <div class="rounded-2xl border border-neutral-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-4 text-sm text-neutral-500">
                    <span>{{ __('Imported:') }} {{ $importedCount }} {{ __('bookmarks') }}</span>
                    <span>{{ __('Current bookmarks:') }} {{ $bookmarksCount }}</span>
                    <span>{{ __('Down:') }} {{ $downBookmarksCount }}</span>
                    <span>{{ __('Selected:') }} {{ $selectedBookmarksCount }}</span>
                </div>

                <div class="mt-4 grid gap-3 lg:grid-cols-3 lg:items-start">
                    <div class="min-w-0 rounded-xl border border-neutral-200 p-3">
                        <label class="mb-1 block text-[11px] font-semibold uppercase tracking-[0.2em] text-neutral-400">
                            {{ __('Search by domain') }}
                        </label>
                        <div class="relative">
                            <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" aria-hidden="true">
                                <circle cx="11" cy="11" r="6" fill="none" stroke="currentColor" stroke-width="1.8"></circle>
                                <path d="m20 20-4.2-4.2" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.8"></path>
                            </svg>
                            <input
                                type="text"
                                class="input input-bordered h-11 w-full pl-10"
                                placeholder="{{ __('Search by domain (e.g. youtube.com)') }}"
                                x-model.live="searchQuery"
                                @pointerdown.stop
                            />
                        </div>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                class="btn btn-primary btn-sm"
                                @click="$refs.fileInput.click()"
                            >
                                {{ __('Import') }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline btn-sm"
                                wire:click="applyAiLabelsManual"
                                @pointerdown.stop
                            >
                                {{ __('AI labels') }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline btn-sm"
                                wire:click="checkUrlStatuses"
                                @pointerdown.stop
                            >
                                {{ __('Check URLs') }}
                            </button>
                            <a class="btn btn-ghost btn-sm" href="{{ route('bookmarks.index') }}">
                                {{ __('Dashboard') }}
                            </a>
                            <a class="btn btn-ghost btn-sm" :href="manageCategoriesUrl">
                                {{ __('Categories') }}
                            </a>
                        </div>
                    </div>

                    <div class="rounded-xl border border-neutral-200 p-3">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-semibold text-neutral-700">{{ __('Bulk cleanup') }}</h4>
                                <p class="mt-1 text-xs text-neutral-500">
                                    {{ __('Select down websites and remove them in one pass.') }}
                                </p>
                            </div>
                            <span class="rounded-full bg-neutral-100 px-2 py-1 text-xs font-semibold text-neutral-600">
                                {{ $selectedBookmarksCount }}
                            </span>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <button
                                type="button"
                                class="btn btn-outline btn-sm"
                                wire:click="selectAllDownBookmarks"
                                @pointerdown.stop
                            >
                                {{ __('Select down') }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline btn-sm"
                                wire:click="clearSelectedBookmarks"
                                @pointerdown.stop
                                @disabled($selectedBookmarksCount === 0)
                            >
                                {{ __('Clear') }}
                            </button>
                            <button
                                type="button"
                                class="btn btn-error btn-sm"
                                wire:click="deleteSelectedBookmarks"
                                @pointerdown.stop
                                @disabled($selectedBookmarksCount === 0)
                            >
                                {{ __('Delete selected') }}
                            </button>
                        </div>
                    </div>

                    <div class="rounded-xl border border-neutral-200 p-3">
                        <h4 class="text-sm font-semibold text-neutral-700">{{ __('Bulk categorize') }}</h4>
                        <p class="mt-1 text-xs text-neutral-500">
                            {{ __('Apply one category to all matching bookmarks from a domain.') }}
                        </p>

                        <div class="mt-3 grid gap-2">
                            <select class="select select-bordered select-sm w-full" wire:model="bulkDomain" @pointerdown.stop>
                                <option value="">{{ __('Select domain') }}</option>
                                @foreach ($this->domainOptions as $option)
                                    <option value="{{ $option['domain'] }}">
                                        {{ $option['domain'] }} ({{ $option['count'] }})
                                    </option>
                                @endforeach
                            </select>

                            <select class="select select-bordered select-sm w-full" wire:model="bulkCategoryId" @pointerdown.stop>
                                <option value="">{{ __('Select category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            <button
                                type="button"
                                class="btn btn-primary btn-sm"
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
        <div class="mt-4 rounded-lg border border-primary-100 bg-primary-50 px-4 py-3 text-sm text-primary-900">
            <span x-text="importStatus"></span>
        </div>
    </template>

    <div
        class="fixed left-1/2 top-6 z-50 w-full max-w-sm -translate-x-1/2 rounded-lg border border-neutral-200 bg-white px-4 py-3 text-center text-sm font-medium text-neutral-800 shadow-lg"
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
                <div class="flex h-full flex-col items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-neutral-50 p-8 text-center">
                    <p class="text-lg font-medium text-neutral-700">{{ __('No bookmarks loaded yet.') }}</p>
                    <p class="mt-2 text-sm text-neutral-500">{{ __('Import a bookmarks HTML file to get started.') }}</p>
                </div>
            @else
                @foreach ($bookmarks as $bookmark)
                    @php
                        $bookmarkHost = preg_replace('/^www\./', '', parse_url($bookmark->url, PHP_URL_HOST) ?? $bookmark->url);
                    @endphp
                    <div
                        class="rounded-2xl border border-neutral-200 bg-white px-4 py-4 shadow-sm transition hover:border-neutral-300"
                        wire:key="bookmark-{{ $bookmark->id }}"
                        :style="draggingId === {{ $bookmark->id }} ? dragStyle : ''"
                        x-show="isBookmarkVisible({{ $bookmark->id }}, @js($bookmark->url))"
                        x-transition.opacity
                        @pointerdown="startDrag($event, {{ $bookmark->id }})"
                        @pointerup="endDrag({{ $bookmark->id }})"
                        @pointercancel="cancelDrag()"
                    >
                        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr),220px] lg:items-start">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full bg-neutral-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-neutral-500">
                                        {{ $bookmarkHost }}
                                    </span>

                                    @if ($bookmark->ai_label)
                                        <span class="rounded-full bg-primary-100 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-primary-700">
                                            {{ $bookmark->ai_label }}
                                        </span>
                                    @endif

                                    <span
                                        class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold
                                        @if ($bookmark->url_status === 200)
                                            bg-emerald-100 text-emerald-700
                                        @elseif (in_array($bookmark->url_status, [0, 404, 500], true))
                                            bg-red-100 text-red-700
                                        @else
                                            bg-amber-100 text-amber-700
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

                                <h3 class="mt-3 text-base font-semibold text-neutral-900">
                                    {{ $bookmark->title ?? $bookmark->url }}
                                </h3>

                                <a
                                    class="mt-1 block truncate text-sm text-primary-600 hover:underline"
                                    href="{{ $bookmark->url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @pointerdown.stop
                                >
                                    {{ $bookmark->url }}
                                </a>

                                <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs text-neutral-500">
                                    @if ($bookmark->folder_path)
                                        <span>{{ __('Folder:') }} {{ $bookmark->folder_path }}</span>
                                    @endif
                                    <span>{{ __('Status:') }} {{ $bookmark->status }}</span>
                                </div>
                            </div>

                            <div class="grid gap-2">
                                <label class="inline-flex items-center gap-2 rounded-xl border border-neutral-200 px-3 py-2 text-xs text-neutral-500">
                                    <input
                                        type="checkbox"
                                        class="checkbox checkbox-sm"
                                        value="{{ $bookmark->id }}"
                                        wire:model.live="selectedBookmarkIds"
                                        @pointerdown.stop
                                    />
                                    <span>{{ __('Select') }}</span>
                                </label>

                                <select
                                    class="select select-bordered w-full"
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

                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-primary btn-sm"
                                        wire:click="categorize({{ $bookmark->id }})"
                                        @pointerdown.stop
                                    >
                                        {{ __('Keep') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-outline btn-error btn-sm"
                                        wire:click="markDeleted({{ $bookmark->id }})"
                                        @pointerdown.stop
                                    >
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
            <h4 class="text-sm font-semibold text-neutral-700">{{ __('How it works') }}</h4>
            <ul class="mt-3 space-y-2 text-sm text-neutral-600">
                <li>{{ __('Import your bookmarks HTML file to load them here.') }}</li>
                <li>{{ __('Pick a category and click categorize to keep them organized.') }}</li>
                <li>{{ __('Swipe left to delete, swipe right to pick a category.') }}</li>
                <li>{{ __('Use the dashboard to manage categories anytime.') }}</li>
            </ul>
        </div>
    </div>

    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        x-show="showCategoryPicker"
        x-transition.opacity
        x-cloak
        @keydown.escape.window="closeCategoryPicker()"
    >
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-neutral-900">{{ __('Choose a category') }}</h3>
                <button type="button" class="btn btn-ghost btn-sm" @click="closeCategoryPicker()">✕</button>
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
                            class="btn btn-outline btn-sm justify-start"
                            @click="selectCategory({{ $category->id }})"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" class="btn btn-ghost" @click="closeCategoryPicker()">
                    {{ __('Cancel') }}
                </button>
                <a class="btn btn-primary" :href="manageCategoriesUrl">
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

            isBookmarkVisible(id, url) {
                if (this.hiddenIds.includes(id)) {
                    return false;
                }

                return this.matchesSearch(url);
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
                for (let i = 0; i < parsed.length; i += batchSize) {
                    const batch = parsed.slice(i, i + batchSize);
                    imported += await this.$wire.importBookmarks(batch);
                }

                this.importStatus = '{{ __('Imported') }} ' + imported + ' {{ __('bookmarks.') }}';
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
                if (event.target.closest('button, select, option, a')) {
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
