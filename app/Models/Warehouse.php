<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'is_main',
        'is_active',
        'pic_name',
        'pic_phone',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function receivings()
    {
        return $this->hasMany(Receiving::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isMain(): bool
    {
        return $this->is_main;
    }
}
