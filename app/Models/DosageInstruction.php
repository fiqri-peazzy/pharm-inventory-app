<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosageInstruction extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'instruction',
        'frequency',
        'timing',
        'additional_notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTiming($query, $timing)
    {
        return $query->where('timing', $timing);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('code', 'like', "%{$search}%")
              ->orWhere('instruction', 'like', "%{$search}%")
              ->orWhere('frequency', 'like', "%{$search}%");
        });
    }

    // Helper Methods
    public function getTimingNameAttribute(): string
    {
        return match($this->timing) {
            'sebelum_makan' => 'Sebelum Makan',
            'sesudah_makan' => 'Sesudah Makan',
            'bersama_makan' => 'Bersama Makan',
            'bebas' => 'Bebas',
            default => $this->timing,
        };
    }

    public function getFullInstructionAttribute(): string
    {
        $instruction = $this->instruction;
        if ($this->additional_notes) {
            $instruction .= ' (' . $this->additional_notes . ')';
        }
        return $instruction;
    }
}
