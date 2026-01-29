<?php

namespace Tests\Feature\Livewire\Bookmarks;

use App\Livewire\Bookmarks\SwipeInbox;
use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use Database\Seeders\Testing\TestingDatabaseSeeder;
use Tests\Feature\FeatureTest;
use Livewire\Livewire;

class BulkCategorizeTest extends FeatureTest
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        $this->artisan('migrate:fresh');
        $this->seed(TestingDatabaseSeeder::class);
    }

    public function test_bulk_categorize_updates_current_bookmarks_count(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $category = BookmarkCategory::create([
            'user_id' => $user->id,
            'name' => 'Programming',
            'sort' => 0,
        ]);

        $bookmarkOne = Bookmark::create([
            'user_id' => $user->id,
            'title' => 'GitHub Repo',
            'url' => 'https://github.com/example/repo',
            'url_hash' => hash('sha256', 'https://github.com/example/repo'),
            'status' => Bookmark::STATUS_NEW,
        ]);

        $bookmarkTwo = Bookmark::create([
            'user_id' => $user->id,
            'title' => 'GitHub Org',
            'url' => 'https://github.com/org',
            'url_hash' => hash('sha256', 'https://github.com/org'),
            'status' => Bookmark::STATUS_NEW,
        ]);

        Livewire::test(SwipeInbox::class)
            ->set('bulkDomain', 'github.com')
            ->set('bulkCategoryId', $category->id)
            ->call('bulkAssignCategory');

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmarkOne->id,
            'status' => Bookmark::STATUS_KEPT,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmarkTwo->id,
            'status' => Bookmark::STATUS_KEPT,
            'category_id' => $category->id,
        ]);

        Livewire::test(SwipeInbox::class)
            ->assertSee('Current bookmarks: 0');
    }
}
