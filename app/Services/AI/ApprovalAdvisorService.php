<?php

namespace App\Services\AI;

use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Cache;

class ApprovalAdvisorService
{
    public function __construct(private GeminiService $gemini)
    {
    }

    /**
     * Give an approver (Kepala Farmasi/Direktur) a quick AI read on one PR:
     * flags any line where the requested quantity looks disproportionate to
     * that item's own recorded average daily usage — so anomalies get a
     * second look before rubber-stamping. Cached per PR since the request
     * doesn't change once submitted.
     */
    public function analyze(PurchaseRequest $pr): array
    {
        $cacheKey = "ai_pr_analysis_{$pr->id}";

        return Cache::remember($cacheKey, now()->addDays(3), function () use ($pr) {
            $lines = [];
            $anomalies = [];

            foreach ($pr->details as $detail) {
                $itemName = $detail->item->name ?? 'Item';
                $qty = $detail->requested_qty ?? 0;
                $adu = $detail->average_usage ?? 0;
                $currentStock = $detail->current_stock ?? 0;

                $flag = '';
                // Requesting far more than ~30 days of average usage on top
                // of what's already in stock is worth a second look.
                if ($adu > 0 && $qty > ($adu * 30 + $currentStock) * 1.5) {
                    $flag = ' [JAUH DI ATAS KEBUTUHAN NORMAL]';
                    $anomalies[] = $itemName;
                }

                $lines[] = "- {$itemName}: stok saat ini {$currentStock}, rata-rata pakai/hari {$adu}, diminta {$qty}{$flag}";
            }

            if (empty($lines)) {
                return ['text' => null, 'anomalies' => [], 'ai_generated' => false];
            }

            $prompt = implode("\n", [
                "Purchase Request {$pr->request_number} dari gudang " . ($pr->warehouse->name ?? '-') . ":",
                implode("\n", $lines),
                '',
                'Beri rekomendasi singkat untuk yang akan menyetujui (approve/reject) PR ini, fokus ke apakah ada kejanggalan jumlah permintaan dibanding stok & pemakaian normal yang perlu dicek ulang sebelum disetujui. Maksimal 50 kata, teks polos tanpa markdown.',
            ]);

            $text = $this->gemini->generate($prompt, 'Kamu asisten AI yang membantu Kepala Farmasi/Direktur RS meninjau permintaan pembelian obat secara cepat dan hati-hati.');

            return [
                'text' => $text ? preg_replace('/\*\*(.*?)\*\*/', '$1', trim($text)) : null,
                'anomalies' => $anomalies,
                'ai_generated' => (bool) $text,
            ];
        });
    }
}
