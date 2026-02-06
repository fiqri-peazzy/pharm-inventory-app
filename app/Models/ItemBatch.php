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
}
