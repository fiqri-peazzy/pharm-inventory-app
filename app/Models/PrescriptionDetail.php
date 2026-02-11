<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_id',
        'item_id',
        'item_batch_id',
        'qty',
        'price_per_unit',
        'subtotal',
        'instruction',
    ];

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
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
