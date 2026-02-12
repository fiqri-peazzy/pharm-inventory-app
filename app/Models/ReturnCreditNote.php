<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnCreditNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_id',
        'credit_note_number',
        'amount',
        'type',
        'note_date',
    ];

    protected $casts = [
        'note_date' => 'date',
    ];

    public function return()
    {
        return $this->belongsTo(InventoryReturn::class, 'return_id');
    }
}
