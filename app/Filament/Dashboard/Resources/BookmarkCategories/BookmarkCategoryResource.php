<?php

namespace App\Filament\Dashboard\Resources\BookmarkCategories;

use App\Filament\Dashboard\Resources\BookmarkCategories\Pages\CreateBookmarkCategory;
use App\Filament\Dashboard\Resources\BookmarkCategories\Pages\EditBookmarkCategory;
use App\Filament\Dashboard\Resources\BookmarkCategories\Pages\ListBookmarkCategories;
use App\Filament\Dashboard\Resources\BookmarkCategories\RelationManagers\BookmarksRelationManager;
use App\Models\BookmarkCategory;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class BookmarkCategoryResource extends Resource
{
    protected static ?string $model = BookmarkCategory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bookmark';

    protected static ?int $navigationSort = 50;

    public static function getNavigationGroup(): ?string
    {
        return __('Bookmarks');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Bookmark Categories');
    }

    public static function getModelLabel(): string
    {
        return __('Bookmark Category');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: function (Unique $rule) {
                                return $rule->where('user_id', auth()->id());
                            }
                        ),
                    TextInput::make('sort')
                        ->numeric()
                        ->default(0)
                        ->minValue(0),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sort')
                    ->label(__('Sort'))
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->dateTime(config('app.datetime_format'))
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('sort');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookmarkCategories::route('/'),
            'create' => CreateBookmarkCategory::route('/create'),
            'edit' => EditBookmarkCategory::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            BookmarksRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
