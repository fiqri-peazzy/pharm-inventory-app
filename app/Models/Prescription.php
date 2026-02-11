<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prescription_number',
        'patient_id',
        'patient_name',
        'medical_record_number',
        'doctor_id',
        'doctor_name',
        'service_unit_id',
        'warehouse_id',
        'total_amount',
        'prescription_date',
        'status',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'processed_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(PrescriptionDetail::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function serviceUnit()
    {
        return $this->belongsTo(ServiceUnit::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
