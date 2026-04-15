<?php

namespace Tests\Feature\Livewire\Bookmarks;

use App\Livewire\Bookmarks\SwipeInbox;
use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use Livewire\Livewire;
use Tests\Feature\FeatureTest;

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

    public function test_select_all_down_bookmarks_can_be_deleted_in_bulk(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $downBookmarkOne = Bookmark::create([
            'user_id' => $user->id,
            'title' => 'Down One',
            'url' => 'https://down-one.example.com',
            'url_hash' => hash('sha256', 'https://down-one.example.com'),
            'status' => Bookmark::STATUS_NEW,
            'url_status' => 404,
        ]);

        $downBookmarkTwo = Bookmark::create([
            'user_id' => $user->id,
            'title' => 'Down Two',
            'url' => 'https://down-two.example.com',
            'url_hash' => hash('sha256', 'https://down-two.example.com'),
            'status' => Bookmark::STATUS_NEW,
            'url_status' => 500,
        ]);

        $liveBookmark = Bookmark::create([
            'user_id' => $user->id,
            'title' => 'Live',
            'url' => 'https://live.example.com',
            'url_hash' => hash('sha256', 'https://live.example.com'),
            'status' => Bookmark::STATUS_NEW,
            'url_status' => 200,
        ]);

        Livewire::test(SwipeInbox::class)
            ->call('selectAllDownBookmarks')
            ->call('deleteSelectedBookmarks');

        $this->assertDatabaseHas('bookmarks', [
            'id' => $downBookmarkOne->id,
            'status' => Bookmark::STATUS_DELETED,
        ]);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $downBookmarkTwo->id,
            'status' => Bookmark::STATUS_DELETED,
        ]);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $liveBookmark->id,
            'status' => Bookmark::STATUS_NEW,
        ]);
    }

}
