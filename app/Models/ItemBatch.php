<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'warehouse_id',
        'batch_number',
        'expired_date',
        'initial_qty',
        'current_qty',
        'purchase_price',
        'status',
        'is_active',
    ];

    protected $casts = [
        'expired_date' => 'date',
        'purchase_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stockCards()
    {
        return $this->hasMany(StockCard::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('current_qty', '>', 0);
    }

    public function scopeQuarantined($query)
    {
        return $query->where('status', 'quarantine');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Batches that are past their expiry date AND still have physical stock
     * on hand. A batch that's already been fully disposed (current_qty = 0)
     * has nothing left to act on, so it shouldn't keep showing up as an
     * active "expired" alert.
     */
    public function scopeExpired($query)
    {
        return $query->where('expired_date', '<=', now())
            ->where('current_qty', '>', 0);
    }

    public function scopeNearExpired($query, $days = 90)
    {
        return $query->where('expired_date', '>', now())
            ->where('expired_date', '<=', now()->addDays($days))
            ->where('current_qty', '>', 0);
    }

    public function getStatusLabelAttribute()
    {
        // A batch with nothing left in stock (fully used, transferred out,
        // or disposed) has no actionable expiry concern anymore — flagging
        // it as "EXPIRED" is misleading once there's no physical stock to
        // act on.
        if ($this->current_qty <= 0) {
            return (object) ['label' => 'HABIS/DIMUSNAHKAN', 'color' => 'bg-gray-100 text-gray-500 border-gray-200', 'urgency' => -1];
        }

        $daysToExpiry = now()->diffInDays($this->expired_date, false);

        if ($daysToExpiry <= 0) {
            return (object) ['label' => 'EXPIRED', 'color' => 'bg-red-100 text-red-700 border-red-200', 'urgency' => 4];
        }

        if ($daysToExpiry <= 30) {
            return (object) ['label' => 'CRITICAL (<30d)', 'color' => 'bg-rose-50 text-rose-600 border-rose-100 animate-pulse', 'urgency' => 3];
        }

        if ($daysToExpiry <= 90) {
            return (object) ['label' => 'NEAR EXPIRED', 'color' => 'bg-amber-50 text-amber-600 border-amber-100', 'urgency' => 2];
        }

        if ($daysToExpiry <= 180) {
            return (object) ['label' => 'MONITORING', 'color' => 'bg-blue-50 text-blue-600 border-blue-100', 'urgency' => 1];
        }

        return (object) ['label' => 'ACTIVE', 'color' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'urgency' => 0];
    }
}
