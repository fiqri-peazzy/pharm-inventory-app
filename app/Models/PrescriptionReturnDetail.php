<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionReturnDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_return_id',
        'prescription_detail_id',
        'item_id',
        'item_batch_id',
        'qty_returned',
        'price_per_unit',
        'subtotal',
        'condition_notes',
    ];

    protected $casts = [
        'qty_returned' => 'integer',
        'price_per_unit' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relations
    public function prescriptionReturn()
    {
        return $this->belongsTo(PrescriptionReturn::class);
    }

    public function prescriptionDetail()
    {
        return $this->belongsTo(PrescriptionDetail::class);
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
