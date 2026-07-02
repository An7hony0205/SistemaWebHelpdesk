<?php

namespace App\Domains\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class Category extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'description', 'is_active'];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
