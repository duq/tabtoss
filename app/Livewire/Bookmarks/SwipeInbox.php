<?php

namespace App\Livewire\Bookmarks;

use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class SwipeInbox extends Component
{
    public int $stackSize = 15;
    public array $selectedCategories = [];
    public ?string $bulkDomain = null;
    public ?int $bulkCategoryId = null;

    public function render()
    {
        $bookmarks = $this->getBookmarks();

        return view('livewire.bookmarks.swipe-inbox', [
            'bookmarks' => $bookmarks,
            'bookmarksCount' => $bookmarks->count(),
            'importedCount' => Bookmark::where('user_id', auth()->id())->count(),
            'categories' => $this->getCategories(),
        ]);
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

            $bookmark->url = $data['url'];
            $bookmark->title = $data['title'] ?? $bookmark->title;
            $bookmark->folder_path = $data['folder_path'] ?? $bookmark->folder_path;
            $bookmark->browser = $data['browser'] ?? $bookmark->browser;

            if (! $bookmark->exists) {
                $bookmark->status = Bookmark::STATUS_NEW;
            }

            $bookmark->save();
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

    public function fetchBookmarks(): array
    {
        return $this->getBookmarks()
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

    private function getBookmarks(): Collection
    {
        return Bookmark::where('user_id', auth()->id())
            ->where('status', '!=', Bookmark::STATUS_DELETED)
            ->orderBy('updated_at', 'desc')
            ->get();
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
        $host = parse_url($url, PHP_URL_HOST);
        if ($host === null) {
            $host = $url;
        }

        return strtolower(preg_replace('/^www\./', '', $host));
    }

    private function newBookmarksCursor()
    {
        return Bookmark::where('user_id', auth()->id())
            ->where('status', Bookmark::STATUS_NEW)
            ->select(['id', 'url'])
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
}
