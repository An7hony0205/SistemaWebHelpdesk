<?php

namespace App\Domains\Intelligence\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'openai_api_key', 'is_triage_enabled', 'is_sentiment_enabled',
    ];

    protected $casts = [
        'is_triage_enabled' => 'boolean',
        'is_sentiment_enabled' => 'boolean',
    ];

    protected $hidden = [
        'openai_api_key', // Nunca exponer la API key al frontend
    ];
}
