<?php

namespace App\Livewire\AI;

use App\Models\AiDailyBriefing;
use App\Services\AI\DailyBriefingService;
use Livewire\Component;

class DailyBriefingModal extends Component
{
    public bool $show = false;
    public ?string $content = null;
    public int $nearExpiredCount = 0;
    public int $criticalStockCount = 0;
    public bool $aiGenerated = true;

    public function mount(DailyBriefingService $service)
    {
        if (!auth()->check() || session('ai_briefing_dismissed_' . today()->toDateString())) {
            return;
        }

        $briefing = AiDailyBriefing::forToday();

        // Fallback for the very first day this feature runs (or if the
        // scheduled command hasn't executed yet) — generate it on demand,
        // once, the first time someone hits this without a cached row.
        if (!$briefing) {
            $briefing = $service->generateForToday();
        }

        $this->content = $briefing->content;
        $this->nearExpiredCount = $briefing->near_expired_count;
        $this->criticalStockCount = $briefing->critical_stock_count;
        $this->aiGenerated = $briefing->generated_by_ai;
        $this->show = true;
    }

    public function dismiss()
    {
        session(['ai_briefing_dismissed_' . today()->toDateString() => true]);
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.ai.daily-briefing-modal');
    }
}
