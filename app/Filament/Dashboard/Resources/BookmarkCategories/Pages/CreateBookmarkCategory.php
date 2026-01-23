<?php

namespace App\Filament\Dashboard\Resources\BookmarkCategories\Pages;

use App\Filament\Dashboard\Resources\BookmarkCategories\BookmarkCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookmarkCategory extends CreateRecord
{
    protected static string $resource = BookmarkCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
