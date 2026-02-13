<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class JournalEntry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'journal_number',
        'journal_date',
        'type',
        'transaction_type',
        'transaction_id',
        'reference',
        'description',
        'status',
        'total_debit',
        'total_credit',
        'created_by',
        'approved_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'journal_date' => 'date',
        'posted_at' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(JournalEntryDetail::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function transaction()
    {
        // Polymorphic-like relationship using transaction_type and transaction_id
        // since we don't have a single source table.
        return null; 
    }
}
