<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'category',
        'default_warehouse_id',
        'building',
        'floor',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function defaultWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'default_warehouse_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Helpers
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'poli' => 'Poliklinik',
            'ruangan' => 'Ruang Rawat Inap',
            'instalasi' => 'Instalasi Darurat/OK',
            default => $this->type,
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
