<?php

namespace App\Domains\Sla;

use App\Domains\Administration\Priority;
use Illuminate\Database\Eloquent\Model;

class SlaTarget extends Model
{
    protected $fillable = [
        'sla_policy_id',
        'priority_id',
        'first_response_time_minutes',
        'resolution_time_minutes',
    ];

    public function policy()
    {
        return $this->belongsTo(SlaPolicy::class, 'sla_policy_id');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
}
