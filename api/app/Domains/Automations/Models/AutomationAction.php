<?php

namespace App\Domains\Automations\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationAction extends Model
{
    protected $fillable = ['rule_id', 'action_type', 'action_payload'];

    protected $casts = [
        'action_payload' => 'array',
    ];

    public function rule()
    {
        return $this->belongsTo(AutomationRule::class, 'rule_id');
    }
}
