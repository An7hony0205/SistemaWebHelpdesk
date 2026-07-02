<?php

namespace App\Domains\Integrations\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDeliveryLog extends Model
{
    protected $fillable = [
        'endpoint_id', 'event_name', 'payload', 'response_status', 'response_body', 'execution_time_ms',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function endpoint()
    {
        return $this->belongsTo(WebhookEndpoint::class, 'endpoint_id');
    }
}
