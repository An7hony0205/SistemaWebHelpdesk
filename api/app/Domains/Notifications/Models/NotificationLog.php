<?php

namespace App\Domains\Notifications\Models;

use App\Domains\Identity\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

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
