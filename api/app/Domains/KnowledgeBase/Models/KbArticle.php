<?php

namespace App\Domains\KnowledgeBase\Models;

use App\Domains\Identity\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KbArticle extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'category_id', 'title', 'slug', 'excerpt', 'content',
        'status', 'author_id', 'editor_id', 'approver_id', 'published_at',
        'expires_at', 'metadata', 'views_count', 'upvotes', 'downvotes',
    ];

    protected $casts = [
        'metadata' => 'array',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($article) {
            if (! $article->slug) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(KbCategory::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function versions()
    {
        return $this->hasMany(KbArticleVersion::class, 'article_id');
    }

    public function tags()
    {
        return $this->belongsToMany(KbTag::class, 'kb_article_tag', 'article_id', 'tag_id');
    }
}
