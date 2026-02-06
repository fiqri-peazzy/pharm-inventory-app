<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Disposal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'disposal_number',
        'warehouse_id',
        'type',
        'disposal_date',
        'notes',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'posted_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(DisposalDetail::class);
    }
}
