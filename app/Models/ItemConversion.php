<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'from_unit_id',
        'to_unit_id',
        'conversion_factor',
        'is_base_unit',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'conversion_factor' => 'decimal:2',
            'is_base_unit' => 'boolean',
        ];
    }

    // Relations
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function fromUnit()
    {
        return $this->belongsTo(ItemUnit::class, 'from_unit_id');
    }

    public function toUnit()
    {
        return $this->belongsTo(ItemUnit::class, 'to_unit_id');
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
    public function scopeBaseUnit($query)
    {
        return $query->where('is_base_unit', true);
    }

    public function scopeByItem($query, $itemId)
    {
        return $query->where('item_id', $itemId);
    }

    // Helpers
    public function getConversionTextAttribute(): string
    {
        return "1 {$this->fromUnit->name} = {$this->conversion_factor} {$this->toUnit->name}";
    }
}