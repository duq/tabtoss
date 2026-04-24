<?php

namespace App\Filament\Dashboard\Pages;

use App\Jobs\CheckBookmarkUrlStatus;
use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TagsInput;
use Filament\Schemas\Components\Wizard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Saasykit\FilamentOnboarding\Pages\OnboardingPage;

class Onboarding extends OnboardingPage
{
    public array $categories = [];

    public string|array|null $bookmarks_file = null;

    public function mount(): void
    {
        if (method_exists(get_parent_class($this), 'mount')) {
            parent::mount();
        }

        $this->form->fill([
            'categories' => $this->categories,
            'bookmarks_file' => $this->bookmarks_file,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Wizard\Step::make(__('Welcome'))
                    ->schema([
                        Placeholder::make('welcome_message')
                            ->label('')
                            ->content(__('You are all set. In the next step, import your bookmarks, then create a few categories to organize them.')),
                    ]),
                Wizard\Step::make(__('Import bookmarks'))
                    ->schema([
                        FileUpload::make('bookmarks_file')
                            ->label(__('Bookmarks HTML file'))
                            ->acceptedFileTypes(['text/html'])
                            ->disk('local')
                            ->directory('onboarding/bookmarks')
                            ->maxFiles(1)
                            ->helperText(__('Upload your exported browser bookmarks (.html). Website status checks are queued and run in the background.')),
                    ]),
                Wizard\Step::make(__('Create categories'))
                    ->schema([
                        TagsInput::make('categories')
                            ->label(__('Bookmark categories'))
                            ->default([])
                            ->separator(',')
                            ->placeholder(__('Type a category and press Enter'))
                            ->helperText(__('Create categories you will use to organize imported bookmarks.')),
                    ]),
            ]),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        $userId = auth()->id();

        if (! $userId) {
            return;
        }

        $bookmarksFile = $data['bookmarks_file'] ?? null;
        if (is_string($bookmarksFile) && $bookmarksFile !== '') {
            $this->importBookmarksFromUploadedFile($bookmarksFile, $userId);
        }

        $categories = $this->normalizeCategoryNames($data['categories'] ?? []);

        $nextSort = (int) BookmarkCategory::where('user_id', $userId)->max('sort') + 1;
        foreach ($categories as $categoryName) {
            $existing = BookmarkCategory::where('user_id', $userId)
                ->whereRaw('LOWER(name) = ?', [Str::lower($categoryName)])
                ->exists();

            if ($existing) {
                continue;
            }

            BookmarkCategory::create([
                'user_id' => $userId,
                'name' => $categoryName,
                'sort' => $nextSort++,
            ]);
        }

        $this->onboarded();
    }

    private function importBookmarksFromUploadedFile(string $path, int $userId): void
    {
        $disk = Storage::disk('local');
        if (! $disk->exists($path)) {
            return;
        }

        $contents = $disk->get($path);
        $bookmarks = $this->extractBookmarks($contents);

        foreach ($bookmarks as $bookmark) {
            if (! filter_var($bookmark['url'], FILTER_VALIDATE_URL)) {
                continue;
            }

            $urlHash = hash('sha256', $bookmark['url']);

            $model = Bookmark::firstOrNew([
                'user_id' => $userId,
                'url_hash' => $urlHash,
            ]);
            $shouldCheckStatus = ! $model->exists || $model->url_checked_at === null;

            $model->url = $bookmark['url'];
            $model->title = $bookmark['title'] ?: $model->title;
            $model->folder_path = $bookmark['folder_path'] ?? $model->folder_path;
            $model->browser = $bookmark['browser'] ?? $model->browser;

            if (! $model->exists) {
                $model->status = Bookmark::STATUS_NEW;
            }

            $model->save();

            if ($shouldCheckStatus) {
                CheckBookmarkUrlStatus::dispatch($model->id);
            }
        }

        $disk->delete($path);
    }

    /**
     * @return array<int, array{title: string, url: string, folder_path: string|null, browser: string|null}>
     */
    private function extractBookmarks(string $html): array
    {
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html);
        libxml_clear_errors();

        $browser = $this->detectBrowser($dom);
        $links = $dom->getElementsByTagName('a');
        $items = [];

        foreach ($links as $link) {
            $url = trim((string) $link->getAttribute('href'));
            if ($url === '') {
                continue;
            }

            $items[] = [
                'title' => trim($link->textContent),
                'url' => $url,
                'folder_path' => null,
                'browser' => $browser,
            ];
        }

        return $items;
    }

    private function detectBrowser(\DOMDocument $dom): ?string
    {
        $title = Str::lower((string) ($dom->getElementsByTagName('title')->item(0)?->textContent ?? ''));

        if (str_contains($title, 'firefox')) {
            return 'firefox';
        }

        if (str_contains($title, 'chrome')) {
            return 'chrome';
        }

        return null;
    }

    /**
     * @param  mixed  $rawCategories
     * @return \Illuminate\Support\Collection<int, string>
     */
    private function normalizeCategoryNames(mixed $rawCategories)
    {
        return collect(is_array($rawCategories) ? $rawCategories : [])
            ->map(fn ($category) => trim((string) $category))
            ->map(fn (string $category) => preg_replace('/\s+/', ' ', $category))
            ->filter()
            ->map(fn (string $category) => Str::limit($category, 120, ''))
            ->unique(fn (string $name) => Str::lower($name))
            ->values();
    }
}

