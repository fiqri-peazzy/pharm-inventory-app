<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'disposal_id',
        'item_id',
        'item_batch_id',
        'qty',
        'reason',
    ];

    public function disposal()
    {
        return $this->belongsTo(Disposal::class);
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
