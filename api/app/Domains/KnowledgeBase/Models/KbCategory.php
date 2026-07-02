<?php

namespace App\Domains\KnowledgeBase\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Illuminate\Support\Str;

class KbCategory extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'parent_id', 'name', 'slug', 'description'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(KbCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(KbCategory::class, 'parent_id');
    }

    public function articles()
    {
        return $this->hasMany(KbArticle::class, 'category_id');
    }
}
