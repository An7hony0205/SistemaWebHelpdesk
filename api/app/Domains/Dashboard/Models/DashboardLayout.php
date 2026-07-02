<?php

namespace App\Domains\Dashboard\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardLayout extends Model
{
    protected $fillable = [
        'configurable_type',
        'configurable_id',
        'widgets_layout',
        'is_default',
    ];

    protected $casts = [
        'widgets_layout' => 'array',
        'is_default' => 'boolean',
    ];

    public function configurable()
    {
        return $this->morphTo();
    }
}
