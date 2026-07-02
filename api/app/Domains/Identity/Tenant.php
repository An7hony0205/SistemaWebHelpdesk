<?php

namespace App\Domains\Identity;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'domain'];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function getTimezone(): string
    {
        return $this->settings['timezone'] ?? 'UTC';
    }

    public function getBusinessHours(): array
    {
        // Formato esperado: ['monday' => ['09:00-18:00'], ...]
        return $this->settings['business_hours'] ?? [
            'monday' => ['09:00-18:00'],
            'tuesday' => ['09:00-18:00'],
            'wednesday' => ['09:00-18:00'],
            'thursday' => ['09:00-18:00'],
            'friday' => ['09:00-18:00'],
        ];
    }

    public function getHolidays(): array
    {
        // Arreglo de fechas strings YYYY-MM-DD
        return $this->settings['holidays'] ?? [];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
