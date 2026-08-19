<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate the AI stock briefing overnight so it's instantly ready (no
// waiting on Gemini) whenever the first user logs in each day.
Schedule::command('ai:generate-daily-briefing')->dailyAt('01:00');
