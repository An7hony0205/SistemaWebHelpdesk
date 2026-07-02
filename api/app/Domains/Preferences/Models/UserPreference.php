<?php

namespace App\Domains\Preferences\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Identity\User;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
