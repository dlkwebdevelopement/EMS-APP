<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Consolidated Invoice - {{ date('Y-m-d') }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 20px; }
        .invoice-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #27a355; padding-bottom: 20px; margin-bottom: 30px; }
        .company-logo { font-size: 24px; font-weight: bold; color: #27a355; text-transform: uppercase; letter-spacing: 2px; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #333; margin: 0; }
        .invoice-meta { margin-bottom: 40px; display: flex; justify-content: space-between; }
        .meta-col div { margin-bottom: 5px; font-size: 14px; }
        .label { color: #888; font-weight: bold; width: 120px; display: inline-block; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f8f9fa; color: #555; text-transform: uppercase; font-size: 12px; text-align: left; padding: 12px 15px; border-bottom: 2px solid #dee2e6; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; font-size: 13px; }
        
        .summary-card { background: #f1fcf4; border: 1px solid #d4edda; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .summary-item h4 { margin: 0 0 5px 0; color: #555; font-size: 12px; text-transform: uppercase; }
        .summary-item p { margin: 0; font-size: 18px; font-weight: bold; color: #27a355; }
        
        .total-section { float: right; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
        .total-row.grand-total { border-top: 2px solid #27a355; border-bottom: none; font-size: 20px; font-weight: bold; color: #27a355; margin-top: 10px; }
        
        .footer { margin-top: 100px; font-size: 12px; text-align: center; color: #888; border-top: 1px solid #eee; padding-top: 20px; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .summary-card { break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="background: #333; color: white; padding: 15px; text-align: center; border-radius: 8px; margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: bold; font-size: 14px;">📄 This is a professional print preview. Click the button to print or save as PDF.</span>
        <button onclick="window.print()" style="background: #27a355; color: white; border: none; padding: 8px 25px; border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 14px;">🖨️ PRINT / SAVE AS PDF</button>
    </div>

    <div class="invoice-header">
        <div class="company-logo">DLK TECHNOLOGIES</div>
        <h1 class="invoice-title">ASSET STATEMENT</h1>
    </div>

    <div class="invoice-meta">
        <div class="meta-col">
            <div><span class="label">DATE:</span> {{ date('d M, Y') }}</div>
            <div><span class="label">REF CODE:</span> INV-ASSET-{{ date('YmdHi') }}</div>
            <div><span class="label">PREPARED BY:</span> {{ auth()->guard('admin')->user()->name }}</div>
        </div>
        <div class="meta-col" style="text-align: right;">
            <div style="font-weight: bold; color: #27a355;">INVENTORY STATUS REPORT</div>
            <div>Generated based on active filters</div>
        </div>
    </div>

    <div class="summary-card">
        <div style="font-weight: bold; margin-bottom: 15px; border-bottom: 1px solid #d4edda; padding-bottom: 10px;">INVENTORY SUMMARY</div>
        <div class="summary-grid">
            <div class="summary-item">
                <h4>Total Items</h4>
                <p>{{ count($assets) }} Units</p>
            </div>
            <div class="summary-item">
                <h4>Categories</h4>
                <p>{{ count($categorySummary) }} Active</p>
            </div>
            <div class="summary-item">
                <h4>Total Valuation</h4>
                <p>₹{{ number_format($totalCost, 2) }}</p>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ASSET TAG</th>
                <th>DESCRIPTION</th>
                <th>CATEGORY</th>
                <th>STATUS</th>
                <th>SERIAL NUMBER</th>
                <th style="text-align: right;">COST (INR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $asset)
            <tr>
                <td style="font-weight: bold;">{{ $asset->asset_tag }}</td>
                <td>{{ $asset->brand }} {{ $asset->model }}</td>
                <td>{{ $asset->category->name ?? 'N/A' }}</td>
                <td>{{ strtoupper($asset->status) }}</td>
                <td style="color: #666; font-size: 12px;">{{ $asset->serial_number }}</td>
                <td style="text-align: right; font-weight: bold;">{{ number_format($asset->purchase_cost, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; border-top: 1px solid #efefef; padding-top: 20px;">
        <div style="font-weight: bold; margin-bottom: 15px;">CATEGORY BREAKDOWN</div>
        <table style="width: 50%; margin-bottom: 0;">
            <thead>
                <tr>
                    <th>Category</th>
                    <th style="text-align: center;">Count</th>
                    <th style="text-align: right;">Sub-Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categorySummary as $name => $stats)
                <tr style="font-size: 12px;">
                    <td>{{ $name }}</td>
                    <td style="text-align: center;">{{ $stats['count'] }}</td>
                    <td style="text-align: right;">₹{{ number_format($stats['total_cost'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total-section">
        <div class="total-row">
            <span>Subtotal Items</span>
            <span>{{ count($assets) }}</span>
        </div>
        <div class="total-row grand-total">
            <span>TOTAL VALUE</span>
            <span>₹{{ number_format($totalCost, 2) }}</span>
        </div>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <div>This is a computer-generated statement and does not require an official signature.</div>
        <div style="margin-top: 5px;">Property of DLK Technologies &copy; {{ date('Y') }}</div>
    </div>
</body>
</html>
