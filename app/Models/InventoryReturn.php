<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'returns';

    protected $fillable = [
        'return_number',
        'type',
        'from_warehouse_id',
        'to_warehouse_id',
        'supplier_id',
        'return_date',
        'reason_category',
        'reason', // detailed reason
        'receiving_number',
        'po_number',
        'invoice_number',
        'supplier_do_number',
        'evidence_file',
        'total_value',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'return_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function details()
    {
        return $this->hasMany(ReturnDetail::class, 'return_id');
    }

    public function creditNotes()
    {
        return $this->hasMany(ReturnCreditNote::class, 'return_id');
    }
}
