<?php

namespace App\Domains\Sla;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class SlaPolicy extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'is_active',
        'conditions'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'conditions' => 'array'
    ];

    public function targets()
    {
        return $this->hasMany(SlaTarget::class);
    }
}
