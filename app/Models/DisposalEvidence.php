<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposalEvidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'disposal_id',
        'file_path',
        'type',
        'notes',
    ];

    public function disposal()
    {
        return $this->belongsTo(Disposal::class);
    }
}
