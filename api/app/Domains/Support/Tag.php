<?php

namespace App\Domains\Support;

use App\Domains\Identity\Tenant;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Tag extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'color',
        'tenant_id',
    ];

    public function tickets()
    {
        return $this->belongsToMany(Ticket::class);
    }
}
