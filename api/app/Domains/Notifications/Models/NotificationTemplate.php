<?php

namespace App\Domains\Notifications\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

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
