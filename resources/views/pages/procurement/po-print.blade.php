@extends('layouts.app')

@section('title', 'Cetak Surat Pesanan (PO) - POS Pharm')

@section('content')
    <div class="p-8 bg-white min-h-[1000px] max-w-4xl mx-auto shadow-lg border">
        <!-- Header Dokumen -->
        <div class="flex justify-between items-start border-b-2 border-black pb-4 mb-6">
            <div class="flex gap-4 items-center">
                <div class="w-20 h-20 bg-gray-200 flex items-center justify-center font-bold text-xs text-center border">LOGO RSUD</div>
                <div>
                    <h1 class="text-xl font-black uppercase">RSUD Kabupaten XYZ</h1>
                    <p class="text-[10px] leading-tight font-medium">Jl. Raya Farmasi No. 123, Kota Sehat, Indonesia<br>Telp: (021) 1234567 | Fax: (021) 7654321<br>Email: farmasi@rsud-xyz.go.id</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-lg font-black underline uppercase">Surat Pesanan (PO)</h2>
                <p class="font-mono text-sm">Nomor: {{ $order->po_number }}</p>
            </div>
        </div>

        <!-- Info Supplier & Pengiriman -->
        <div class="grid grid-cols-2 gap-8 mb-8 text-sm">
            <div class="border p-4 rounded-lg relative">
                <span class="absolute -top-2 left-3 bg-white px-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Supplier</span>
                <p class="font-black text-indigo-900 uppercase">{{ $order->supplier->name }}</p>
                <p class="text-xs mt-1">{{ $order->supplier->address }}</p>
                <p class="text-xs">Telp: {{ $order->supplier->phone }}</p>
            </div>
            <div class="border p-4 rounded-lg relative">
                <span class="absolute -top-2 left-3 bg-white px-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest">Alamat Pengiriman</span>
                <p class="font-black uppercase">{{ $order->warehouse->name }}</p>
                <p class="text-xs mt-1">RSUD Kabupaten XYZ - Instalasi Farmasi</p>
                <p class="text-xs">Tgl Pesanan: {{ date('d/m/Y', strtotime($order->po_date)) }}</p>
            </div>
        </div>

        <!-- Table Items -->
        <table class="w-full text-xs border-collapse border border-black mb-6">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-black px-2 py-2 w-8">No</th>
                    <th class="border border-black px-2 py-2 text-left">Nama Barang / Deskripsi</th>
                    <th class="border border-black px-2 py-2 w-16 text-center">Satuan</th>
                    <th class="border border-black px-2 py-2 w-16 text-center">Qty</th>
                    <th class="border border-black px-2 py-2 w-28 text-right">Harga Satuan</th>
                    <th class="border border-black px-2 py-2 w-16 text-center">PPN%</th>
                    <th class="border border-black px-2 py-2 w-32 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->details as $index => $detail)
                    <tr>
                        <td class="border border-black px-2 py-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-black px-2 py-2 font-bold uppercase">{{ $detail->item->name }}</td>
                        <td class="border border-black px-2 py-2 text-center">{{ $detail->item->unit->name ?? 'Pcs' }}</td>
                        <td class="border border-black px-2 py-2 text-center font-bold">{{ number_format($detail->qty_ordered) }}</td>
                        <td class="border border-black px-2 py-2 text-right">{{ number_format($detail->purchase_price, 0, ',', '.') }}</td>
                        <td class="border border-black px-2 py-2 text-center">{{ $detail->ppn_percentage }}%</td>
                        <td class="border border-black px-2 py-2 text-right font-bold">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="border border-black px-2 py-2 text-right font-bold uppercase bg-gray-50 font-mono">Total Sebelum PPN</td>
                    <td class="border border-black px-2 py-2 text-right font-bold bg-gray-50">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="6" class="border border-black px-2 py-2 text-right font-bold uppercase bg-gray-50 font-mono">PPN Keseluruhan</td>
                    <td class="border border-black px-2 py-2 text-right font-bold bg-gray-50">Rp {{ number_format($order->ppn_amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="text-sm">
                    <td colspan="6" class="border border-black px-2 py-2 text-right font-black uppercase bg-indigo-50 font-mono">Total Pesanan (Nett)</td>
                    <td class="border border-black px-2 py-2 text-right font-black bg-indigo-50">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Notes & Tanda Tangan -->
        <div class="grid grid-cols-2 gap-8 mt-12 text-sm">
            <div>
                <p class="font-bold underline uppercase mb-2">Catatan:</p>
                <div class="border p-4 min-h-[100px] text-xs italic">
                    {{ $order->notes ?? 'Pemesanan barang ini sesuai dengan E-Catalog / Kontrak yang berlaku. Mohon segera melakukan konfirmasi pengiriman.' }}
                </div>
            </div>
            <div class="text-center">
                <p class="mb-20">Kota Sehat, {{ date('d F Y') }}<br><strong>Pejabat Pembuat Komitmen (PPK)</strong></p>
                <p class="font-black underline uppercase">dr. HEBAT SEKALI, Sp.An</p>
                <p class="text-xs italic">NIP: 19800101 200501 1 001</p>
            </div>
        </div>

        <div class="mt-20 pt-8 border-t text-[10px] text-gray-400 text-center italic">
            Dokumen ini dihasilkan secara otomatis oleh Sistem POS Manajemen Farmasi.
        </div>
    </div>

    <!-- Print Control -->
    <div class="fixed bottom-6 right-6 no-print">
        <button onclick="window.print()" class="bg-black text-white px-6 py-3 rounded-full font-bold shadow-2xl flex items-center gap-2 hover:scale-105 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            CETAK DOKUMEN (PDF)
        </button>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .shadow-lg { shadow: none !important; border: none !important; }
            .max-w-4xl { max-width: 100% !important; }
            .p-8 { padding: 0 !important; }
        }
    </style>
@endsection
