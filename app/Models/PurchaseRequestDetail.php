<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PurchaseRequestDetail extends Model
{
    protected $fillable = [
        'purchase_request_id',
        'item_id',
        'current_stock',
        'average_usage',
        'requested_qty',
        'approved_qty',
        'notes',
    ];

    public function request()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
