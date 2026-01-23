<?php

namespace Tests\Feature\Livewire\Bookmarks;

use App\Livewire\Bookmarks\SwipeInbox;
use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use Tests\Feature\FeatureTest;
use Livewire\Livewire;

class SwipeInboxTest extends FeatureTest
{
    public function test_import_bookmarks_creates_records_for_user(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $items = [
            [
                'title' => 'Example',
                'url' => 'https://example.com',
                'folder_path' => 'Bookmarks Bar',
                'browser' => 'chrome',
            ],
        ];

        Livewire::test(SwipeInbox::class)
            ->call('importBookmarks', $items);

        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'url' => 'https://example.com',
            'status' => Bookmark::STATUS_NEW,
        ]);
    }

    public function test_swipe_left_marks_bookmark_deleted(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $bookmark = Bookmark::create([
            'user_id' => $user->id,
            'title' => 'Example',
            'url' => 'https://example.com',
            'url_hash' => hash('sha256', 'https://example.com'),
            'status' => Bookmark::STATUS_NEW,
        ]);

        Livewire::test(SwipeInbox::class)
            ->call('markDeleted', $bookmark->id);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'status' => Bookmark::STATUS_DELETED,
            'category_id' => null,
        ]);
    }

    public function test_swipe_right_assigns_category(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $bookmark = Bookmark::create([
            'user_id' => $user->id,
            'title' => 'Example',
            'url' => 'https://example.com',
            'url_hash' => hash('sha256', 'https://example.com'),
            'status' => Bookmark::STATUS_NEW,
        ]);

        $category = BookmarkCategory::create([
            'user_id' => $user->id,
            'name' => 'Reading',
            'sort' => 0,
        ]);

        Livewire::test(SwipeInbox::class)
            ->call('assignCategory', $bookmark->id, $category->id);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'status' => Bookmark::STATUS_KEPT,
            'category_id' => $category->id,
        ]);
    }
}
