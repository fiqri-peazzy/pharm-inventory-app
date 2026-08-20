<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;

class ReportInsightService
{
    public function __construct(private GeminiService $gemini)
    {
    }

    /**
     * Turn the structured stock analysis (ABC class, health score, movement
     * pattern, recommendations already computed by StockAnalysisService)
     * into a short plain-language narrative, so the report reads at a
     * glance instead of requiring the viewer to interpret raw numbers.
     */
    public function narrateStockAnalysis(array $data): ?string
    {
        $item = $data['item'];
        $cacheKey = 'ai_report_insight_' . md5($item->id . '_' . json_encode($data['health_score']) . '_' . json_encode($data['movement_pattern'] ?? null));

        return Cache::remember($cacheKey, now()->addDay(), function () use ($data, $item) {
            $lines = [
                "Item: {$item->name} ({$item->code})",
                'Kelas ABC: ' . ($data['abc_class']['class'] ?? '-'),
                'Skor kesehatan stok: ' . ($data['health_score']['score'] ?? '-') . ' (' . ($data['health_score']['status'] ?? '-') . ')',
                'Stok saat ini: ' . ($data['current_stock'] ?? 0),
            ];

            if (!empty($data['movement_pattern'])) {
                $lines[] = 'Pola pergerakan: ' . json_encode($data['movement_pattern']);
            }

            if (!empty($data['recommendations'])) {
                $recs = collect($data['recommendations'])->pluck('message')->filter()->implode('; ');
                if ($recs) {
                    $lines[] = 'Rekomendasi sistem: ' . $recs;
                }
            }

            $lines[] = '';
            $lines[] = 'Buat satu paragraf ringkasan naratif singkat (maksimal 60 kata) dari data analisis stok ini untuk pembaca laporan, bahasa Indonesia, teks polos tanpa markdown.';

            $text = $this->gemini->generate(implode("\n", $lines), 'Kamu analis inventaris farmasi yang merangkum data analitik jadi narasi singkat mudah dipahami.');

            return $text ? trim(preg_replace('/\*\*(.*?)\*\*/', '$1', $text)) : null;
        });
    }
}
