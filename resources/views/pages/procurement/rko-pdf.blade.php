<!DOCTYPE html>
<html>

<head>
    <title>Rencana Kebutuhan Obat (RKO)</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            margin-top: 0;
        }

        .header h2 {
            margin: 0;
            padding: 0;
        }

        .footer {
            margin-top: 30px;
        }

        .footer-table {
            border: none;
        }

        .footer-table td {
            border: none;
            text-align: center;
            width: 33%;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bg-red {
            background-color: #ffebee;
        }

        .bg-amber {
            background-color: #fff8e1;
        }
    </style>
</head>

<body>
    @include('pdf.partials.kop-header')

    <div class="header">
        <h2>RENCANA KEBUTUHAN OBAT (RKO)</h2>
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
                    <td>{{ $row['code'] }}</td>
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
                <td>Mengetahui,<br>Kepala Instalasi Farmasi</td>
                <td></td>
                <td>Mengesahkan,<br>Direktur RSUD</td>
            </tr>
            <tr style="height: 60px;">
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>(...........................................)</td>
                <td></td>
                <td>(...........................................)</td>
            </tr>
        </table>
    </div>
</body>

</html>