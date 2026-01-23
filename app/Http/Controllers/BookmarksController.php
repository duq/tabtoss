<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use Illuminate\View\View;

class BookmarksController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

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
}
