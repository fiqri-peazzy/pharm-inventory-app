<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrescriptionReturn extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'return_number',
        'prescription_id',
        'warehouse_id',
        'reason',
        'notes',
        'total_return_value',
        'returned_by',
        'returned_at',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'returned_at' => 'datetime',
        'approved_at' => 'datetime',
        'total_return_value' => 'decimal:2',
    ];

    // Relations
    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function details()
    {
        return $this->hasMany(PrescriptionReturnDetail::class);
    }

    // Helper Methods
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getReasonNameAttribute(): string
    {
        return match($this->reason) {
            'pasien_pulang' => 'Pasien Pulang',
            'pasien_meninggal' => 'Pasien Meninggal',
            'obat_berlebih' => 'Obat Berlebih',
            'salah_dispensing' => 'Salah Dispensing',
            'lainnya' => 'Lainnya',
            default => $this->reason,
        };
    }

    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }
}
