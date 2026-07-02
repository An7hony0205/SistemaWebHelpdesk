<?php

namespace App\Domains\Automations\Models;

use Illuminate\Database\Eloquent\Model;

class AutomationCondition extends Model
{
    protected $fillable = ['rule_id', 'field', 'operator', 'value'];

    public function rule()
    {
        return $this->belongsTo(AutomationRule::class, 'rule_id');
    }
}
