<?php

namespace App\Domains\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class NotificationTemplate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'event_name',
        'channel',
        'locale',
        'subject_template',
        'body_template',
        'version',
    ];
}
