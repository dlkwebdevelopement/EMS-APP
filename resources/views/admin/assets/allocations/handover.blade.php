<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Handover Receipt - {{ $allocation->asset->asset_tag }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: start; border-bottom: 2px solid #27a355; padding-bottom: 20px; margin-bottom: 30px; }
        .company-info h1 { margin: 0; color: #27a355; font-size: 24px; text-transform: uppercase; letter-spacing: 1px; }
        .company-info p { margin: 5px 0 0 0; font-size: 12px; color: #666; }
        .document-title { text-align: right; }
        .document-title h2 { margin: 0; font-size: 28px; color: #333; }
        .document-title p { margin: 5px 0 0 0; font-size: 14px; font-weight: bold; color: #27a355; }

        .recipient-section { display: flex; justify-content: space-between; margin-bottom: 40px; background: #f9f9f9; padding: 20px; border-radius: 8px; }
        .recipient-box h3 { margin: 0 0 10px 0; font-size: 12px; color: #888; text-transform: uppercase; }
        .recipient-box p { margin: 2px 0; font-size: 14px; font-weight: bold; }

        .asset-details { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .asset-details th { text-align: left; background: #f4fbf6; color: #27a355; padding: 15px; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #e0e0e0; }
        .asset-details td { padding: 15px; border-bottom: 1px solid #eee; font-size: 14px; }
        .cost-row { background: #f0f7f2; font-weight: bold; }
        .cost-label { color: #27a355; }

        .terms { margin-bottom: 40px; font-size: 12px; color: #666; }
        .terms h4 { margin-bottom: 10px; color: #333; text-transform: uppercase; }
        .terms ul { padding-left: 20px; margin: 0; }
        .terms li { margin-bottom: 5px; }

        .signatures { display: flex; justify-content: space-between; margin-top: 80px; }
        .sig-box { width: 250px; border-top: 1px solid #333; text-align: center; padding-top: 10px; }
        .sig-box p { margin: 0; font-size: 12px; font-weight: bold; }
        .sig-box span { font-size: 10px; color: #888; display: block; margin-top: 5px; }

        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 20px; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 30px; background: #333; color: white; padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
        <span style="font-weight: bold;">🤝 Handover Protocol Preview. Please verify device cost and assignment details.</span>
        <button onclick="window.print()" style="background: #27a355; color: white; border: none; padding: 8px 30px; border-radius: 5px; cursor: pointer; font-weight: bold;">PRINT RECEIPT</button>
    </div>

    <div class="header">
        <div class="company-info">
            <h1>DLK TECHNOLOGIES</h1>
            <p>IT Asset Management Division</p>
            <p>Professional Hardware Distribution Services</p>
        </div>
        <div class="document-title">
            <h2>HANDOVER</h2>
            <p>ASSET ALLOCATION RECEIPT</p>
        </div>
    </div>

    <div class="recipient-section">
        <div class="recipient-box">
            <h3>ISSUED TO (EMPLOYEE)</h3>
            <p>{{ $allocation->employee->name }}</p>
            <p>{{ $allocation->employee->employee_code }}</p>
            <p>{{ $allocation->employee->department->name ?? 'Internal Team' }}</p>
        </div>
        <div class="recipient-box" style="text-align: right;">
            <h3>ALLOCATION DETAILS</h3>
            <p><span style="color: #888; font-weight: normal;">Handover Date:</span> {{ $allocation->allocated_at->format('d M, Y') }}</p>
            <p><span style="color: #888; font-weight: normal;">Handover ID:</span> #{{ strtoupper(substr($allocation->asset->asset_tag, 0, 3)) }}-{{ $allocation->id }}</p>
            <p><span style="color: #888; font-weight: normal;">Authorized By:</span> {{ $allocation->allocatedBy->name ?? 'Admin' }}</p>
        </div>
    </div>

    <table class="asset-details">
        <thead>
            <tr>
                <th>ASSET TAG</th>
                <th>HARDWARE DESCRIPTION</th>
                <th>SERIAL NUMBER</th>
                <th>CONDITION</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold; color: #27a355;">{{ $allocation->asset->asset_tag }}</td>
                <td>
                    <strong>{{ $allocation->asset->brand }} {{ $allocation->asset->model }}</strong>
                    <br><span style="font-size: 11px; color: #888;">{{ $allocation->asset->category->name }}</span>
                </td>
                <td>{{ $allocation->asset->serial_number }}</td>
                <td>{{ $allocation->condition_on_allocation }}</td>
            </tr>
            <tr class="cost-row">
                <td colspan="3" style="text-align: right;" class="cost-label">AGREED ASSET VALUE (INR)</td>
                <td style="font-size: 18px;">₹{{ number_format($allocation->asset->purchase_cost, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="terms">
        <h4>TERMS OF ALLOCATION</h4>
        <ul>
            <li>The employee acknowledges receipt of the hardware in the condition stated above.</li>
            <li>This asset is for professional use only and remains the property of Antigravity Systems.</li>
            <li>In case of loss or damage due to negligence, the employee may be liable for the **Agreed Asset Value** shown above.</li>
            <li>Hardware components (RAM, Storage, Battery) must not be tampered with or replaced without IT authorization.</li>
            <li>The device must be returned to the IT department upon separation from the company.</li>
        </ul>
    </div>

    <div class="signatures">
        <div class="sig-box">
            <p>{{ $allocation->employee->name }}</p>
            <span>Employee Signature & Date</span>
        </div>
        <div class="sig-box">
            <p>{{ $allocation->allocatedBy->name ?? 'IT Manager' }}</p>
            <span>Authorized Signature & Date</span>
        </div>
    </div>

    <div class="footer">
        <div>This is a formal asset distribution record. Please keep a copy for your records.</div>
        <div>DLK Technologies Asset Management &copy; {{ date('Y') }} | Generated at {{ date('H:i:s d-m-Y') }}</div>
    </div>
</body>
</html>
