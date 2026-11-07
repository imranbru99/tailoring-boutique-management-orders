<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <title>Measurement Sheet - {{ $customer->full_name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            color: #1f2937;
            font-size: 13px;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #1e1b4b;
            margin: 0;
        }
        .meta-grid {
            width: 100%;
            margin-bottom: 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
        }
        .meta-grid td {
            padding: 4px 8px;
        }
        .measurements-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .measurements-table th {
            background-color: #312e81;
            color: #ffffff;
            padding: 8px 12px;
            text-align: left;
            font-size: 12px;
        }
        .measurements-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }
        .measurements-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .val-badge {
            font-weight: bold;
            color: #4338ca;
            font-size: 15px;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signature-line {
            border-top: 1px solid #94a3b8;
            padding-top: 5px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1 class="title">BODY MEASUREMENT SPECIFICATION SHEET</h1>
                    <div style="color: #6366f1; font-weight: bold; margin-top: 3px;">
                        {{ $tenant->name ?? 'Tailoring Atelier' }} — Confidential Client Record
                    </div>
                </td>
                <td style="text-align: right;">
                    <div style="font-size: 16px; font-weight: bold; color: #374151;">
                        Profile: {{ $profile->profile_name }}
                    </div>
                    <div style="font-size: 11px; color: #64748b;">
                        Version: #{{ $profile->version }} | Unit: {{ strtoupper($profile->unit) }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-grid">
        <tr>
            <td style="width: 25%;"><strong>Client Name:</strong></td>
            <td style="width: 35%; color: #1e1b4b; font-weight: bold;">{{ $customer->full_name }}</td>
            <td style="width: 20%;"><strong>Client Code:</strong></td>
            <td style="width: 20%;">{{ $customer->customer_code }}</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong></td>
            <td>{{ $customer->phone }}</td>
            <td><strong>Gender:</strong></td>
            <td>{{ ucfirst($customer->gender ?? 'Unspecified') }}</td>
        </tr>
        <tr>
            <td><strong>Template Used:</strong></td>
            <td>{{ $template->name ?? 'Custom Template' }}</td>
            <td><strong>Recorded On:</strong></td>
            <td>{{ $profile->updated_at->format('M d, Y') }}</td>
        </tr>
    </table>

    <h3 style="color: #1e1b4b; margin-bottom: 5px;">Recorded Anatomical Measurements</h3>
    <table class="measurements-table">
        <thead>
            <tr>
                <th style="width: 50%;">Measurement Parameter</th>
                <th style="width: 30%;">Recorded Value</th>
                <th style="width: 20%;">Unit</th>
            </tr>
        </thead>
        <tbody>
            @php $values = $profile->values ?? []; @endphp
            @forelse($values as $key => $val)
            <tr>
                <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                <td><span class="val-badge">{{ $val }}</span></td>
                <td>{{ $profile->unit }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; color: #94a3b8; padding: 20px;">
                    No measurement fields recorded in this profile yet.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($profile->fitting_notes)
    <div style="margin-top: 25px; padding: 12px; background: #fffbeb; border-left: 3px solid #f59e0b;">
        <strong style="color: #92400e;">Tailor Fitting Notes & Body Posture Adjustments:</strong><br>
        <span style="color: #78350f;">{{ $profile->fitting_notes }}</span>
    </div>
    @endif

    <table class="signatures">
        <tr>
            <td style="width: 40%; text-align: center;">
                <div style="height: 40px;"></div>
                <div class="signature-line">Master Tailor Signature & Date</div>
            </td>
            <td style="width: 20%;"></td>
            <td style="width: 40%; text-align: center;">
                <div style="height: 40px;"></div>
                <div class="signature-line">Customer Approval / Witness</div>
            </td>
        </tr>
    </table>
</body>
</html>
