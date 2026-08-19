<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Buku Stok</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #1f2937;
            line-height: 1.5;
            margin: 24px 28px;
        }

        .doc-title {
            text-align: center;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1D4ED8;
        }

        .doc-title h2 {
            margin: 0 0 6px 0;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1f2937;
        }

        .doc-title p {
            margin: 2px 0;
            font-size: 10px;
            color: #6b7280;
        }

        .doc-title p strong {
            color: #1f2937;
        }

        .analysis-box {
            background: #f9fafb;
            padding: 10px 12px;
            margin: 15px 0;
            border: 1px solid #e5e7eb;
        }

        .analysis-box h3 {
            margin: 0 0 10px 0;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1D4ED8;
        }

        .analysis-grid {
            display: table;
            width: 100%;
        }

        .analysis-item {
            display: table-cell;
            width: 25%;
            padding: 5px;
        }

        .analysis-item strong {
            display: block;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #6b7280;
            margin-bottom: 3px;
            font-weight: normal;
        }

        .analysis-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }

        .recommendations {
            margin: 15px 0;
        }

        .recommendations h3 {
            margin: 10px 0;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1D4ED8;
        }

        .recommendation-item {
            padding: 8px 10px;
            margin: 5px 0;
            border-left: 3px solid #1D4ED8;
            background: #eff6ff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 7px 8px;
            text-align: left;
        }

        th {
            background-color: #1D4ED8;
            color: #ffffff;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        td {
            font-size: 10px;
        }

        tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        .mono {
            font-family: Courier, monospace;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-green {
            color: #22c55e;
            font-weight: bold;
        }

        .text-red {
            color: #ef4444;
            font-weight: bold;
        }

        .footer {
            margin-top: 26px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            font-size: 8px;
            color: #9ca3af;
            text-align: center;
        }

        .footer p {
            margin: 2px 0;
        }
    </style>
</head>

<body>
    @include('pdf.partials.kop-header')

    <div class="doc-title">
        <h2>LAPORAN BUKU STOK</h2>
        <p><strong>{{ $data['item']->name }}</strong> ({{ $data['item']->code }})</p>
        <p>Gudang: {{ $data['warehouse']->name }}</p>
        <p>Periode: {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} -
            {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}
        </p>
        <p>Dicetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Analysis Summary -->
    <div class="analysis-box">
        <h3>Analisis Stok</h3>
        <div class="analysis-grid">
            <div class="analysis-item">
                <strong>ABC Class</strong>
                <div class="value">{{ $data['abc_class']['class'] }}</div>
                <div style="font-size: 9px; color: #6b7280;">{{ $data['abc_class']['description'] }}</div>
            </div>
            <div class="analysis-item">
                <strong>Movement Pattern</strong>
                <div class="value">{{ $data['movement_pattern']['pattern'] }}</div>
                <div style="font-size: 9px; color: #6b7280;">ADU: {{ $data['movement_pattern']['adu'] }}/day</div>
            </div>
            <div class="analysis-item">
                <strong>Health Score</strong>
                <div class="value">{{ $data['health_score']['score'] }}%</div>
                <div style="font-size: 9px; color: #6b7280;">{{ $data['health_score']['status'] }}</div>
            </div>
            <div class="analysis-item">
                <strong>Current Stock</strong>
                <div class="value">{{ number_format($data['current_stock']) }}</div>
                <div style="font-size: 9px; color: #6b7280;">{{ $data['item']->unit->name }}</div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    @if(count($data['recommendations']) > 0)
        <div class="recommendations">
            <h3>Rekomendasi</h3>
            @foreach($data['recommendations'] as $rec)
                <div class="recommendation-item">
                    <strong>{{ $rec['message'] }}</strong><br>
                    <span style="font-size: 9px; color: #6b7280;">→ {{ $rec['action'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Stock Card Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 15%;">Transaksi</th>
                <th style="width: 12%;">Batch</th>
                <th class="text-right" style="width: 10%;">Masuk</th>
                <th class="text-right" style="width: 10%;">Keluar</th>
                <th class="text-right" style="width: 10%;">Saldo</th>
                <th style="width: 31%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['stockCards'] as $card)
                <tr>
                    <td class="mono">{{ $card->transaction_date->format('d/m/Y H:i') }}</td>
                    <td>{{ str_replace('_', ' ', $card->transaction_type) }}</td>
                    <td class="mono">{{ $card->batch?->batch_number ?? '-' }}</td>
                    <td class="text-right {{ $card->qty_in > 0 ? 'text-green' : '' }}">
                        {{ $card->qty_in > 0 ? number_format($card->qty_in) : '-' }}
                    </td>
                    <td class="text-right {{ $card->qty_out > 0 ? 'text-red' : '' }}">
                        {{ $card->qty_out > 0 ? number_format($card->qty_out) : '-' }}
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        {{ number_format($card->last_stock) }}
                    </td>
                    <td>{{ $card->notes }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada transaksi dalam periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini digenerate otomatis oleh sistem {{ \App\Models\Setting::current()->app_name }}</p>
    </div>
</body>

</html>
