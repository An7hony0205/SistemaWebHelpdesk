<?php

namespace App\Domains\Integrations\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = ['endpoint_id', 'event_name'];

    public function endpoint()
    {
        return $this->belongsTo(WebhookEndpoint::class, 'endpoint_id');
    }
}
