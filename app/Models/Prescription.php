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
        'payer_type',
        'patient_type',
        'room_bed_number',
        'total_amount',
        'prescription_date',
        'status',
        'payment_status',
        'is_returnable',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'processed_at' => 'datetime',
        'is_returnable' => 'boolean',
    ];

    // Relations
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

    public function returns()
    {
        return $this->hasMany(PrescriptionReturn::class);
    }

    // Accessor & Helper Methods
    public function isInpatient(): bool
    {
        return $this->patient_type === 'ri';
    }

    public function isOutpatient(): bool
    {
        return $this->patient_type === 'rj';
    }

    public function isBpjs(): bool
    {
        return $this->payer_type === 'bpjs';
    }

    public function isUmum(): bool
    {
        return $this->payer_type === 'umum';
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function canBeReturned(): bool
    {
        return $this->is_returnable 
            && $this->isInpatient() 
            && $this->status === 'completed';
    }

    public function getPayerTypeNameAttribute(): string
    {
        return match($this->payer_type) {
            'umum' => 'Umum',
            'bpjs' => 'BPJS',
            'asuransi_lain' => 'Asuransi Lain',
            default => $this->payer_type,
        };
    }

    public function getPatientTypeNameAttribute(): string
    {
        return match($this->patient_type) {
            'rj' => 'Rawat Jalan',
            'ri' => 'Rawat Inap',
            default => $this->patient_type,
        };
    }

    public function getPaymentStatusNameAttribute(): string
    {
        return match($this->payment_status) {
            'unpaid' => 'Belum Lunas',
            'partial' => 'Dibayar Sebagian',
            'paid' => 'Lunas',
            default => $this->payment_status,
        };
    }
}
