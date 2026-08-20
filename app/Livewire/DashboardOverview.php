<?php

namespace App\Livewire;

use App\Models\Distribution;
use App\Models\Item;
use App\Models\ItemBatch;
use App\Models\Prescription;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequest;
use App\Models\Receiving;
use App\Services\AI\GeminiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class DashboardOverview extends Component
{
    public array $metrics = [];
    public array $trend = [];
    public array $activities = [];
    public ?string $aiInsight = null;
    public bool $aiGenerated = false;

    public function mount()
    {
        $this->loadMetrics();
        $this->loadTrend();
        $this->loadActivities();
        $this->loadAiInsight();
    }

    private function loadMetrics(): void
    {
        $totalValue = ItemBatch::where('is_active', true)
            ->where('current_qty', '>', 0)
            ->selectRaw('SUM(current_qty * purchase_price) as total')
            ->value('total');

        $this->metrics = [
            'total_items' => Item::where('is_active', true)->count(),
            'total_stock_value' => $totalValue ?? 0,
            'prescriptions_today' => Prescription::whereDate('prescription_date', today())->count(),
            'pending_approvals' => PurchaseRequest::where('status', 'submitted')->count(),
        ];
    }

    private function loadTrend(): void
    {
        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $prescriptionCounts = Prescription::whereBetween('prescription_date', [Carbon::today()->subDays(6), Carbon::today()])
            ->selectRaw('DATE(prescription_date) as d, COUNT(*) as c')
            ->groupBy('d')->pluck('c', 'd');

        $distributionCounts = Distribution::whereBetween('created_at', [Carbon::today()->subDays(6), Carbon::today()->endOfDay()])
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')->pluck('c', 'd');

        $this->trend = [
            'categories' => $days->map(fn ($d) => $d->translatedFormat('d M'))->all(),
            'prescriptions' => $days->map(fn ($d) => (int) ($prescriptionCounts[$d->toDateString()] ?? 0))->all(),
            'distributions' => $days->map(fn ($d) => (int) ($distributionCounts[$d->toDateString()] ?? 0))->all(),
        ];
    }

    private function loadActivities(): void
    {
        $activities = collect();

        Receiving::with('supplier')->latest()->limit(4)->get()->each(function ($r) use ($activities) {
            $activities->push([
                'title' => 'Penerimaan barang dari ' . ($r->supplier->name ?? '-'),
                'subtitle' => $r->receiving_number,
                'time' => $r->created_at,
                'color' => 'emerald',
            ]);
        });

        Distribution::with('destination')->latest()->limit(4)->get()->each(function ($d) use ($activities) {
            $activities->push([
                'title' => 'Distribusi ke ' . ($d->destination->name ?? '-'),
                'subtitle' => $d->distribution_number,
                'time' => $d->created_at,
                'color' => 'blue',
            ]);
        });

        PurchaseOrder::latest()->limit(3)->get()->each(function ($po) use ($activities) {
            $activities->push([
                'title' => 'Surat Pesanan diterbitkan',
                'subtitle' => $po->po_number,
                'time' => $po->created_at,
                'color' => 'indigo',
            ]);
        });

        $this->activities = $activities->sortByDesc('time')->take(6)->values()->all();
    }

    private function loadAiInsight(): void
    {
        $cacheKey = 'ai_dashboard_overview_' . today()->toDateString();

        $result = Cache::remember($cacheKey, now()->endOfDay(), function () {
            $gemini = app(GeminiService::class);

            $prompt = implode("\n", [
                "Data operasional farmasi RS hari ini (" . today()->translatedFormat('d F Y') . "):",
                "- Total item aktif: {$this->metrics['total_items']}",
                "- Nilai stok saat ini: Rp " . number_format($this->metrics['total_stock_value'], 0, ',', '.'),
                "- Resep dilayani hari ini: {$this->metrics['prescriptions_today']}",
                "- Permintaan pembelian menunggu persetujuan: {$this->metrics['pending_approvals']}",
                "- Tren resep 7 hari terakhir: " . implode(', ', $this->trend['prescriptions']),
                "- Tren distribusi 7 hari terakhir: " . implode(', ', $this->trend['distributions']),
                '',
                'Buat satu kalimat ringkasan operasional yang membantu manajemen memahami kondisi hari ini sekilas, singkat dan actionable (maksimal 40 kata, teks polos tanpa markdown).',
            ]);

            $text = $gemini->generate($prompt, 'Kamu analis operasional farmasi RS, jawab singkat dan langsung ke inti dalam Bahasa Indonesia.');

            if (!$text) {
                return ['text' => null, 'ai' => false];
            }

            return ['text' => preg_replace('/\*\*(.*?)\*\*/', '$1', trim($text)), 'ai' => true];
        });

        $this->aiInsight = $result['text'];
        $this->aiGenerated = $result['ai'];
    }

    public function render()
    {
        return view('livewire.dashboard-overview');
    }
}
