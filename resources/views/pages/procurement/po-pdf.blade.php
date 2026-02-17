<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Surat Pesanan - {{ $order->po_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            font-size: 10px;
        }

        .title {
            text-align: center;
            text-decoration: underline;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
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

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background: #f5f5f5;
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        .items-table td {
            border: 1px solid #ccc;
            padding: 8px;
            vertical-align: top;
        }

        .totals-table {
            width: 40%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 5px;
            text-align: right;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
        }

        .footer-table {
            width: 100%;
        }

        .footer-table td {
            text-align: center;
            width: 33%;
        }

        .sig-space {
            height: 80px;
        }

        .type-badge {
            position: absolute;
            top: 0;
            right: 0;
            padding: 5px 10px;
            border: 1px solid #000;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>RSUD JAILOLO</h1>
        <p>Jl. Inpres Jailolo, Halmahera Barat, Maluku Utara</p>
        <p>Telp: (0922) 2221062 | Email: rsud.jailolo@gmail.com</p>
    </div>

    <div class="type-badge">
        SP {{ $order->sp_type }}
    </div>

    <div class="title">
        SURAT PESANAN (SP) {{ $order->sp_type }}
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 15%;">Nomor SP</td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;"><strong>{{ $order->po_number }}</strong></td>
            <td style="width: 15%;">Kepada Yth.</td>
            <td style="width: 2%;">:</td>
            <td style="width: 33%;"><strong>{{ $order->supplier->name }}</strong></td>
        </tr>
        <tr>
            <td>Tanggal SP</td>
            <td>:</td>
            <td>{{ $order->po_date->format('d F Y') }}</td>
            <td>Alamat</td>
            <td>:</td>
            <td>{{ $order->supplier->address ?? '-' }}</td>
        </tr>
        <tr>
            <td>Gudang Tujuan</td>
            <td>:</td>
            <td>{{ $order->warehouse->name }}</td>
            <td>Kontak</td>
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
                        <small>{{ $detail->item->generic_name }}</small>
                    </td>
                    <td style="text-align: center;">{{ $detail->item->unit->name }}</td>
                    <td style="text-align: center;">{{ number_format($detail->qty_ordered) }}</td>
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
        <tr style="font-weight: bold; border-top: 1px solid #000;">
            <td>GRAND TOTAL</td>
            <td>:</td>
            <td>Rp{{ number_format($order->grand_total) }}</td>
        </tr>
    </table>

    <div style="margin-top: 20px;">
        <strong>Catatan:</strong><br>
        {{ $order->notes ?: 'Mohon barang dikirim tepat waktu sesuai dengan pesanan.' }}
    </div>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    Direktur RSUD Jailolo
                    <div class="sig-space"></div>
                    <strong>( .................................... )</strong>
                </td>
                <td></td>
                <td>
                    Jailolo, {{ date('d F Y') }}<br>
                    Pejabat Pengadaan / Apoteker
                    <div class="sig-space"></div>
                    <strong>( .................................... )</strong><br>
                    SIPA No: ..........................
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 30px; font-size: 8px; color: #999; text-align: center;">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Inventori Farmasi RSUD Jailolo.
    </div>
</body>

</html>