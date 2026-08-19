<?php

namespace App\Services\AI;

use App\Models\Item;
use Illuminate\Support\Facades\Cache;

class RestockAdvisorService
{
    public function __construct(private GeminiService $gemini)
    {
    }

    /**
     * Ask Gemini for a plain-language restock recommendation for one item,
     * given the numbers the statistical formula (StockSuggestionService)
     * already computed. Cached per item+warehouse+day so re-opening the
     * page or asking again the same day doesn't burn extra quota.
     */
    public function advise(Item $item, int $warehouseId, array $stats): array
    {
        $cacheKey = "ai_restock_advice_{$item->id}_{$warehouseId}_" . today()->toDateString();

        return Cache::remember($cacheKey, now()->endOfDay(), function () use ($item, $stats) {
            $prompt = $this->buildPrompt($item, $stats);

            $text = $this->gemini->generate(
                $prompt,
                'Kamu adalah analis inventaris farmasi rumah sakit. Diberi data pemakaian dan stok satu item obat/BMHP, '
                . 'berikan rekomendasi restock yang singkat, spesifik, dan actionable dalam Bahasa Indonesia (maksimal 70 kata, teks polos tanpa markdown/bullet). '
                . 'Sebutkan: apakah perlu restock sekarang atau tidak, perkiraan urgensi (mendesak/segera/tidak mendesak), dan alasan singkat berdasarkan angka yang diberikan. '
                . 'Jangan mengarang data yang tidak diberikan.'
            );

            if (!$text) {
                return [
                    'text' => 'AI sedang tidak tersedia. Gunakan saran sistem berbasis formula ADU di atas sebagai acuan sementara.',
                    'ai_generated' => false,
                ];
            }

            $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text);

            return ['text' => trim($text), 'ai_generated' => true];
        });
    }

    private function buildPrompt(Item $item, array $stats): string
    {
        return implode("\n", [
            "Item: {$item->name} ({$item->code})",
            "Kategori: " . ($item->category->name ?? '-'),
            "Stok saat ini: {$stats['current_stock']} " . ($item->unit->name ?? 'unit'),
            "Rata-rata pemakaian per hari (ADU): {$stats['adu']}",
            "Batas minimum saat ini: {$stats['current_min']}",
            "Reorder point saran sistem: {$stats['suggested_rp']}",
            "Stok maksimum saran sistem: {$stats['suggested_max']}",
            '',
            'Berikan rekomendasi restock untuk item ini.',
        ]);
    }
}
