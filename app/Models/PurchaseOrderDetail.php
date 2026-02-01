<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PurchaseOrderDetail extends Model
{
    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'qty_ordered',
        'qty_received',
        'purchase_price',
        'discount_percentage',
        'discount_amount',
        'ppn_percentage',
        'ppn_amount',
        'subtotal',
        'notes',
    ];

    public function order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
