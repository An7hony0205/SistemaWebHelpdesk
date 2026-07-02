<?php

namespace App\Domains\Support;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

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
