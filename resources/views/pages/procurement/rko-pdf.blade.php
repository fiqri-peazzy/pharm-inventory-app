<!DOCTYPE html>
<html>

<head>
    <title>Rencana Kebutuhan Obat (RKO)</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10.5px;
            color: #1f2937;
            line-height: 1.5;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 7px 8px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #1D4ED8;
            color: #fff;
            font-weight: bold;
            font-size: 9px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        .header {
            text-align: center;
            margin-bottom: 4px;
            margin-top: 4px;
        }

        .header h2 {
            margin: 0;
            padding: 0;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .header .title-divider {
            width: 90px;
            margin: 6px auto;
            border-bottom: 2px solid #1D4ED8;
        }

        .header p {
            margin: 0;
            font-size: 10px;
            color: #6b7280;
        }

        .footer {
            margin-top: 40px;
        }

        .footer-table {
            border: none;
        }

        .footer-table td {
            border: none;
            text-align: center;
            width: 33%;
            font-size: 10px;
        }

        .footer-table .role {
            font-weight: bold;
        }

        .doc-footer {
            margin-top: 30px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
        }

        .mono {
            font-family: 'Courier New', monospace;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bg-red td {
            background-color: #fef2f2 !important;
        }

        .bg-amber td {
            background-color: #fffbeb !important;
        }
    </style>
</head>

<body>
    @include('pdf.partials.kop-header')

    <div class="header">
        <h2>RENCANA KEBUTUHAN OBAT (RKO)</h2>
        <div class="title-divider"></div>
        <p>Unit/Gudang: {{ $warehouse }} | Proyeksi: {{ $days }} Hari | Per Tanggal: {{ $date }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Item</th>
                <th>VEN</th>
                <th>ABC</th>
                <th class="text-right">Stok Akhir</th>
                <th class="text-right">Avg Usage</th>
                <th class="text-right">Saran Order</th>
                <th>Urgensi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $row)
                <tr
                    class="{{ $row['urgency_level'] === 'OUT_OF_STOCK' ? 'bg-red' : ($row['urgency_level'] === 'CRITICAL' ? 'bg-amber' : '') }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="mono">{{ $row['code'] }}</td>
                    <td>{{ $row['item_name'] }}</td>
                    <td class="text-center">{{ $row['ven'] }}</td>
                    <td class="text-center">{{ $row['abc'] }}</td>
                    <td class="text-right">{{ number_format($row['total_stock'] ?? $row['current_stock']) }}</td>
                    <td class="text-right">{{ number_format($row['total_avg_usage'] ?? $row['avg_usage'], 2) }}</td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($row['suggested_qty']) }}</td>
                    <td class="text-center">{{ $row['urgency_level'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td class="role">Mengetahui,<br><span style="font-weight: normal; font-style: italic; color: #6b7280; font-size: 9px;">Kepala Instalasi Farmasi</span></td>
                <td></td>
                <td class="role">Mengesahkan,<br><span style="font-weight: normal; font-style: italic; color: #6b7280; font-size: 9px;">Direktur RSUD</span></td>
            </tr>
            <tr style="height: 70px;">
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><strong>( .................................... )</strong></td>
                <td></td>
                <td><strong>( .................................... )</strong></td>
            </tr>
        </table>
    </div>

    <div class="doc-footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Inventori Farmasi.
    </div>
</body>

</html>