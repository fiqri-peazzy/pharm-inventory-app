<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'address',
        'phone',
        'email',
        'contact_person',
        'npwp',
        'tax_status',
        'payment_term',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'payment_term' => 'integer',
        ];
    }

    // Relations
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function prices()
    {
        return $this->hasMany(ItemPrice::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePkp($query)
    {
        return $query->where('tax_status', 'pkp');
    }

    // Helpers
    public function getTypeNameAttribute(): string
    {
        return match($this->type) {
            'pbf' => 'PBF (Pedagang Besar Farmasi)',
            'distributor' => 'Distributor',
            'manufaktur' => 'Manufaktur',
            'toko' => 'Toko',
            default => $this->type,
        };
    }

    public function isPkp(): bool
    {
        return $this->tax_status === 'pkp';
    }
}