<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PurchaseRequest extends Model
{
    protected $fillable = [
        'request_number',
        'warehouse_id',
        'request_date',
        'period_month',
        'period_year',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseRequestDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
