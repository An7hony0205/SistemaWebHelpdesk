<?php

namespace App\Domains\Sla;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class TicketSla extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'ticket_id',
        'sla_policy_id',
        'first_response_due_at',
        'first_response_completed_at',
        'first_response_breached',
        'resolution_due_at',
        'resolution_completed_at',
        'resolution_breached',
        'paused_at',
        'accumulated_pause_minutes',
    ];

    protected $casts = [
        'first_response_due_at' => 'datetime',
        'first_response_completed_at' => 'datetime',
        'first_response_breached' => 'boolean',
        'resolution_due_at' => 'datetime',
        'resolution_completed_at' => 'datetime',
        'resolution_breached' => 'boolean',
        'paused_at' => 'datetime',
    ];

    public function policy()
    {
        return $this->belongsTo(SlaPolicy::class, 'sla_policy_id');
    }
}
