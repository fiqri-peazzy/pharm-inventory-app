<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Distribusi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }

        .summary-box {
            background: #f5f5f5;
            padding: 10px;
            margin: 15px 0;
            border: 1px solid #ddd;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 5px;
        }

        .summary-item strong {
            display: block;
            font-size: 10px;
            color: #666;
        }

        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
        }

        .metrics {
            margin: 15px 0;
        }

        .metric-item {
            display: inline-block;
            width: 32%;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            margin-right: 1%;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #e0e0e0;
            font-weight: bold;
            font-size: 10px;
        }

        td {
            font-size: 10px;
        }

        .text-center {
            text-align: center;
        }

        .insights {
            margin: 15px 0;
            padding: 10px;
            background: #fff5f5;
            border-left: 3px solid #ff6b6b;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN DISTRIBUSI</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} -
            {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}</p>
        <p>Dicetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <h3 style="margin: 0 0 10px 0;">📊 Ringkasan</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <strong>Total Distribusi</strong>
                <div class="value">{{ number_format($data['analysis']['summary']['total_distributions']) }}</div>
            </div>
            <div class="summary-item">
                <strong>Total Item</strong>
                <div class="value">{{ number_format($data['analysis']['summary']['total_items_moved']) }}</div>
            </div>
            <div class="summary-item">
                <strong>Total Nilai</strong>
                <div class="value">Rp {{ number_format($data['analysis']['summary']['total_value'], 0, ',', '.') }}
                </div>
            </div>
            <div class="summary-item">
                <strong>Completed</strong>
                <div class="value">{{ $data['analysis']['summary']['by_status']['received'] }}</div>
            </div>
        </div>
    </div>

    <!-- Metrics -->
    <div class="metrics">
        <h3 style="margin: 10px 0;">📈 Efficiency Metrics</h3>
        <div class="metric-item">
            <strong>Avg Lead Time</strong>
            <div style="font-size: 18px; font-weight: bold; color: #3b82f6;">
                {{ $data['analysis']['efficiency_metrics']['avg_lead_time'] }} days
            </div>
        </div>
        <div class="metric-item">
            <strong>Fill Rate</strong>
            <div style="font-size: 18px; font-weight: bold; color: #22c55e;">
                {{ $data['analysis']['efficiency_metrics']['fill_rate'] }}%
            </div>
        </div>
        <div class="metric-item">
            <strong>On-Time Rate</strong>
            <div style="font-size: 18px; font-weight: bold; color: #a855f7;">
                {{ $data['analysis']['efficiency_metrics']['on_time_rate'] }}%
            </div>
        </div>
    </div>

    <!-- Insights -->
    @if(count($data['analysis']['recommendations']) > 0)
        <div class="insights">
            <h3 style="margin: 0 0 10px 0;">💡 Insights:</h3>
            @foreach($data['analysis']['recommendations'] as $insight)
                <p style="margin: 5px 0;">• {{ $insight['message'] }}</p>
            @endforeach
        </div>
    @endif

    <!-- Distribution Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">No. Distribusi</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 25%;">Asal</th>
                <th style="width: 25%;">Tujuan</th>
                <th class="text-center" style="width: 8%;">Items</th>
                <th class="text-center" style="width: 10%;">Qty</th>
                <th class="text-center" style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['distributions'] as $dist)
                <tr>
                    <td>{{ $dist->distribution_number }}</td>
                    <td>{{ $dist->created_at->format('d/m/Y') }}</td>
                    <td>{{ $dist->origin->name }}</td>
                    <td>{{ $dist->destination->name }}</td>
                    <td class="text-center">{{ $dist->details->count() }}</td>
                    <td class="text-center">{{ number_format($dist->details->sum('qty_sent')) }}</td>
                    <td class="text-center">{{ ucfirst($dist->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 9px; color: #666;">
        <p>Laporan ini digenerate otomatis oleh sistem Medivault Pharmacy Inventory</p>
    </div>
</body>

</html>