<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use OpenAI;
use OpenAI\Client;

class BookmarkAiLabelsSeeder extends Seeder
{
    private const AI_MODEL = 'gpt-4o-mini';

    public function run(): void
    {
        Log::info('BookmarkAiLabelsSeeder: started');
        $apiKey = config('services.openai.key');
        if (! $apiKey) {
            Log::warning('BookmarkAiLabelsSeeder: missing OPENAI_API_KEY');
            return;
        }

        $bookmarks = Bookmark::whereNull('ai_label')
            ->limit(10)
            ->get(['id', 'user_id', 'title', 'url']);

        if ($bookmarks->isEmpty()) {
            Log::info('BookmarkAiLabelsSeeder: no bookmarks found for labeling');
            return;
        }

        $client = OpenAI::client($apiKey);

        foreach ($bookmarks as $bookmark) {
            $categories = BookmarkCategory::where('user_id', $bookmark->user_id)
                ->orderBy('sort')
                ->orderBy('name')
                ->pluck('name')
                ->values()
                ->all();

            if ($categories === []) {
                Log::info('BookmarkAiLabelsSeeder: no categories for user', [
                    'user_id' => $bookmark->user_id,
                    'bookmark_id' => $bookmark->id,
                ]);
                continue;
            }

            $label = $this->generateAiLabel($client, $bookmark->title, $bookmark->url, $categories);
            if ($label === null) {
                Log::warning('BookmarkAiLabelsSeeder: label generation failed', [
                    'bookmark_id' => $bookmark->id,
                ]);
                continue;
            }

            $bookmark->ai_label = $label;
            $bookmark->save();
            Log::info('BookmarkAiLabelsSeeder: labeled bookmark', [
                'bookmark_id' => $bookmark->id,
                'label' => $label,
            ]);
        }

        Log::info('BookmarkAiLabelsSeeder: finished');
    }

    private function generateAiLabel(Client $client, ?string $title, string $url, array $categories): ?string
    {
        $categoryList = implode(', ', $categories);

        $prompt = <<<PROMPT
You are labeling a bookmark with a single category from this list:
{$categoryList}

Bookmark:
Title: {$title}
URL: {$url}

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
            Log::error('BookmarkAiLabelsSeeder: OpenAI request failed', [
                'error' => $exception->getMessage(),
            ]);
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
