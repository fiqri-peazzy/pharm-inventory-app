<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemWarehouseSetting extends Model
{
    protected $fillable = [
        'item_id',
        'warehouse_id',
        'min_stock',
        'max_stock',
        'reorder_point',
        'safety_stock',
        'average_daily_usage',
        'lead_time_days',
        'usage_rate_per_day',
        'last_suggested_at',
        'is_critical',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'last_suggested_at' => 'datetime',
        'is_critical' => 'boolean',
        'average_daily_usage' => 'decimal:2',
        'usage_rate_per_day' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
