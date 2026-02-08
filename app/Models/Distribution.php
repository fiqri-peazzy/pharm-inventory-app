<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'distribution_number',
        'origin_warehouse_id',
        'destination_warehouse_id',
        'status',
        'type',
        'notes',
        'total_items',
        'total_qty',
        'requested_at',
        'approved_at',
        'sent_at',
        'received_at',
        'created_by',
        'approved_by',
        'sent_by',
        'received_by',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function origin()
    {
        return $this->belongsTo(Warehouse::class, 'origin_warehouse_id');
    }

    public function destination()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function details()
    {
        return $this->hasMany(DistributionDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
