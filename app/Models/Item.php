<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'nie_number',
        'barcode',
        'name',
        'generic_name',
        'item_category_id',
        'manufacturer',
        'item_unit_id',
        'is_prescription',
        'is_consignment',
        'is_active',
        'storage_condition',
        'fornas_status',
        'fornas_code',
        'abc_classification',
        'ven_classification',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_prescription' => 'boolean',
            'is_consignment' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function category()
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }

    public function warehouseSettings()
    {
        return $this->hasMany(ItemWarehouseSetting::class);
    }

    public function unit()
    {
        return $this->belongsTo(ItemUnit::class, 'item_unit_id');
    }

    public function conversions()
    {
        return $this->hasMany(ItemConversion::class);
    }

    public function prices()
    {
        return $this->hasMany(ItemPrice::class);
    }

    public function purchaseRequestDetails()
    {
        return $this->hasMany(PurchaseRequestDetail::class);
    }

    public function purchaseOrderDetails()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function batches()
    {
        return $this->hasMany(ItemBatch::class);
    }

    public function stockCards()
    {
        return $this->hasMany(StockCard::class);
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

    public function scopePrescription($query)
    {
        return $query->where('is_prescription', true);
    }

    public function scopeNonPrescription($query)
    {
        return $query->where('is_prescription', false);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('item_category_id', $categoryId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('generic_name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    // Helpers
    public function getStorageConditionNameAttribute(): string
    {
        return match($this->storage_condition) {
            'suhu_ruang' => 'Suhu Ruang',
            'kulkas' => 'Kulkas (2-8°C)',
            'freezer' => 'Freezer (<-15°C)',
            default => $this->storage_condition,
        };
    }

    public function getFullNameAttribute(): string
    {
        if ($this->generic_name) {
            return $this->name . ' (' . $this->generic_name . ')';
        }
        return $this->name;
    }

    public function isFornas(): bool
    {
        return !empty($this->fornas_code);
    }
}