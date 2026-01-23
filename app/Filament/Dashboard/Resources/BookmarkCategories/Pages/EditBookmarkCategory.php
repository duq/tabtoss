<?php

namespace App\Filament\Dashboard\Resources\BookmarkCategories\Pages;

use App\Filament\Dashboard\Resources\BookmarkCategories\BookmarkCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditBookmarkCategory extends EditRecord
{
    protected static string $resource = BookmarkCategoryResource::class;
}
