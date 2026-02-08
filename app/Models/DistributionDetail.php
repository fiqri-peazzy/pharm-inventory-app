<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribution_id',
        'item_id',
        'item_batch_id',
        'qty_requested',
        'qty_sent',
        'qty_received',
        'unit_price',
        'notes',
    ];

    public function distribution()
    {
        return $this->belongsTo(Distribution::class);
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
