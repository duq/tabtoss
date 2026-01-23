<?php

namespace App\Filament\Dashboard\Resources\BookmarkCategories\RelationManagers;

use App\Models\Bookmark;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookmarksRelationManager extends RelationManager
{
    protected static string $relationship = 'bookmarks';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('Bookmarks'))
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->formatStateUsing(fn (?string $state, Bookmark $record) => $state ?? $record->url)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->label(__('URL'))
                    ->url(fn (Bookmark $record) => $record->url, true)
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime(config('app.datetime_format'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->recordActions([
            ])
            ->toolbarActions([
            ]);
    }
}
