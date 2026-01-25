<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use Illuminate\View\View;

class BookmarksController extends Controller
{
    private const DEFAULT_CATEGORIES = [
        'Work/Professional',
        'Learning/Education',
        'Tech',
        'Tools',
        'Personal',
        'Finance',
        'Shopping',
        'Media/Entertainment',
        'News',
        'Social & Communication',
        'Health',
        'Travel',
        'Creative',
        'Reference',
        'Archive',
    ];

    public function index(): View
    {
        $userId = auth()->id();

        $this->ensureDefaultCategories($userId);

        $categories = BookmarkCategory::where('user_id', $userId)
            ->with(['bookmarks' => function ($query) {
                $query->where('status', '!=', Bookmark::STATUS_DELETED)
                    ->orderBy('updated_at', 'desc');
            }])
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        return view('bookmarks.index', [
            'categories' => $categories,
        ]);
    }

    public function inbox(): View
    {
        $userId = auth()->id();

        $this->ensureDefaultCategories($userId);

        return view('bookmarks.inbox');
    }

    private function ensureDefaultCategories(int $userId): void
    {
        $existing = BookmarkCategory::where('user_id', $userId)
            ->pluck('name')
            ->map(fn (string $name) => mb_strtolower($name))
            ->all();

        $sort = 0;
        foreach (self::DEFAULT_CATEGORIES as $name) {
            if (in_array(mb_strtolower($name), $existing, true)) {
                $sort++;
                continue;
            }

            BookmarkCategory::create([
                'user_id' => $userId,
                'name' => $name,
                'sort' => $sort,
            ]);
            $sort++;
        }
    }
}
