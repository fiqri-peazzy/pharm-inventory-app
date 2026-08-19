<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiDailyBriefing extends Model
{
    protected $fillable = [
        'briefing_date',
        'content',
        'near_expired_count',
        'critical_stock_count',
        'generated_by_ai',
    ];

    protected $casts = [
        'briefing_date' => 'date',
        'generated_by_ai' => 'boolean',
    ];

    public static function forToday(): ?self
    {
        return static::query()->whereDate('briefing_date', today())->first();
    }
}
