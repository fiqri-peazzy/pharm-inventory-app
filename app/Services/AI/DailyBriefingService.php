<?php

namespace App\Services\AI;

use App\Models\AiDailyBriefing;
use App\Models\ItemBatch;
use App\Models\ItemWarehouseSetting;
use Carbon\Carbon;

class DailyBriefingService
{
    public function __construct(private GeminiService $gemini)
    {
    }

    /**
     * Generate (or return the already-generated) briefing for today.
     * Idempotent — calling this multiple times the same day does not
     * burn extra Gemini quota once a row exists.
     */
    public function generateForToday(): AiDailyBriefing
    {
        $existing = AiDailyBriefing::forToday();
        if ($existing) {
            return $existing;
        }

        $nearExpired = $this->nearExpiredItems();
        $criticalStock = $this->criticalStockItems();

        $content = $this->buildContent($nearExpired, $criticalStock);

        return AiDailyBriefing::create([
            'briefing_date' => today(),
            'content' => $content['text'],
            'near_expired_count' => $nearExpired->count(),
            'critical_stock_count' => $criticalStock->count(),
            'generated_by_ai' => $content['ai_generated'],
        ]);
    }

    private function nearExpiredItems()
    {
        return ItemBatch::with(['item', 'warehouse'])
            ->where('is_active', true)
            ->where('current_qty', '>', 0)
            ->whereBetween('expired_date', [Carbon::now(), Carbon::now()->addDays(90)])
            ->orderBy('expired_date')
            ->limit(20)
            ->get()
            ->map(fn ($b) => [
                'name' => $b->item->name ?? '-',
                'qty' => $b->current_qty,
                'warehouse' => $b->warehouse->name ?? '-',
                'days_left' => (int) Carbon::now()->diffInDays($b->expired_date, false),
            ]);
    }

    private function criticalStockItems()
    {
        return ItemWarehouseSetting::with(['item', 'warehouse'])
            ->where('min_stock', '>', 0)
            ->whereRaw('(SELECT COALESCE(SUM(current_qty), 0) FROM item_batches WHERE item_batches.item_id = item_warehouse_settings.item_id AND item_batches.warehouse_id = item_warehouse_settings.warehouse_id AND is_active = 1) < min_stock')
            ->limit(20)
            ->get()
            ->map(function ($s) {
                $currentQty = ItemBatch::where('item_id', $s->item_id)
                    ->where('warehouse_id', $s->warehouse_id)
                    ->where('is_active', true)
                    ->sum('current_qty');

                return [
                    'name' => $s->item->name ?? '-',
                    'current_qty' => $currentQty,
                    'min_stock' => $s->min_stock,
                    'warehouse' => $s->warehouse->name ?? '-',
                ];
            });
    }

    private function buildContent($nearExpired, $criticalStock): array
    {
        if ($nearExpired->isEmpty() && $criticalStock->isEmpty()) {
            return [
                'text' => 'Semua stok obat & BMHP dalam kondisi aman hari ini — tidak ada item yang mendekati kadaluarsa dalam 90 hari ke depan, dan tidak ada stok yang berada di bawah batas minimum. Tetap pantau secara berkala.',
                'ai_generated' => false,
            ];
        }

        $prompt = $this->buildPrompt($nearExpired, $criticalStock);
        $aiText = $this->gemini->generate(
            $prompt,
            'Kamu adalah asisten farmasi rumah sakit yang membantu apoteker/petugas gudang memantau stok obat setiap pagi. '
            . 'Tulis ringkasan singkat, jelas, dan actionable dalam Bahasa Indonesia (maksimal 120 kata, format paragraf pendek). '
            . 'Fokus ke urgensi: item apa yang paling perlu ditindaklanjuti duluan. Jangan mengulang seluruh daftar mentah, cukup soroti yang paling kritis dan beri saran singkat. '
            . 'PENTING: jangan gunakan format markdown sama sekali (tanpa **, tanpa *, tanpa #, tanpa heading) — tulis sebagai teks polos biasa saja, karena akan ditampilkan apa adanya tanpa parser markdown. Gunakan tanda "-" di awal baris kalau perlu membuat daftar.'
        );

        if ($aiText) {
            return ['text' => $this->stripMarkdown($aiText), 'ai_generated' => true];
        }

        // Fallback if Gemini is unreachable/quota exhausted: a plain, still
        // useful, non-AI summary so the feature degrades gracefully.
        $parts = [];
        if ($nearExpired->isNotEmpty()) {
            $parts[] = $nearExpired->count() . ' item mendekati kadaluarsa dalam 90 hari ke depan, termasuk "' . $nearExpired->first()['name'] . '" (' . $nearExpired->first()['days_left'] . ' hari lagi).';
        }
        if ($criticalStock->isNotEmpty()) {
            $parts[] = $criticalStock->count() . ' item berada di bawah stok minimum, termasuk "' . $criticalStock->first()['name'] . '" (sisa ' . $criticalStock->first()['current_qty'] . ', minimum ' . $criticalStock->first()['min_stock'] . ').';
        }

        return ['text' => implode(' ', $parts) . ' Silakan cek Dashboard Stok untuk detail lengkap.', 'ai_generated' => false];
    }

    /**
     * Belt-and-suspenders cleanup in case the model still slips in markdown
     * despite being told not to — this text is rendered as plain text, not
     * parsed, so raw ** or * would otherwise show up literally to the user.
     */
    private function stripMarkdown(string $text): string
    {
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text);
        $text = preg_replace('/^#{1,6}\s*/m', '', $text);
        $text = preg_replace('/^\*\s+/m', '- ', $text);

        return trim($text);
    }

    private function buildPrompt($nearExpired, $criticalStock): string
    {
        $lines = ["Data kondisi stok farmasi hari ini (" . today()->translatedFormat('d F Y') . "):", ''];

        if ($nearExpired->isNotEmpty()) {
            $lines[] = 'ITEM MENDEKATI KADALUARSA (90 hari ke depan):';
            foreach ($nearExpired as $i) {
                $lines[] = "- {$i['name']} | sisa {$i['qty']} unit | {$i['warehouse']} | kadaluarsa dalam {$i['days_left']} hari";
            }
            $lines[] = '';
        }

        if ($criticalStock->isNotEmpty()) {
            $lines[] = 'ITEM DI BAWAH STOK MINIMUM:';
            foreach ($criticalStock as $i) {
                $lines[] = "- {$i['name']} | sisa {$i['current_qty']} unit | minimum {$i['min_stock']} | {$i['warehouse']}";
            }
        }

        $lines[] = '';
        $lines[] = 'Buat ringkasan singkat untuk apoteker/petugas gudang yang baru login pagi ini.';

        return implode("\n", $lines);
    }
}
