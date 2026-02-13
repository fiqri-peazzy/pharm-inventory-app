<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'item_id',
        'item_batch_id',
        'qty',
        'price',
        'total_value',
        'notes',
    ];

    public function return()
    {
        return $this->belongsTo(InventoryReturn::class, 'return_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function batch()
    {
        return $this->belongsTo(ItemBatch::class, 'item_batch_id');
    }
}
