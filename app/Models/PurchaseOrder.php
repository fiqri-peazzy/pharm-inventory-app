<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PurchaseOrder extends Model
{
    protected $casts = [
        'po_date' => 'date',
        'expected_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    protected $fillable = [
        'po_number',
        'sp_type',
        'purchase_request_id',
        'supplier_id',
        'warehouse_id',
        'po_date',
        'expected_delivery_date',
        'payment_term',
        'total_amount',
        'ppn_amount',
        'discount_amount',
        'grand_total',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
