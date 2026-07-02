<?php

namespace App\Domains\KnowledgeBase\Models;

use App\Domains\Identity\User;
use Illuminate\Database\Eloquent\Model;

class KbArticleVersion extends Model
{
    protected $fillable = ['article_id', 'version_number', 'title', 'content', 'created_by'];

    public function article()
    {
        return $this->belongsTo(KbArticle::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
