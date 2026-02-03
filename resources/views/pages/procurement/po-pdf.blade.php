<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order - {{ $order->po_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; line-height: 1.3; color: #333; }
        .header { margin-bottom: 20px; border-bottom: 1px solid #444; padding-bottom: 5px; }
        .header table { width: 100%; }
        .brand { font-size: 20px; font-weight: bold; color: #4f46e5; text-transform: uppercase; }
        .doc-title { font-size: 16px; font-weight: bold; text-align: right; text-transform: uppercase; }
        
        .info-section { width: 100%; margin-bottom: 20px; }
        .info-box { width: 48%; vertical-align: top; }
        .label { font-size: 9px; font-weight: bold; color: #666; text-transform: uppercase; margin-bottom: 2px; }
        .value { font-size: 11px; font-weight: bold; margin-bottom: 8px; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; table-layout: fixed; }
        .items-table th { background-color: #f3f4f6; color: #374151; font-weight: bold; text-transform: uppercase; font-size: 9px; padding: 6px 4px; border: 1px solid #d1d5db; text-align: left; }
        .items-table td { padding: 6px 4px; border: 1px solid #e5e7eb; vertical-align: top; font-size: 10px; word-wrap: break-word; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        
        .totals-section { width: 100%; margin-top: 10px; }
        .totals-table { width: 250px; margin-left: auto; border-collapse: collapse; }
        .totals-table td { padding: 4px 8px; border-bottom: 1px solid #eee; font-size: 10px; }
        .totals-table .grand-total { background-color: #f3f4f6; font-weight: bold; font-size: 12px; border-top: 2px solid #4f46e5; }
        
        .notes-section { margin-top: 20px; font-style: italic; color: #666; font-size: 10px; }
        .signature-section { margin-top: 40px; width: 100%; }
        .signature-box { width: 180px; text-align: center; vertical-align: top; }
        .signature-line { margin-top: 50px; border-top: 1px solid #333; padding-top: 5px; font-weight: bold; font-size: 11px; }
        
        @page { margin: 1.2cm 1cm; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td class="brand">POS PHARMACY</td>
                <td class="doc-title">PURCHASE ORDER</td>
            </tr>
            <tr>
                <td style="font-size: 10px; color: #666;">
                    Jl. Kesehatan No. 123, Jakarta Selatan<br>
                    Telp: (021) 1234567 | Email: info@pospharm.com
                </td>
                <td style="text-align: right; font-weight: bold;">
                    #{{ $order->po_number }}
                </td>
            </tr>
        </table>
    </div>

    <table class="info-section">
        <tr>
            <td class="info-box">
                <div class="label">DIBELI DARI (SUPPLIER):</div>
                <div class="value">
                    {{ $order->supplier->name ?? 'Internal Store' }}<br>
                    <span style="font-weight: normal; font-size: 10px; color: #666;">
                        {{ $order->supplier->address ?? '-' }}<br>
                        PIC: {{ $order->supplier->pic_name ?? '-' }} ({{ $order->supplier->pic_phone ?? '-' }})
                    </span>
                </div>
            </td>
            <td class="info-box" style="padding-left: 4%;">
                <div class="label">KIRIM KE (DESTINATION):</div>
                <div class="value">
                    {{ $order->warehouse->name }}<br>
                    <span style="font-weight: normal; font-size: 10px; color: #666;">
                        {{ $order->warehouse->address ?? 'Alamat Gudang Utama' }}<br>
                        Internal PR: {{ $order->purchaseRequest->request_number ?? '-' }}
                    </span>
                </div>
            </td>
        </tr>
        <tr>
            <td class="info-box">
                <div class="label">TANGGAL PESANAN:</div>
                <div class="value">{{ \Carbon\Carbon::parse($order->po_date)->format('d F Y') }}</div>
            </td>
            <td class="info-box" style="padding-left: 4%;">
                <div class="label">TERM PEMBAYARAN:</div>
                <div class="value">{{ $order->payment_term }} Hari (Net)</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="40%">NAMA ITEM / BARANG</th>
                <th width="8%" class="text-center">QTY</th>
                <th width="15%" class="text-right">HARGA</th>
                <th width="8%" class="text-right">DISC%</th>
                <th width="8%" class="text-right">PPN%</th>
                <th width="16%" class="text-right">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->details as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $detail->item->name }}</strong><br>
                        <span style="font-size: 8px; color: #666;">{{ $detail->item->code }} @if($detail->notes) | {{ $detail->notes }} @endif</span>
                    </td>
                    <td class="text-center">{{ number_format($detail->qty_ordered) }}</td>
                    <td class="text-right">{{ number_format($detail->purchase_price) }}</td>
                    <td class="text-right">{{ (float)$detail->discount_percentage }}%</td>
                    <td class="text-right">{{ (float)$detail->ppn_percentage }}%</td>
                    <td class="text-right">{{ number_format($detail->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-section">
        <tr>
            <td style="vertical-align: top;">
                <div class="notes-section">
                    <strong>Catatan:</strong><br>
                    {{ $order->notes ?: 'Hanya melayani pengiriman barang sesuai spesifikasi di atas. Mohon lampirkan salinan PO ini saat pengiriman barang.' }}
                </div>
            </td>
            <td width="250">
                <table class="totals-table">
                    <tr>
                        <td style="color: #666;">Subtotal (Gross)</td>
                        <td class="text-right">{{ number_format($order->total_amount) }}</td>
                    </tr>
                    <tr>
                        <td style="color: #666;">Total Diskon (-)</td>
                        <td class="text-right" style="color: #ef4444;">{{ number_format($order->discount_amount) }}</td>
                    </tr>
                    <tr>
                        <td style="color: #666;">Total PPN (+)</td>
                        <td class="text-right">{{ number_format($order->ppn_amount) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td>GRAND TOTAL (RP)</td>
                        <td class="text-right" style="font-size: 13px;">{{ number_format($order->grand_total) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="signature-section">
        <tr>
            <td class="signature-box">
                <div class="label">DISETUJUI OLEH,</div>
                <div class="signature-line">MANAGEMENT / KAI</div>
            </td>
            <td style="width: 50%;"></td>
            <td class="signature-box">
                <div class="label">DICETAK OLEH,</div>
                <div class="signature-line">{{ Auth::user()->name }}</div>
                <div style="font-size: 8px; color: #999; margin-top: 3px;">Printed at: {{ now()->format('d/m/Y H:i') }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
