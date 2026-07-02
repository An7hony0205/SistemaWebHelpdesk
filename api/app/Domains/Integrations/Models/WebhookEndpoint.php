<?php

namespace App\Domains\Integrations\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class WebhookEndpoint extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'url', 'secret', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function events()
    {
        return $this->hasMany(WebhookEvent::class, 'endpoint_id');
    }

    public function logs()
    {
        return $this->hasMany(WebhookDeliveryLog::class, 'endpoint_id');
    }
}
