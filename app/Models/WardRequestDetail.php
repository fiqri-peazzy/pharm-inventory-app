<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WardRequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'ward_request_id',
        'item_id',
        'qty_requested',
        'qty_fulfilled',
        'notes',
    ];

    public function wardRequest()
    {
        return $this->belongsTo(WardRequest::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
