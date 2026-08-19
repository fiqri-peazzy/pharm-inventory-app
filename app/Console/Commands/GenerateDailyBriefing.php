<?php

namespace App\Console\Commands;

use App\Services\AI\DailyBriefingService;
use Illuminate\Console\Command;

class GenerateDailyBriefing extends Command
{
    protected $signature = 'ai:generate-daily-briefing';

    protected $description = 'Generate today\'s AI stock briefing (near-expired items & critical stock) shown to users on login';

    public function handle(DailyBriefingService $service)
    {
        $briefing = $service->generateForToday();

        $this->info('Briefing untuk ' . $briefing->briefing_date->format('d/m/Y') . ' siap.');
        $this->line($briefing->content);

        return self::SUCCESS;
    }
}
