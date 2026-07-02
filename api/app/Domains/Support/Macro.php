<?php

namespace App\Domains\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Macro extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'title',
        'content',
        'is_active',
    ];

    public function tenant()
    {
        return $this->belongsTo(\App\Domains\Identity\Tenant::class);
    }
}
