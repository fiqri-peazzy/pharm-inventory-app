<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara Penerimaan - {{ $receiving->receiving_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10.5px; color: #1f2937; line-height: 1.5; }

        .doc-title { text-align: center; margin: 4px 0 18px; }
        .doc-title h1 { margin: 0; font-size: 15px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .doc-title .divider { width: 90px; margin: 6px auto; border-bottom: 2px solid #1D4ED8; }
        .doc-title p { margin: 2px 0 0; font-size: 10px; color: #6b7280; }

        .eyebrow { text-transform: uppercase; font-weight: bold; font-size: 9px; color: #6b7280; letter-spacing: 0.5px; }

        .info-table { width: 100%; margin-bottom: 18px; border-collapse: collapse; }
        .info-table td { padding: 3px 0; vertical-align: top; }
        .info-table .label { width: 110px; font-weight: bold; color: #1f2937; }
        .info-table .colon { width: 12px; }

        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
        .content-table th { background: #1D4ED8; color: #fff; border: 1px solid #1D4ED8; padding: 7px 8px; font-weight: bold; text-align: left; text-transform: uppercase; font-size: 9px; letter-spacing: 0.3px; }
        .content-table td { border: 1px solid #e5e7eb; padding: 7px 8px; font-size: 10px; }
        .content-table tbody tr:nth-child(even) td { background: #f9fafb; }
        .content-table tfoot td { border-top: 2px solid #1D4ED8; background: #eff6ff; font-size: 11px; padding: 8px; }

        .mono { font-family: 'Courier New', monospace; }

        .footer-sign { width: 100%; margin-top: 40px; border-collapse: collapse; }
        .footer-sign td { text-align: center; width: 33%; vertical-align: top; padding: 0 6px; }
        .footer-sign .role { margin: 0 0 2px; font-size: 10px; }
        .footer-sign .sub { margin: 0; font-size: 9px; color: #6b7280; font-style: italic; }
        .signature-box { height: 70px; }
        .sig-line { margin: 0; font-size: 10px; }
        .sig-name { margin: 2px 0 0; font-weight: bold; font-size: 10px; }

        .doc-footer { margin-top: 30px; padding-top: 8px; border-top: 1px solid #e5e7eb; font-size: 8px; color: #9ca3af; text-align: center; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
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
        <div class="divider"></div>
        <p>No. Dokumen: {{ $receiving->receiving_number }}</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Supplier</td>
            <td class="colon">:</td>
            <td>{{ $receiving->supplier->name }}</td>
            <td class="label">Tanggal Terima</td>
            <td class="colon">:</td>
            <td>{{ $receiving->receiving_date->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="label">Gudang Tujuan</td>
            <td class="colon">:</td>
            <td>{{ $receiving->warehouse->name }}</td>
            <td class="label">No. Faktur</td>
            <td class="colon">:</td>
            <td>{{ $receiving->invoice_number }}</td>
        </tr>
        <tr>
            <td class="label">Referensi PO</td>
            <td class="colon">:</td>
            <td>{{ $receiving->purchaseOrder ? $receiving->purchaseOrder->po_number : '-' }}</td>
            <td class="label">Tanggal Faktur</td>
            <td class="colon">:</td>
            <td>{{ $receiving->invoice_date->format('d/m/Y') }}</td>
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
                <td class="mono">{{ $detail->batch_number }}</td>
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
        <div class="eyebrow">Keterangan</div>
        <div style="font-style: italic; margin-top: 2px;">{{ $receiving->notes }}</div>
    </div>
    @endif

    <table class="footer-sign">
        <tr>
            <td>
                <p class="role">Diserahkan Oleh,</p>
                <p class="sub">(Supplier / Ekspedisi)</p>
                <div class="signature-box"></div>
                <p class="sig-line">( .................................... )</p>
            </td>
            <td>
                <p class="role">Diterima Oleh,</p>
                <p class="sub">(Gudang Farmasi)</p>
                <div class="signature-box"></div>
                <p class="sig-name">{{ $receiving->creator->name ?? '( .................................... )' }}</p>
            </td>
            <td>
                <p class="role">Mengetahui,</p>
                <p class="sub">(Kepala Instalasi/Gudang)</p>
                <div class="signature-box"></div>
                <p class="sig-name">{{ $receiving->approver->name ?? '( .................................... )' }}</p>
            </td>
        </tr>
    </table>

    <div class="doc-footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh {{ auth()->user()->name }}
    </div>
</body>
</html>
