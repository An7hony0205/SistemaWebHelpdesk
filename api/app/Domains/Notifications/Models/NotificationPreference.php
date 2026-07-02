<?php

namespace App\Domains\Notifications\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'configurable_type',
        'configurable_id',
        'event_name',
        'channels',
    ];

    protected $casts = [
        'channels' => 'array',
    ];

    public function configurable()
    {
        return $this->morphTo();
    }
}
