<?php

namespace App\Domains\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Ticket extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'category_id',
        'title',
        'description',
        'status_id',
        'priority_id',
        'assigned_to',
        'is_active',
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Domains\Identity\Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Domains\Identity\User::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(\App\Domains\Identity\User::class, 'assigned_to');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function sla()
    {
        return $this->hasOne(\App\Domains\Sla\TicketSla::class);
    }
}
