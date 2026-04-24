<?php

namespace App\Livewire\Bookmarks;

use App\Jobs\CheckBookmarkUrlStatus;
use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use OpenAI;
use OpenAI\Client;

class SwipeInbox extends Component
{
    use WithPagination;

    public int $stackSize = 15;

    public int $perPage = 200;

    public array $selectedCategories = [];

    public array $selectedBookmarkIds = [];

    public ?string $bulkDomain = null;

    public ?int $bulkCategoryId = null;

    public ?string $aiLabelStatus = null;

    public bool $confirmingBulkDelete = false;

    private const AI_LABELS_PER_REQUEST = 10;

    private const AI_MODEL = 'gpt-4o-mini';

    private const URL_STATUS_BATCH = 40;

    private const URL_STATUS_TTL_DAYS = 30;

    public function render()
    {
        $bookmarksPaginator = $this->getBookmarksPaginator();
        if ($bookmarksPaginator->lastPage() > 0 && $bookmarksPaginator->currentPage() > $bookmarksPaginator->lastPage()) {
            $this->setPage($bookmarksPaginator->lastPage());
            $bookmarksPaginator = $this->getBookmarksPaginator();
        }
        $bookmarks = $bookmarksPaginator->getCollection();

        $this->syncSelectedBookmarkIds($bookmarks);

        return view('livewire.bookmarks.swipe-inbox', [
            'bookmarks' => $bookmarks,
            'bookmarksPaginator' => $bookmarksPaginator,
            'visibleBookmarksCount' => $bookmarks->count(),
            'bookmarksCount' => $bookmarksPaginator->total(),
            'downBookmarksCount' => $bookmarks->filter(fn (Bookmark $bookmark) => $this->isDownStatus($bookmark->url_status))->count(),
            'uncheckedBookmarksCount' => $bookmarks->filter(fn (Bookmark $bookmark) => $bookmark->url_status === null)->count(),
            'aiLabeledBookmarksCount' => $bookmarks->filter(fn (Bookmark $bookmark) => ! empty($bookmark->ai_label))->count(),
            'selectedBookmarksCount' => count($this->selectedBookmarkIds),
            'importedCount' => Bookmark::where('user_id', auth()->id())->count(),
            'categories' => $this->getCategories(),
        ]);
    }

    public function applyAiLabelsManual(): void
    {
        $this->applyAiLabels();
    }

    public function importBookmarks(array $items): int
    {
        $userId = auth()->id();

        if (! $userId) {
            return 0;
        }

        $count = 0;

        foreach ($items as $item) {
            $validator = Validator::make($item, [
                'title' => ['nullable', 'string'],
                'url' => ['required', 'string'],
                'folder_path' => ['nullable', 'string'],
                'browser' => ['nullable', 'string'],
            ]);

            if ($validator->fails()) {
                continue;
            }

            $data = $validator->validated();

            if (! filter_var($data['url'], FILTER_VALIDATE_URL)) {
                continue;
            }

            $urlHash = hash('sha256', $data['url']);

            $bookmark = Bookmark::firstOrNew([
                'user_id' => $userId,
                'url_hash' => $urlHash,
            ]);
            $shouldCheckStatus = ! $bookmark->exists || $bookmark->url_checked_at === null;

            $bookmark->url = $data['url'];
            $bookmark->title = $data['title'] ?? $bookmark->title;
            $bookmark->folder_path = $data['folder_path'] ?? $bookmark->folder_path;
            $bookmark->browser = $data['browser'] ?? $bookmark->browser;

            if (! $bookmark->exists) {
                $bookmark->status = Bookmark::STATUS_NEW;
            }

            $bookmark->save();

            if ($shouldCheckStatus) {
                CheckBookmarkUrlStatus::dispatch($bookmark->id);
            }

            $count++;
        }

        return $count;
    }

    public function markDeleted(int $bookmarkId): void
    {
        $bookmark = $this->findBookmark($bookmarkId);
        if (! $bookmark) {
            return;
        }

        $bookmark->status = Bookmark::STATUS_DELETED;
        $bookmark->category_id = null;
        $bookmark->save();
        $this->removeSelectedBookmarkId($bookmarkId);
        $this->dispatch('bookmark-deleted', id: $bookmarkId);
        $this->dispatch('$refresh');
    }

    public function assignCategory(int $bookmarkId, int $categoryId): void
    {
        $bookmark = $this->findBookmark($bookmarkId);
        if (! $bookmark) {
            return;
        }

        $category = BookmarkCategory::where('user_id', auth()->id())
            ->where('id', $categoryId)
            ->first();

        if (! $category) {
            return;
        }

        $bookmark->category_id = $category->id;
        $bookmark->status = Bookmark::STATUS_KEPT;
        $bookmark->save();
        $this->removeSelectedBookmarkId($bookmarkId);
        $this->dispatch('$refresh');
    }

    public function categorize(int $bookmarkId): void
    {
        $categoryId = $this->selectedCategories[$bookmarkId] ?? null;
        if (! $categoryId) {
            return;
        }

        $this->assignCategory($bookmarkId, (int) $categoryId);
        unset($this->selectedCategories[$bookmarkId]);
    }

    public function bulkAssignCategory(): void
    {
        if (! $this->bulkDomain || ! $this->bulkCategoryId) {
            return;
        }

        $category = BookmarkCategory::where('user_id', auth()->id())
            ->where('id', $this->bulkCategoryId)
            ->first();

        if (! $category) {
            return;
        }

        $normalizedDomain = $this->normalizeDomain($this->bulkDomain);
        $this->bulkUpdateByDomain($normalizedDomain, function (array $ids) use ($category) {
            Bookmark::where('user_id', auth()->id())
                ->whereIn('id', $ids)
                ->update([
                    'category_id' => $category->id,
                    'status' => Bookmark::STATUS_KEPT,
                ]);
        });
    }

    public function selectAllDownBookmarks(): void
    {
        $this->confirmingBulkDelete = false;
        $this->selectedBookmarkIds = $this->getCurrentPageBookmarks()
            ->filter(fn (Bookmark $bookmark) => $this->isDownStatus($bookmark->url_status))
            ->pluck('id')
            ->map(fn (int $id) => $id)
            ->values()
            ->all();
    }

    public function clearSelectedBookmarks(): void
    {
        $this->confirmingBulkDelete = false;
        $this->selectedBookmarkIds = [];
    }

    public function updatedSelectedBookmarkIds(): void
    {
        $this->confirmingBulkDelete = false;
    }

    public function requestDeleteSelectedBookmarks(): void
    {
        if (count($this->selectedBookmarkIds) === 0) {
            return;
        }

        if (! $this->confirmingBulkDelete) {
            $this->confirmingBulkDelete = true;

            return;
        }

        $this->deleteSelectedBookmarks();
    }

    public function cancelDeleteSelectedBookmarks(): void
    {
        $this->confirmingBulkDelete = false;
    }

    private function deleteSelectedBookmarks(): void
    {
        $bookmarkIds = collect($this->selectedBookmarkIds)
            ->map(fn ($bookmarkId) => (int) $bookmarkId)
            ->filter()
            ->values()
            ->all();

        if ($bookmarkIds === []) {
            return;
        }

        Bookmark::where('user_id', auth()->id())
            ->where('status', Bookmark::STATUS_NEW)
            ->whereIn('id', $bookmarkIds)
            ->update([
                'status' => Bookmark::STATUS_DELETED,
                'category_id' => null,
            ]);

        $this->confirmingBulkDelete = false;
        $this->selectedBookmarkIds = [];
        $this->dispatch('bookmarks-deleted', ids: $bookmarkIds);
        $this->dispatch('$refresh');
    }

    public function fetchBookmarks(): array
    {
        return $this->getCurrentPageBookmarks()
            ->map(function (Bookmark $bookmark) {
                return [
                    'id' => $bookmark->id,
                    'title' => $bookmark->title ?? $bookmark->url,
                    'url' => $bookmark->url,
                    'folder_path' => $bookmark->folder_path,
                ];
            })
            ->values()
            ->all();
    }

    private function getBookmarksPaginator(): LengthAwarePaginator
    {
        return Bookmark::where('user_id', auth()->id())
            ->where('status', Bookmark::STATUS_NEW)
            ->orderBy('updated_at', 'desc')
            ->paginate($this->perPage);
    }

    private function getCurrentPageBookmarks(): Collection
    {
        return $this->getBookmarksPaginator()->getCollection();
    }

    public function getDomainOptionsProperty(): array
    {
        $counts = [];

        foreach ($this->newBookmarksCursor() as $bookmark) {
            $domain = $this->normalizeDomain($bookmark->url);
            if ($domain === '') {
                continue;
            }
            $counts[$domain] = ($counts[$domain] ?? 0) + 1;
        }

        arsort($counts);

        return collect($counts)->map(function (int $count, string $domain) {
            return [
                'domain' => $domain,
                'count' => $count,
            ];
        })->values()->all();
    }

    private function getCategories(): Collection
    {
        return BookmarkCategory::where('user_id', auth()->id())
            ->orderBy('sort')
            ->orderBy('name')
            ->get();
    }

    private function findBookmark(int $bookmarkId): ?Bookmark
    {
        return Bookmark::where('user_id', auth()->id())
            ->where('id', $bookmarkId)
            ->first();
    }

    private function normalizeDomain(string $url): string
    {
        $url = trim($url);
        if (! preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://'.$url;
        }

        $host = parse_url($url, PHP_URL_HOST);
        if ($host === null) {
            $host = $url;
        }

        return Str::lower(preg_replace('/^www\./', '', $host));
    }

    private function isDownStatus(?int $status): bool
    {
        return in_array($status, [0, 404, 500], true);
    }

    private function newBookmarksCursor(array $columns = ['*'])
    {
        return Bookmark::where('user_id', auth()->id())
            ->where('status', Bookmark::STATUS_NEW)
            ->orderBy('updated_at', 'desc')
            ->select($columns)
            ->cursor();
    }

    private function bulkUpdateByDomain(string $domain, callable $updater): void
    {
        $ids = [];

        foreach ($this->newBookmarksCursor() as $bookmark) {
            if ($this->normalizeDomain($bookmark->url) !== $domain) {
                continue;
            }

            $ids[] = $bookmark->id;
            if (count($ids) >= 500) {
                $updater($ids);
                $ids = [];
            }
        }

        if ($ids !== []) {
            $updater($ids);
        }
    }

    private function syncSelectedBookmarkIds(Collection $bookmarks): void
    {
        $visibleIds = $bookmarks->pluck('id');

        $this->selectedBookmarkIds = collect($this->selectedBookmarkIds)
            ->map(fn ($bookmarkId) => (int) $bookmarkId)
            ->filter()
            ->intersect($visibleIds)
            ->values()
            ->all();
    }

    private function removeSelectedBookmarkId(int $bookmarkId): void
    {
        $this->selectedBookmarkIds = collect($this->selectedBookmarkIds)
            ->map(fn ($selectedId) => (int) $selectedId)
            ->reject(fn (int $selectedId) => $selectedId === $bookmarkId)
            ->values()
            ->all();
    }

    private function applyAiLabels(): void
    {
        $apiKey = config('services.openai.key');
        if (! $apiKey) {
            $this->aiLabelStatus = __('OpenAI API key is not configured.');

            return;
        }

        $categories = $this->getCategories();
        if ($categories->isEmpty()) {
            $this->aiLabelStatus = __('No categories found for labeling.');

            return;
        }

        $bookmarks = Bookmark::where('user_id', auth()->id())
            ->where('status', Bookmark::STATUS_NEW)
            ->whereNull('ai_label')
            ->limit(self::AI_LABELS_PER_REQUEST)
            ->get(['id', 'title', 'url']);

        if ($bookmarks->isEmpty()) {
            $this->aiLabelStatus = __('No bookmarks need AI labels.');

            return;
        }

        $client = OpenAI::client($apiKey);
        $categoryNames = $categories->pluck('name')->values()->all();

        foreach ($bookmarks as $bookmark) {
            $label = $this->generateAiLabel($client, $bookmark, $categoryNames);
            if ($label === null) {
                continue;
            }

            $bookmark->ai_label = $label;
            $bookmark->save();
        }

        $this->aiLabelStatus = __('AI labels updated.');
    }

    private function applyUrlStatuses(Collection $bookmarks): void
    {
        $cutoff = CarbonImmutable::now()->subDays(self::URL_STATUS_TTL_DAYS);

        $targets = $bookmarks->filter(function (Bookmark $bookmark) use ($cutoff) {
            if ($bookmark->url_checked_at === null) {
                return true;
            }

            return $bookmark->url_checked_at->lt($cutoff);
        })->take(self::URL_STATUS_BATCH);

        foreach ($targets as $bookmark) {
            CheckBookmarkUrlStatus::dispatch($bookmark->id);
        }
    }

    public function regenerateAiLabels(): void
    {
        Bookmark::where('user_id', auth()->id())
            ->where('status', Bookmark::STATUS_NEW)
            ->update([
                'ai_label' => null,
            ]);

        $this->applyAiLabels();
    }

    public function checkUrlStatuses(): void
    {
        $bookmarks = $this->getCurrentPageBookmarks();
        $this->applyUrlStatuses($bookmarks);
    }

    private function generateAiLabel(Client $client, Bookmark $bookmark, array $categories): ?string
    {
        $categoryList = implode(', ', $categories);

        $prompt = <<<PROMPT
You are labeling a bookmark with a single category from this list:
{$categoryList}

Bookmark:
Title: {$bookmark->title}
URL: {$bookmark->url}

Return only the category name from the list, no extra text.
PROMPT;

        try {
            $response = $client->chat()->create([
                'model' => self::AI_MODEL,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a classification assistant.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'temperature' => 0,
            ]);
        } catch (\Throwable $exception) {
            return null;
        }

        $result = trim($response->choices[0]->message->content ?? '');
        if ($result === '') {
            return null;
        }

        foreach ($categories as $category) {
            if (strcasecmp($category, $result) === 0) {
                return $category;
            }
        }

        return null;
    }
}
