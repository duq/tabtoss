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
}
