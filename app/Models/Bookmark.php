<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_KEPT = 'kept';
    public const STATUS_DELETED = 'deleted';

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'url',
        'url_hash',
        'folder_path',
        'browser',
        'status',
        'ai_label',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookmarkCategory::class, 'category_id');
    }
}
