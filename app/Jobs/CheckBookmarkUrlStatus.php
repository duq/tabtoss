<?php

namespace App\Jobs;

use App\Models\Bookmark;
use App\Services\BookmarkUrlStatusService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckBookmarkUrlStatus implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $bookmarkId,
    ) {}

    public function handle(BookmarkUrlStatusService $service): void
    {
        $bookmark = Bookmark::find($this->bookmarkId);

        if (! $bookmark) {
            return;
        }

        $service->checkAndUpdate($bookmark);
    }
}

