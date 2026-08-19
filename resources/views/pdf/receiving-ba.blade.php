<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Penerimaan - {{ $receiving->receiving_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; line-height: 1.4; }
        .doc-title { text-align: center; margin: 4px 0 20px; }
        .doc-title h1 { margin: 0; font-size: 15px; text-transform: uppercase; letter-spacing: 0.5px; }
        .doc-title p { margin: 2px 0; font-size: 10.5px; color: #555; }

        .info-table { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-table .label { width: 120px; font-weight: bold; }
        
        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .content-table th { background: #f5f5f5; border: 1px solid #ddd; padding: 8px; font-weight: bold; text-align: left; text-transform: uppercase; font-size: 10px; }
        .content-table td { border: 1px solid #ddd; padding: 8px; font-size: 11px; }
        
        .footer { width: 100%; margin-top: 50px; }
        .footer td { text-align: center; width: 33%; }
        .signature-box { height: 80px; }
        
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #fdf6e3; padding: 10px; border: 1px solid #eee; margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 20px; background: #2d3436; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">CETAK BERITA ACARA</button>
    </div>

    @include('pdf.partials.kop-header')

    <div class="doc-title">
        <h1>Berita Acara Penerimaan Barang</h1>
        <p>No. Dokumen: {{ $receiving->receiving_number }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Supplier</td>
            <td>: {{ $receiving->supplier->name }}</td>
            <td class="label">Tanggal Terima</td>
            <td>: {{ $receiving->receiving_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Gudang Tujuan</td>
            <td>: {{ $receiving->warehouse->name }}</td>
            <td class="label">No. Faktur</td>
            <td>: {{ $receiving->invoice_number }}</td>
        </tr>
        <tr>
            <td class="label">Referensi PO</td>
            <td>: {{ $receiving->purchaseOrder ? $receiving->purchaseOrder->po_number : '-' }}</td>
            <td class="label">Tanggal Faktur</td>
            <td>: {{ $receiving->invoice_date->format('d/m/Y') }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Barang</th>
                <th style="width: 80px;">Batch</th>
                <th style="width: 80px;">ED</th>
                <th style="width: 60px; text-align: center;">Qty</th>
                <th style="text-align: right;">Harga Satuan</th>
                <th style="text-align: right;">Total (Inc. PPN)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receiving->details as $index => $detail)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <div class="font-bold">{{ $detail->item->name }}</div>
                    <div style="font-size: 9px; color: #666;">{{ $detail->item->code }}</div>
                </td>
                <td style="font-family: monospace;">{{ $detail->batch_number }}</td>
                <td>{{ $detail->expired_date->format('d/m/Y') }}</td>
                <td style="text-align: center;">{{ number_format($detail->qty_received) }}</td>
                <td class="text-right">Rp{{ number_format($detail->purchase_price) }}</td>
                <td class="text-right">Rp{{ number_format($detail->subtotal) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right font-bold">Grand Total</td>
                <td class="text-right font-bold">Rp{{ number_format($receiving->grand_total) }}</td>
            </tr>
        </tfoot>
    </table>

    @if($receiving->notes)
    <div style="margin-bottom: 20px;">
        <div class="font-bold">Keterangan:</div>
        <div style="font-style: italic;">{{ $receiving->notes }}</div>
    </div>
    @endif

    <table class="footer">
        <tr>
            <td>
                <p>Diserahkan Oleh,</p>
                <p style="font-size: 10px; color: #666;">(Supplier / Ekspedisi)</p>
                <div class="signature-box"></div>
                <p>____________________</p>
            </td>
            <td>
                <p>Diterima Oleh,</p>
                <p style="font-size: 10px; color: #666;">(Gudang Farmasi)</p>
                <div class="signature-box"></div>
                <p class="font-bold">{{ $receiving->creator->name ?? '____________________' }}</p>
            </td>
            <td>
                <p>Mengetahui,</p>
                <p style="font-size: 10px; color: #666;">(Kepala Instalasi/Gudang)</p>
                <div class="signature-box"></div>
                <p class="font-bold">{{ $receiving->approver->name ?? '____________________' }}</p>
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; font-size: 9px; color: #999; text-align: center;">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name }}
    </div>
</body>
</html>
