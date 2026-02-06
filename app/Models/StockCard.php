<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCard extends Model
{
    protected $fillable = [
        'item_id',
        'warehouse_id',
        'item_batch_id',
        'transaction_date',
        'reference_type',
        'reference_id',
        'qty_in',
        'qty_out',
        'last_stock',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function batch()
    {
        return $this->belongsTo(ItemBatch::class, 'item_batch_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
