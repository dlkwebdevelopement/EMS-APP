<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hardware Components Statement - {{ date('Y-m-d') }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 20px; }
        .invoice-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ffc107; padding-bottom: 20px; margin-bottom: 30px; }
        .company-logo { font-size: 24px; font-weight: bold; color: #ffc107; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #333; margin: 0; }
        .invoice-meta { margin-bottom: 40px; display: flex; justify-content: space-between; }
        .meta-col div { margin-bottom: 5px; font-size: 14px; }
        .label { color: #888; font-weight: bold; width: 140px; display: inline-block; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #fffcf0; color: #664d03; text-transform: uppercase; font-size: 11px; text-align: left; padding: 12px 15px; border-bottom: 2px solid #ffecb3; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 12px; }
        
        .summary-card { background: #fffdf5; border: 1px solid #ffecb3; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .summary-item h4 { margin: 0 0 5px 0; color: #856404; font-size: 11px; text-transform: uppercase; }
        .summary-item p { margin: 0; font-size: 18px; font-weight: bold; color: #ffc107; }
        
        .footer { margin-top: 100px; font-size: 11px; text-align: center; color: #888; border-top: 1px solid #eee; padding-top: 20px; }
        
        .status-badge { font-size: 10px; font-weight: bold; padding: 4px 10px; border-radius: 20px; display: inline-block; }
        .status-active { background: #d1e7dd; color: #0f5132; }
        .status-deplaced { background: #f8d7da; color: #842029; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #333; color: white; padding: 15px; text-align: center; border-radius: 8px; margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: bold; font-size: 14px;">⚡ Hardware Components Statement. Track internal parts and upgrades.</span>
        <button onclick="window.print()" style="background: #ffc107; color: #333; border: none; padding: 8px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">🖨️ PRINT REPORT</button>
    </div>

    <div class="invoice-header">
        <div class="company-logo">DLK TECHNOLOGIES</div>
        <h1 class="invoice-title">COMPONENTS LOG</h1>
    </div>

    <div class="invoice-meta">
        <div class="meta-col">
            <div><span class="label">STATEMENT DATE:</span> {{ date('d M, Y') }}</div>
            <div><span class="label">REF CODE:</span> LOG-COMP-{{ date('YmdHi') }}</div>
            <div><span class="label">PERIOD:</span> Internal Parts Inventory</div>
        </div>
        <div class="meta-col" style="text-align: right;">
            <div style="font-weight: bold; color: #ffc107;">HARDWARE ATTACHMENTS STATEMENT</div>
            <div>Internal Upgrades & Component Tracking</div>
        </div>
    </div>

    <div class="summary-card">
        <div style="font-weight: bold; margin-bottom: 15px; border-bottom: 1px solid #ffecb3; padding-bottom: 10px; color: #856404;">LOG SUMMARY</div>
        <div class="summary-grid">
            <div class="summary-item">
                <h4>Total Parts Recorded</h4>
                <p>{{ count($components) }} Units</p>
            </div>
            <div class="summary-item">
                <h4>Currently Active</h4>
                <p>{{ $components->where('is_active', true)->count() }} Components</p>
            </div>
            <div class="summary-item">
                <h4>Replaced/Historical</h4>
                <p>{{ $components->where('is_active', false)->count() }} Parts</p>
            </div>
            <div class="summary-item" style="border-left: 2px solid #ffecb3; padding-left: 20px;">
                <h4>Total Components Value</h4>
                <p>₹{{ number_format($components->sum('purchase_cost'), 2) }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>PART NAME</th>
                <th>MODEL / BRAND</th>
                <th>SERIAL NUMBER</th>
                <th>ATTACHED TO (ASSET)</th>
                <th>COST (₹)</th>
                <th>INSTALLED DATE</th>
                <th style="text-align: center;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($components as $component)
            <tr>
                <td style="font-weight: bold;">{{ $component->name }}</td>
                <td>{{ $component->model ?? 'N/A' }}</td>
                <td style="color: #666; font-size: 11px;">{{ $component->serial_number }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $component->asset->brand }} {{ $component->asset->model }}</div>
                    <div style="font-size: 10px; color: #888;">TAG: {{ $component->asset->asset_tag }}</div>
                </td>
                <td style="font-weight: bold;">{{ $component->purchase_cost ? '₹' . number_format($component->purchase_cost, 2) : '-' }}</td>
                <td>{{ $component->purchase_date ? $component->purchase_date->format('d-m-Y') : 'N/A' }}</td>
                <td style="text-align: center;">
                    <span class="status-badge status-{{ $component->is_active ? 'active' : 'deplaced' }}">
                        {{ $component->is_active ? 'ACTIVE' : 'REPLACED' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div>This is an internal statement for hardware component tracking and asset maintenance optimization.</div>
        <div style="margin-top: 5px;">Property of DLK Technologies &copy; {{ date('Y') }}</div>
    </div>
</body>
</html>
