<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <title>Job Card {{ $jobCard->job_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            color: #0f172a;
            font-size: 13px;
            margin: 0;
            padding: 20px;
        }
        .header {
            border-bottom: 2px dashed #64748b;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .stage-badge {
            background: #0284c7;
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .meta-table, .snapshot-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .meta-table td {
            padding: 6px;
            border: 1px solid #e2e8f0;
        }
        .snapshot-table th {
            background: #f1f5f9;
            padding: 6px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        .snapshot-table td {
            padding: 6px;
            border: 1px solid #cbd5e1;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h2 style="margin: 0; color: #0369a1;">WORKSHOP PRODUCTION JOB CARD</h2>
                    <div style="font-size: 11px; color: #64748b;">{{ $tenant->name ?? 'Tailoring Management' }}</div>
                </td>
                <td style="text-align: right;">
                    <div style="font-size: 16px; font-weight: bold;">#{{ $jobCard->job_number }}</div>
                    <span class="stage-badge">{{ $jobCard->stage }}</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 25%; background: #f8fafc;"><strong>Order Number:</strong></td>
            <td style="width: 25%; font-weight: bold;">{{ $order->order_number }}</td>
            <td style="width: 25%; background: #f8fafc;"><strong>Due Delivery Date:</strong></td>
            <td style="width: 25%; color: #dc2626; font-weight: bold;">{{ $order->expected_delivery_date->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td style="background: #f8fafc;"><strong>Garment Item:</strong></td>
            <td>{{ $item?->item_name ?? 'Custom Garment' }}</td>
            <td style="background: #f8fafc;"><strong>Priority:</strong></td>
            <td><strong>{{ strtoupper($jobCard->priority) }}</strong></td>
        </tr>
        <tr>
            <td style="background: #f8fafc;"><strong>Assigned Tailor:</strong></td>
            <td>{{ $jobCard->assignedTailor?->name ?? 'Unassigned' }}</td>
            <td style="background: #f8fafc;"><strong>Est. Hours:</strong></td>
            <td>{{ $jobCard->estimated_hours }} hrs</td>
        </tr>
    </table>

    <h4 style="margin-top: 15px; margin-bottom: 5px; color: #0369a1;">Measurement Snapshot (Locked at Order Time)</h4>
    @php
        $snapshot = $item?->measurement_snapshot['values'] ?? [];
        $unit = $item?->measurement_snapshot['unit'] ?? 'inch';
    @endphp
    <table class="snapshot-table">
        <thead>
            <tr>
                <th style="width: 60%;">Measurement Parameter</th>
                <th style="width: 40%;">Specification Value ({{ $unit }})</th>
            </tr>
        </thead>
        <tbody>
            @forelse($snapshot as $field => $val)
            <tr>
                <td>{{ ucwords(str_replace('_', ' ', $field)) }}</td>
                <td style="font-weight: bold; color: #0284c7;">{{ $val }} {{ $unit }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="2" style="text-align: center; color: #94a3b8;">Standard tailoring sizing / No custom values.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 10px; border-radius: 4px; margin-top: 15px;">
        <strong style="color: #166534;">Workshop & Stitching Instructions:</strong><br>
        <span style="font-size: 12px; color: #14532d;">{{ $jobCard->instructions ?? $item?->special_instructions ?? 'Follow standard atelier quality guidelines.' }}</span>
    </div>

    <div style="margin-top: 30px; border-top: 1px solid #cbd5e1; padding-top: 10px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">Tailor Checked: [ &nbsp; ] Started &nbsp; [ &nbsp; ] Completed</td>
                <td style="width: 50%; text-align: right;">Inspector Sign-off: _______________________</td>
            </tr>
        </table>
    </div>
</body>
</html>
