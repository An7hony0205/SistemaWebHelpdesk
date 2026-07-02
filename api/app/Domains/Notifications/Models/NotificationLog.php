<?php

namespace App\Domains\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use App\Domains\Identity\User;

class NotificationLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'event_name',
        'channel',
        'status',
        'error_message',
        'payload',
        'sent_at',
        'retry_count',
        'processing_time_ms',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
