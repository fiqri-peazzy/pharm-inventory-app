<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'email',
        'employee_id',
        'phone',
        'warehouse_id',
        'is_active',
        'password',
        'last_login_at',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function receivings()
    {
        return $this->hasMany(Receiving::class, 'created_by');
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class, 'created_by');
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class, 'created_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    public function getHomeRoute(): string
    {
        // High-level access -> Global Inventory Dashboard
        if ($this->hasAnyRole(['super-admin', 'kepala-farmasi', 'direktur', 'bupati'])) {
            return route('inventory.dashboard');
        }

        // Specific Roles (Doctor/Pharmacist)
        if ($this->hasAnyRole(['doctor', 'apoteker'])) {
            // If they have a warehouse assigned, go there, otherwise clinical dashboard
            if ($this->warehouse_id) {
                return route('inventory.dashboard', ['warehouse' => $this->warehouse_id]);
            }
            return route('clinical.prescriptions.index');
        }

        // Warehouse specific access -> Warehouse Dashboard
        if ($this->warehouse_id) {
            return route('inventory.dashboard', ['warehouse' => $this->warehouse_id]);
        }

        return route('dashboard');
    }
}