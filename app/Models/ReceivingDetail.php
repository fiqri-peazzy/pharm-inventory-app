<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceivingDetail extends Model
{
    protected $fillable = [
        'receiving_id',
        'item_id',
        'batch_number',
        'expired_date',
        'qty_received',
        'purchase_price',
        'ppn_percentage',
        'ppn_amount',
        'subtotal',
    ];

    protected $casts = [
        'expired_date' => 'date',
        'purchase_price' => 'decimal:2',
        'ppn_percentage' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function receiving()
    {
        return $this->belongsTo(Receiving::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
