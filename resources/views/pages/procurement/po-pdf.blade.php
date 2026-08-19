<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Pesanan - {{ $order->po_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10.5px;
            line-height: 1.5;
            color: #1f2937;
        }

        .title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            margin: 4px 0 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .title-divider {
            width: 90px;
            margin: 6px auto 18px;
            border-bottom: 2px solid #1D4ED8;
        }

        .eyebrow {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 9px;
            color: #6b7280;
            letter-spacing: 0.5px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .info-table .label {
            font-weight: bold;
            color: #1f2937;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #1D4ED8;
            color: #fff;
            border: 1px solid #1D4ED8;
            padding: 7px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .items-table td {
            border: 1px solid #e5e7eb;
            padding: 7px 8px;
            vertical-align: top;
            font-size: 10px;
        }

        .items-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        .totals-table {
            width: 45%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 5px 6px;
            text-align: right;
        }

        .totals-table .grand-total td {
            border-top: 2px solid #1D4ED8;
            background: #eff6ff;
            font-size: 11px;
            padding: 8px 6px;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            text-align: center;
            width: 33%;
            vertical-align: top;
            padding: 0 6px;
            font-size: 10px;
        }

        .sig-space {
            height: 70px;
        }

        .doc-footer {
            margin-top: 30px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
        }

        .type-badge {
            position: absolute;
            top: 0;
            right: 0;
            padding: 4px 10px;
            border: 1px solid #1D4ED8;
            color: #1D4ED8;
            font-weight: bold;
            font-size: 9px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    @include('pdf.partials.kop-header')

    <div class="type-badge">
        SP {{ $order->sp_type }}
    </div>

    <div class="title">
        SURAT PESANAN (SP) {{ $order->sp_type }}
    </div>
    <div class="title-divider"></div>

    <table class="info-table">
        <tr>
            <td class="label" style="width: 15%;">Nomor SP</td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;"><strong>{{ $order->po_number }}</strong></td>
            <td class="label" style="width: 15%;">Kepada Yth.</td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;"><strong>{{ $order->supplier->name }}</strong></td>
        </tr>
        <tr>
            <td class="label">Tanggal SP</td>
            <td>:</td>
            <td>{{ $order->po_date->format('d F Y') }}</td>
            <td class="label">Alamat</td>
            <td>:</td>
            <td>{{ $order->supplier->address ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Gudang Tujuan</td>
            <td>:</td>
            <td>{{ $order->warehouse->name }}</td>
            <td class="label">Kontak</td>
            <td>:</td>
            <td>{{ $order->supplier->phone ?? '-' }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="40%">Nama Barang / Obat</th>
                <th width="10%">Satuan</th>
                <th width="10%">Jumlah</th>
                <th width="15%">Harga Satuan</th>
                <th width="20%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->details as $index => $detail)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $detail->item->name }}</strong><br>
                        <small style="color: #6b7280;">{{ $detail->item->generic_name }}</small>
                    </td>
                    <td style="text-align: center;">{{ $detail->item->unit->name }}</td>
                    <td style="text-align: right;">{{ number_format($detail->qty_ordered) }}</td>
                    <td style="text-align: right;">{{ number_format($detail->purchase_price) }}</td>
                    <td style="text-align: right;">{{ number_format($detail->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td>Subtotal (Gross)</td>
            <td width="5%">:</td>
            <td width="35%">Rp{{ number_format($order->total_amount) }}</td>
        </tr>
        @if($order->total_discount > 0)
            <tr>
                <td>Total Diskon</td>
                <td>:</td>
                <td>(Rp{{ number_format($order->total_discount) }})</td>
            </tr>
        @endif
        <tr>
            <td>PPN (11%)</td>
            <td>:</td>
            <td>Rp{{ number_format($order->ppn_amount) }}</td>
        </tr>
        <tr class="grand-total" style="font-weight: bold;">
            <td>GRAND TOTAL</td>
            <td>:</td>
            <td>Rp{{ number_format($order->grand_total) }}</td>
        </tr>
    </table>

    <div style="margin-top: 20px; margin-bottom: 10px;">
        <div class="eyebrow">Catatan</div>
        <div style="margin-top: 2px;">{{ $order->notes ?: 'Mohon barang dikirim tepat waktu sesuai dengan pesanan.' }}</div>
    </div>

    @php $setting = \App\Models\Setting::current(); @endphp
    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <span style="font-style: italic; color: #6b7280; font-size: 9px;">Direktur {{ $setting->hospital_name ?: $setting->app_name }}</span>
                    <div class="sig-space"></div>
                    <strong>( .................................... )</strong>
                </td>
                <td></td>
                <td>
                    {{ date('d F Y') }}<br>
                    <span style="font-style: italic; color: #6b7280; font-size: 9px;">Pejabat Pengadaan / Apoteker</span>
                    <div class="sig-space"></div>
                    <strong>( .................................... )</strong><br>
                    <span style="font-size: 9px;">SIPA No: ..........................</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="doc-footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Inventori Farmasi.
    </div>
</body>

</html>