@php
    $categoryOptions = $categories->map(fn ($category) => [
        'id' => $category->id,
        'name' => $category->name,
    ])->values();
@endphp

<div
    class="mx-auto w-full max-w-4xl"
    x-data="bookmarkImport(@js([
        'manageCategoriesUrl' => route('filament.dashboard.resources.bookmark-categories.index'),
    ]))"
>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-semibold text-primary-900">{{ __('Bookmark Inbox') }}</h2>
            <p class="text-sm text-neutral-500">
                {{ __('Import your bookmarks and swipe to organize them.') }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <button
                type="button"
                class="btn btn-primary"
                @click="$refs.fileInput.click()"
            >
                {{ __('Import Bookmarks') }}
            </button>
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

    <div class="mt-8 grid gap-6 lg:grid-cols-[2fr,1fr]">
        <div class="space-y-4">
            @if ($bookmarks->isEmpty())
                <div class="flex h-full flex-col items-center justify-center rounded-2xl border border-dashed border-neutral-200 bg-neutral-50 p-8 text-center">
                    <p class="text-lg font-medium text-neutral-700">{{ __('No bookmarks loaded yet.') }}</p>
                    <p class="mt-2 text-sm text-neutral-500">{{ __('Import a bookmarks HTML file to get started.') }}</p>
                </div>
            @else
                @foreach ($bookmarks as $bookmark)
                    <div class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
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
                                >
                                    {{ __('Categorize') }}
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
                <li>{{ __('Use the dashboard to manage categories anytime.') }}</li>
            </ul>
        </div>
    </div>
</div>

<script>
    function bookmarkImport({ manageCategoriesUrl }) {
        return {
            manageCategoriesUrl,
            importStatus: null,

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

        };
    }
</script>
