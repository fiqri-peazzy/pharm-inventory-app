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
        'disposal_type',
        'method',
        'disposal_method',
        'ba_number',
        'location',
        'disposal_date',
        'execution_date',
        'total_value',
        'notes',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'executed_by',
        'executed_at',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'execution_date' => 'date',
        'approved_at' => 'datetime',
        'executed_at' => 'datetime',
        'posted_at' => 'datetime',
        'total_value' => 'decimal:2',
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

    public function witnesses()
    {
        return $this->hasMany(DisposalWitness::class);
    }

    public function evidences()
    {
        return $this->hasMany(DisposalEvidence::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function executor()
    {
        return $this->belongsTo(User::class, 'executed_by');
    }
}
