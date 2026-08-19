<?php

namespace App\Exports\Sheets;

use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class TemplateGuideSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithDrawings
{
    /** Number of header rows reserved for the official KOP letterhead. */
    private const KOP_ROWS = 5;

    public function title(): string
    {
        return 'PANDUAN';
    }

    public function array(): array
    {
        $setting = Setting::current();

        $rows = [];

        // KOP surat (official letterhead) block — rows 1..KOP_ROWS
        $rows[] = [$setting->hospital_name ?: config('app.name')];
        $rows[] = [$setting->address ?: ''];
        $rows[] = [trim(collect([
            $setting->phone ? 'Telp: ' . $setting->phone : null,
            $setting->email ? 'Email: ' . $setting->email : null,
        ])->filter()->implode('  |  '))];
        $rows[] = [''];
        $rows[] = [''];

        $rows[] = ['PANDUAN PENGISIAN TEMPLATE IMPORT STOK AWAL'];
        $rows[] = [''];
        $rows[] = ['Dokumen ini digunakan untuk mengimpor data saldo awal stok ke sistem inventaris farmasi.'];
        $rows[] = [''];
        $rows[] = ['ATURAN PENGISIAN SHEET "TEMPLATE_STOK":'];
        $rows[] = ['Kolom', 'Keterangan', 'Wajib?'];
        $rows[] = ['item_name',     'Nama lengkap item/obat/BMHP/alkes. Jika belum ada di sistem, otomatis ditambahkan ke master.', 'Wajib'];
        $rows[] = ['category_code', 'Kode kategori dari sheet REF_KATEGORI. Contoh: OK, OB, BMHP, ALKES, NAR, PSI', 'Opsional (default OK)'];
        $rows[] = ['unit_code',     'Kode satuan dari sheet REF_SATUAN. Contoh: TAB, BOX, AMP, VIAL, PCS, BTL', 'Opsional (default PCS)'];
        $rows[] = ['batch_number',  'Nomor batch atau nomor kwitansi/SP dari PBF', 'Opsional (dibuatkan otomatis jika kosong)'];
        $rows[] = ['expired_date',  'Tanggal kadaluarsa format YYYY-MM-DD. Contoh: 2027-04-30', 'Opsional (default +2 tahun jika kosong)'];
        $rows[] = ['supplier_code', 'Kode PBF dari sheet REF_SUPPLIER. Contoh: KF, MUP, LMS', 'Opsional'];
        $rows[] = ['qty_received',  'Jumlah total yang diterima/masuk sesuai catatan stok lama', 'WAJIB — harus lebih dari 0'];
        $rows[] = ['qty_used',      'Jumlah stok yang sudah keluar di periode sebelumnya (sisa = qty_received - qty_used)', 'Opsional (isi 0 jika tidak ada)'];
        $rows[] = ['purchase_price', 'Harga beli per satuan item', 'Opsional (boleh 0)'];
        $rows[] = ['invoice_number', 'Nomor faktur/SP dari PBF. Baris dengan nomor invoice sama = 1 dokumen penerimaan. Jika dikosongkan, seluruh baris tanpa nomor invoice akan digabung otomatis menjadi 1 dokumen "Saldo Awal Tanpa Faktur".', 'Opsional'];
        $rows[] = ['invoice_date',  'Tanggal faktur format YYYY-MM-DD. Jika kosong, memakai tanggal hari ini.', 'Opsional'];
        $rows[] = [''];
        $rows[] = ['CATATAN PENTING:'];
        $rows[] = ['1. Hanya item_name dan qty_received yang WAJIB diisi. Kolom lain akan diisi otomatis dengan nilai default yang wajar jika dikosongkan.'];
        $rows[] = ['2. Satu baris = satu item, satu batch. Item yang sama bisa muncul di beberapa baris (beda batch/supplier)'];
        $rows[] = ['3. Baris dengan qty_received kosong atau 0 akan DIABAIKAN oleh sistem dan dilaporkan di ringkasan hasil import'];
        $rows[] = ['4. Nama item yang SAMA dengan di master data akan di-link otomatis (tidak duplikat)'];
        $rows[] = ['5. Nama item yang BARU akan ditambahkan ke master data secara otomatis'];
        $rows[] = ['6. Baris dengan kombinasi item + nomor batch yang SUDAH PERNAH diimport sebelumnya akan otomatis dilewati agar stok tidak tercatat dobel'];
        $rows[] = ['7. Sebelum data benar-benar disimpan, gunakan tombol "Validasi Data" di aplikasi untuk melihat pratinjau baris mana yang valid/dilewati beserta alasannya'];
        $rows[] = ['8. Setelah proses import selesai, laporan hasil (baris berhasil/dilewati) dapat diunduh dari aplikasi'];

        return $rows;
    }

    public function columnWidths(): array
    {
        return ['A' => 20, 'B' => 75, 'C' => 30];
    }

    public function drawings()
    {
        $setting = Setting::current();

        if (!$setting->logo_path) {
            return [];
        }

        $path = public_path($setting->logo_path);
        if (!is_file($path)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setPath($path);
        $drawing->setHeight(70);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);

        return [$drawing];
    }

    public function styles(Worksheet $sheet)
    {
        $kopLastRow = self::KOP_ROWS;

        // KOP surat block
        $sheet->mergeCells('A1:C1');
        $sheet->mergeCells('A2:C2');
        $sheet->mergeCells('A3:C3');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['size' => 10],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("A{$kopLastRow}:C{$kopLastRow}")->applyFromArray([
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1D4ED8']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        // Title
        $sheet->mergeCells('A6:C6');
        $sheet->getStyle('A6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A11:C11')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
        ]);
        $sheet->getStyle('A10')->applyFromArray(['font' => ['bold' => true]]);
        $sheet->getStyle('A24')->applyFromArray(['font' => ['bold' => true]]);
        return [];
    }
}
