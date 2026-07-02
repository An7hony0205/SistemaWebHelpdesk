<?php

namespace App\Domains\Automations\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id', 'name', 'description', 'trigger_event', 'is_active', 'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function conditions()
    {
        return $this->hasMany(AutomationCondition::class, 'rule_id');
    }

    public function actions()
    {
        return $this->hasMany(AutomationAction::class, 'rule_id');
    }
}
