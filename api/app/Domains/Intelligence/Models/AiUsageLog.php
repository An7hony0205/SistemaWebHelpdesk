<?php

namespace App\Domains\Intelligence\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    protected $fillable = [
        'tenant_id', 'feature', 'prompt_tokens', 'completion_tokens', 'estimated_cost_usd'
    ];
}
