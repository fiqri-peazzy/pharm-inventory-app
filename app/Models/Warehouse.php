<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'is_main',
        'is_active',
        'pic_name',
        'pic_phone',
        'address',
        'created_by',
        'updated_by',
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
        return $this->hasMany(User::class, 'warehouse_id');
    }

    public function servedUnits()
    {
        return $this->hasMany(ServiceUnit::class, 'default_warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function batches()
    {
        return $this->hasMany(ItemBatch::class);
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
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'gudang_utama' => 'Gudang Utama',
            'depo_farmasi' => 'Depo Farmasi',
            'depo_ok' => 'Depo OK',
            'depo_igd' => 'Depo IGD',
            'depo_ranap' => 'Depo Rawat Inap',
            'depo_rajal' => 'Depo Rawat Jalan',
            default => $this->type,
        };
    }
}