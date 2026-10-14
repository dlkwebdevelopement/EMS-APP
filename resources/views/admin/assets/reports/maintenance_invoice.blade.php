<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance History Statement - {{ date('Y-m-d') }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 20px; }
        .invoice-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0d6efd; padding-bottom: 20px; margin-bottom: 30px; }
        .company-logo { font-size: 24px; font-weight: bold; color: #0d6efd; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #333; margin: 0; }
        .invoice-meta { margin-bottom: 40px; display: flex; justify-content: space-between; }
        .meta-col div { margin-bottom: 5px; font-size: 14px; }
        .label { color: #888; font-weight: bold; width: 120px; display: inline-block; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f8f9fa; color: #555; text-transform: uppercase; font-size: 11px; text-align: left; padding: 12px 15px; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 12px; }
        
        .summary-card { background: #f0f7ff; border: 1px solid #cfe2ff; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .summary-item h4 { margin: 0 0 5px 0; color: #555; font-size: 11px; text-transform: uppercase; }
        .summary-item p { margin: 0; font-size: 18px; font-weight: bold; color: #0d6efd; }
        
        .total-section { float: right; width: 320px; }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .total-row.grand-total { border-top: 2px solid #0d6efd; border-bottom: none; font-size: 20px; font-weight: bold; color: #0d6efd; margin-top: 10px; }
        
        .footer { margin-top: 100px; font-size: 11px; text-align: center; color: #888; border-top: 1px solid #eee; padding-top: 20px; }
        
        .status-badge { font-size: 10px; font-weight: bold; padding: 4px 10px; border-radius: 20px; display: inline-block; }
        .status-completed { background: #d1e7dd; color: #0f5132; }
        .status-repairing { background: #fff3cd; color: #664d03; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .summary-card { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #333; color: white; padding: 15px; text-align: center; border-radius: 8px; margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: bold; font-size: 14px;">🛠️ Service History Preview. Click to print or save the Maintenance Statement.</span>
        <button onclick="window.print()" style="background: #0d6efd; color: white; border: none; padding: 8px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">🖨️ PRINT STATEMENT</button>
    </div>

    <div class="invoice-header">
        <div class="company-logo">DLK TECHNOLOGIES</div>
        <h1 class="invoice-title">MAINTENANCE LOG</h1>
    </div>

    <div class="invoice-meta">
        <div class="meta-col">
            <div><span class="label">STATEMENT DATE:</span> {{ date('d M, Y') }}</div>
            <div><span class="label">REF CODE:</span> LOG-MAINT-{{ date('YmdHi') }}</div>
            <div><span class="label">PERIOD:</span> Compiled on active filters</div>
        </div>
        <div class="meta-col" style="text-align: right;">
            <div style="font-weight: bold; color: #0d6efd;">HARDWARE SERVICE SUMMARY</div>
            <div>Consolidated Repair Expenses Sheet</div>
        </div>
    </div>

    <div class="summary-card">
        <div style="font-weight: bold; margin-bottom: 15px; border-bottom: 1px solid #cfe2ff; padding-bottom: 10px;">MAINTENANCE AGGREGATES</div>
        <div class="summary-grid">
            <div class="summary-item">
                <h4>Service Records</h4>
                <p>{{ count($maintenances) }} Activities</p>
            </div>
            <div class="summary-item">
                <h4>Total Service Cost</h4>
                <p>₹{{ number_format($totalMaintenanceCost, 2) }}</p>
            </div>
            <div class="summary-item">
                <h4>Health Indicator</h4>
                <p>{{ $maintenances->where('status', 'completed')->count() }} Resolved</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>SERVICE DATE</th>
                <th>ASSET / TAG</th>
                <th>ISSUE DESCRIPTION</th>
                <th>TECHNICAL RESOLUTION</th>
                <th>STATUS</th>
                <th style="text-align: right;">COST (INR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($maintenances as $record)
            <tr>
                <td>{{ $record->maintenance_at ? $record->maintenance_at->format('d-m-Y') : 'N/A' }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $record->asset->brand }} {{ $record->asset->model }}</div>
                    <div style="font-size: 11px; color: #666;">{{ $record->asset->asset_tag }}</div>
                </td>
                <td style="max-width: 150px;">{{ $record->issue_description }}</td>
                <td style="max-width: 150px;">{{ $record->resolution ?? 'Pending/Under Observation' }}</td>
                <td>
                    <span class="status-badge status-{{ $record->status }}">
                        {{ strtoupper($record->status) }}
                    </span>
                </td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($record->cost, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>Aggregated Service Units</span>
            <span>{{ count($maintenances) }}</span>
        </div>
        <div class="total-row">
            <span>Pending/In-Progress Cost</span>
            <span>₹{{ number_format($maintenances->where('status', '!=', 'completed')->sum('cost'), 2) }}</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL EXPENDITURE</span>
            <span>₹{{ number_format($totalMaintenanceCost, 2) }}</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <div>This is a formal asset distribution record. Please keep a copy for your records.</div>
        <div>DLK Technologies Asset Management &copy; {{ date('Y') }} | Generated at {{ date('H:i:s d-m-Y') }}</div>
    </div>
</body>
</html>
