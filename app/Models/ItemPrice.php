<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ItemPrice extends Model
{
    protected $fillable = [
        'item_id',
        'supplier_id',
        'price_type',
        'price',
        'ppn_percentage',
        'effective_date',
        'end_date',
        'is_active',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
