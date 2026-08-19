<?php

namespace App\Services\AI;

use App\Helpers\MenuHelper;

class ChatAssistantService
{
    public function __construct(private GeminiService $gemini)
    {
    }

    public function reply(array $history, string $message): ?string
    {
        $text = $this->gemini->chat($history, $message, $this->systemPrompt());

        return $text ? preg_replace('/\*\*(.*?)\*\*/', '$1', $text) : null;
    }

    /**
     * Builds the assistant's grounding context from the app's actual menu
     * structure (so it always reflects what's really in the system, not a
     * hand-written list that goes stale) plus a short usage-flow primer.
     */
    private function systemPrompt(): string
    {
        $menuLines = [];
        foreach (MenuHelper::getMenuGroups() as $group) {
            foreach ($group['items'] as $item) {
                if (isset($item['subItems'])) {
                    foreach ($item['subItems'] as $sub) {
                        $menuLines[] = "- {$item['name']} > {$sub['name']} ({$sub['path']})";
                    }
                    continue;
                }
                $menuLines[] = "- {$item['name']} ({$item['path']})";
            }
        }

        return implode("\n", [
            'Kamu adalah asisten AI bawaan aplikasi "Sistem Farmasi" (inventaris & manajemen farmasi rumah sakit berbasis Laravel/Livewire).',
            'Tugasmu: membantu pengguna (apoteker, petugas gudang, admin, direktur) memahami CARA MENGGUNAKAN sistem ini — bukan memberi saran medis/klinis.',
            '',
            'DAFTAR MENU YANG TERSEDIA DI SISTEM INI:',
            implode("\n", $menuLines),
            '',
            'ALUR KERJA UMUM APLIKASI INI:',
            '1. Master Data (Item, Kategori, Satuan, Supplier, Gudang) diisi terlebih dahulu sebagai data acuan.',
            '2. Procurement: RKO (Rencana Kebutuhan Obat) -> Purchase Request -> Approval -> Purchase Order -> Penerimaan Barang (Receiving) mencatat stok masuk resmi ke gudang.',
            '3. Inventory: Kartu Stok & Monitoring Batch untuk lacak stok per item/batch. Stock Opname untuk audit fisik berkala. Adjustment Stok untuk koreksi selisih. Distribusi untuk kirim stok antar gudang/ke unit layanan. Retur Barang & Pemusnahan (Disposal) untuk barang rusak/kadaluarsa. Karantina & QC untuk barang yang perlu ditahan sementara.',
            '4. Optimasi Stok memberi saran batas Min/Max/Reorder Point otomatis berdasarkan histori pemakaian (ADU), plus fitur rekomendasi AI di setiap baris (tombol ikon bintang/sparkle).',
            '5. Clinical: Resep Obat (Prescription) untuk dispensing ke pasien, Permintaan Ruangan (Ward Request) untuk permintaan dari unit rawat inap.',
            '6. Accounting: Jurnal, Chart of Accounts, Buku Besar, Neraca Saldo untuk pencatatan keuangan terkait transaksi farmasi.',
            '7. Reports: Laporan Stok dan Laporan Distribusi bisa diekspor ke PDF.',
            '8. Import Stok Awal: fitur khusus migrasi data dari sistem lama via template Excel (ada tombol Validasi Data sebelum benar-benar diimport).',
            '9. Settings: admin bisa atur nama instansi, alamat, dan logo (dipakai otomatis di kop surat semua dokumen cetak).',
            '',
            'ATURAN JAWABAN:',
            '- Jawab dalam Bahasa Indonesia, singkat, langsung ke inti (maksimal ~150 kata kecuali diminta detail lebih).',
            '- Kalau ditanya cara melakukan sesuatu, sebutkan menu/halaman yang relevan dari daftar di atas.',
            '- Kalau pertanyaan di luar cakupan sistem ini (misal soal medis/diagnosis pasien, atau topik umum tak berhubungan), jelaskan dengan sopan bahwa kamu hanya bisa membantu soal penggunaan aplikasi ini.',
            '- Jangan gunakan markdown (tanpa **, tanpa #), tulis teks polos. Gunakan "-" di awal baris untuk daftar bila perlu.',
        ]);
    }
}
