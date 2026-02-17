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

    /**
     * Check if this service unit is for inpatient (Rawat Inap)
     */
    public function isInpatient(): bool
    {
        return $this->type === 'ruangan';
    }

    /**
     * Check if this service unit is for outpatient (Rawat Jalan)
     */
    public function isOutpatient(): bool
    {
        return in_array($this->type, ['poli', 'instalasi']);
    }

    /**
     * Get patient type code (rj/ri) for prescriptions
     */
    public function getPatientTypeCode(): string
    {
        return $this->isInpatient() ? 'ri' : 'rj';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInpatient($query)
    {
        return $query->where('type', 'ruangan');
    }

    public function scopeOutpatient($query)
    {
        return $query->whereIn('type', ['poli', 'instalasi']);
    }
}
