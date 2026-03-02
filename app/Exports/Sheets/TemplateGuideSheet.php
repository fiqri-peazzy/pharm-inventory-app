<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TemplateGuideSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function title(): string
    {
        return 'PANDUAN';
    }

    public function array(): array
    {
        return [
            ['PANDUAN PENGISIAN TEMPLATE IMPORT STOK AWAL'],
            [''],
            ['Dokumen ini digunakan untuk mengimpor data saldo awal stok ke sistem inventaris farmasi.'],
            [''],
            ['ATURAN PENGISIAN SHEET "TEMPLATE_STOK":'],
            ['Kolom', 'Keterangan', 'Wajib?'],
            ['item_name',     'Nama lengkap item/obat/BMHP/alkes. Jika belum ada di sistem, otomatis ditambahkan ke master.', 'Wajib'],
            ['category_code', 'Kode kategori dari sheet REF_KATEGORI. Contoh: OK, OB, BMHP, ALKES, NAR, PSI', 'Wajib'],
            ['unit_code',     'Kode satuan dari sheet REF_SATUAN. Contoh: TAB, BOX, AMP, VIAL, PCS, BTL', 'Wajib'],
            ['batch_number',  'Nomor batch atau nomor kwitansi/SP dari PBF', 'Wajib'],
            ['expired_date',  'Tanggal kadaluarsa format YYYY-MM-DD. Contoh: 2027-04-30', 'Wajib'],
            ['supplier_code', 'Kode PBF dari sheet REF_SUPPLIER. Contoh: KF, MUP, LMS', 'Wajib'],
            ['qty_received',  'Jumlah total yang diterima/masuk sesuai faktur awal', 'Wajib'],
            ['qty_used',      'Jumlah stok yang sudah keluar di periode sebelumnya (sisa = qty_received - qty_used)', 'Opsional (isi 0 jika tidak ada)'],
            ['purchase_price', 'Harga beli per satuan item', 'Opsional (boleh 0)'],
            ['invoice_number', 'Nomor faktur/SP dari PBF. Baris dengan nomor invoice sama = 1 dokumen penerimaan', 'Wajib'],
            ['invoice_date',  'Tanggal faktur format YYYY-MM-DD', 'Wajib'],
            [''],
            ['CATATAN PENTING:'],
            ['1. Satu baris = satu item, satu batch. Item yang sama bisa muncul di beberapa baris (beda batch/supplier)'],
            ['2. Baris dengan qty_received = 0 akan DIABAIKAN oleh sistem'],
            ['3. Nama item yang SAMA dengan di master data akan di-link otomatis (tidak duplikat)'],
            ['4. Nama item yang BARU akan ditambahkan ke master data secara otomatis'],
            ['5. Kelompokkan per invoice_number: 1 invoice_number = 1 dokumen Penerimaan Barang di sistem'],
            ['6. qty_used akan dicatat sebagai stok keluar (penyesuaian) terpisah'],
            ['7. Saldo stok akhir di sistem = qty_received - qty_used'],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 75, 'C' => 30];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:C1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A6:C6')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
        ]);
        $sheet->getStyle('A5')->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle('A19')->applyFromArray(['font' => ['bold' => true]]);
        return [];
    }
}
