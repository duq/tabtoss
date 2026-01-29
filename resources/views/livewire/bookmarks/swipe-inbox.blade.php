@php
    $categoryOptions = $categories->map(fn ($category) => [
        'id' => $category->id,
        'name' => $category->name,
    ])->values();
@endphp

<div
    class="mx-auto w-full max-w-4xl"
    x-data="bookmarkInbox(@js([
        'manageCategoriesUrl' => route('filament.dashboard.resources.bookmark-categories.index'),
    ]))"
>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-primary-900">{{ __('Bookmark Inbox') }}</h2>
            <div class="mt-2 flex flex-wrap gap-4 text-sm text-neutral-500">
                <span>
                    {{ __('Imported:') }} {{ $importedCount }} {{ __('bookmarks') }}
                </span>
                <span>
                    {{ __('Current bookmarks:') }} {{ $bookmarksCount }}
                </span>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="w-full sm:w-80">
                <input
                    type="text"
                    class="input input-bordered input-lg w-full"
                    placeholder="{{ __('Search by domain (e.g. youtube.com)') }}"
                    wire:model.live="filterDomain"
                    @pointerdown.stop
                />
            </div>
            <button
                type="button"
                class="btn btn-primary"
                @click="$refs.fileInput.click()"
            >
                {{ __('Import Bookmarks') }}
            </button>
            <button
                type="button"
                class="btn btn-outline"
                wire:click="regenerateAiLabels"
                @pointerdown.stop
            >
                {{ __('Regenerate labels') }}
            </button>
            <a class="btn btn-ghost" href="{{ route('bookmarks.index') }}">
                {{ __('View Dashboard') }}
            </a>
            <a
                class="btn btn-ghost"
                :href="manageCategoriesUrl"
            >
                {{ __('Manage Categories') }}
            </a>
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
    @if ($aiLabelStatus)
        <div class="mt-4 rounded-lg border border-neutral-200 bg-neutral-50 px-4 py-3 text-sm text-neutral-700">
            {{ $aiLabelStatus }}
        </div>
    @endif

    <div class="mt-6 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
        <h4 class="text-sm font-semibold text-neutral-700">{{ __('Bulk categorize') }}</h4>
        <p class="mt-2 text-sm text-neutral-500">
            {{ __('Select a website domain to categorize all matching bookmarks.') }}
        </p>
        <div class="mt-4 grid gap-3 sm:grid-cols-[2fr,2fr,1fr] sm:items-end">
            <div class="space-y-2">
                <label class="text-xs text-neutral-400">{{ __('Website domain') }}</label>
                <select class="select select-bordered w-full" wire:model="bulkDomain" @pointerdown.stop>
                    <option value="">{{ __('Select domain') }}</option>
                    @foreach ($this->domainOptions as $option)
                        <option value="{{ $option['domain'] }}">
                            {{ $option['domain'] }} ({{ $option['count'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-xs text-neutral-400">{{ __('Category') }}</label>
                <select class="select select-bordered w-full" wire:model="bulkCategoryId" @pointerdown.stop>
                    <option value="">{{ __('Select category') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button
                type="button"
                class="btn btn-primary w-full"
                wire:click="bulkAssignCategory"
                @pointerdown.stop
            >
                {{ __('Apply') }}
            </button>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-[2fr,1fr]">
        <div class="space-y-4">
            @if ($bookmarks->isEmpty())
                <div class="flex h-full flex-col items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-neutral-50 p-8 text-center">
                    <p class="text-lg font-medium text-neutral-700">{{ __('No bookmarks loaded yet.') }}</p>
                    <p class="mt-2 text-sm text-neutral-500">{{ __('Import a bookmarks HTML file to get started.') }}</p>
                </div>
            @else
                @foreach ($bookmarks as $bookmark)
                    <div
                        class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition"
                        :style="draggingId === {{ $bookmark->id }} ? dragStyle : ''"
                        @pointerdown="startDrag($event, {{ $bookmark->id }})"
                        @pointerup="endDrag({{ $bookmark->id }})"
                        @pointercancel="cancelDrag()"
                    >
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between relative">
                            @if ($bookmark->ai_label)
                                <span class="absolute right-3 top-3 rounded-full bg-primary-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-primary-700">
                                    {{ $bookmark->ai_label }}
                                </span>
                            @endif
                            <span class="absolute bottom-3 left-3 inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-800">
                                @if ($bookmark->url_status === 200)
                                    <svg viewBox="0 0 24 24" class="h-3 w-3 text-emerald-600" aria-hidden="true">
                                        <path d="M12 4l6 6h-4v10h-4V10H6z" fill="currentColor"></path>
                                    </svg>
                                    {{ __('Live') }}
                                @elseif (in_array($bookmark->url_status, [0, 404, 500], true))
                                    <svg viewBox="0 0 24 24" class="h-3 w-3 text-red-600" aria-hidden="true">
                                        <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                    </svg>
                                    {{ __('Down') }}
                                @else
                                    <svg viewBox="0 0 24 24" class="h-3 w-3 text-amber-600" aria-hidden="true">
                                        <path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                                        <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="2" fill="none"></circle>
                                    </svg>
                                    {{ __('Checking') }}
                                @endif
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs uppercase tracking-wide text-neutral-400">{{ __('Bookmark') }}</p>
                                <h3 class="mt-2 text-lg font-semibold text-neutral-900">
                                    {{ $bookmark->title ?? $bookmark->url }}
                                </h3>
                                <a
                                    class="break-all text-sm text-primary-600 hover:underline"
                                    href="{{ $bookmark->url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ $bookmark->url }}
                                </a>
                                @if ($bookmark->folder_path)
                                    <p class="mt-2 text-xs text-neutral-500">
                                        {{ __('Folder:') }} {{ $bookmark->folder_path }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex w-full flex-col gap-3 sm:w-56">
                                <span class="text-xs text-neutral-400">{{ __('Category') }}</span>
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
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    wire:click="categorize({{ $bookmark->id }})"
                                    @pointerdown.stop
                                >
                                    {{ __('Categorize') }}
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-outline btn-error"
                                    wire:click="markDeleted({{ $bookmark->id }})"
                                    @pointerdown.stop
                                >
                                    {{ __('Delete') }}
                                </button>
                                <span class="text-xs text-neutral-400">
                                    {{ __('Status:') }} {{ $bookmark->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm">
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
            showCategoryPicker: false,
            pendingBookmarkId: null,
            draggingId: null,
            dragStartX: 0,
            dragStartY: 0,
            dragDeltaX: 0,
            threshold: 90,

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
