<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookmarkCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'sort',
    ];

    protected $casts = [
        'sort' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class, 'category_id');
    }
}
