<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Distribusi</title>
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

        .summary-box {
            background: #f9fafb;
            padding: 10px 12px;
            margin: 15px 0;
            border: 1px solid #e5e7eb;
        }

        .summary-box h3 {
            margin: 0 0 10px 0;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1D4ED8;
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
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #6b7280;
            font-weight: normal;
            margin-bottom: 3px;
        }

        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
        }

        .metrics {
            margin: 15px 0;
        }

        .metrics h3 {
            margin: 10px 0;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1D4ED8;
        }

        .metric-item {
            display: inline-block;
            width: 32%;
            padding: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            margin-right: 1%;
            text-align: center;
        }

        .metric-item strong {
            display: block;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #6b7280;
            font-weight: normal;
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

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .insights {
            margin: 15px 0;
            padding: 10px 12px;
            background: #eff6ff;
            border-left: 3px solid #1D4ED8;
        }

        .insights h3 {
            margin: 0 0 10px 0;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1D4ED8;
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
        <h2>LAPORAN DISTRIBUSI</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($filters['date_from'])->format('d/m/Y') }} -
            {{ \Carbon\Carbon::parse($filters['date_to'])->format('d/m/Y') }}
        </p>
        <p>Dicetak: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <h3>Ringkasan</h3>
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
        <h3>Efficiency Metrics</h3>
        <div class="metric-item">
            <strong>Avg Lead Time</strong>
            <div style="font-size: 18px; font-weight: bold; color: #1D4ED8;">
                {{ $data['analysis']['efficiency_metrics']['avg_lead_time'] }} days
            </div>
        </div>
        <div class="metric-item">
            <strong>Fill Rate</strong>
            <div style="font-size: 18px; font-weight: bold; color: #1D4ED8;">
                {{ $data['analysis']['efficiency_metrics']['fill_rate'] }}%
            </div>
        </div>
        <div class="metric-item">
            <strong>On-Time Rate</strong>
            <div style="font-size: 18px; font-weight: bold; color: #1D4ED8;">
                {{ $data['analysis']['efficiency_metrics']['on_time_rate'] }}%
            </div>
        </div>
    </div>

    <!-- Insights -->
    @if(count($data['analysis']['recommendations']) > 0)
        <div class="insights">
            <h3>Insights</h3>
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
                <th class="text-right" style="width: 10%;">Qty</th>
                <th class="text-center" style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data['distributions'] as $dist)
                <tr>
                    <td class="mono">{{ $dist->distribution_number }}</td>
                    <td class="mono">{{ $dist->created_at->format('d/m/Y') }}</td>
                    <td>{{ $dist->origin->name }}</td>
                    <td>{{ $dist->destination->name }}</td>
                    <td class="text-center">{{ $dist->details->count() }}</td>
                    <td class="text-right">{{ number_format($dist->details->sum('qty_sent')) }}</td>
                    <td class="text-center">{{ ucfirst($dist->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini digenerate otomatis oleh sistem {{ \App\Models\Setting::current()->app_name }}</p>
    </div>
</body>

</html>
