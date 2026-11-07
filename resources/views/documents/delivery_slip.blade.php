<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <title>Delivery Slip {{ $delivery->delivery_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            color: #1e293b;
            font-size: 13px;
            margin: 0;
            padding: 20px;
        }
        .slip-box {
            border: 2px solid #0f766e;
            padding: 15px;
            border-radius: 8px;
        }
        .header {
            border-bottom: 2px solid #0f766e;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .meta-table td {
            padding: 5px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="slip-box">
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <h2 style="margin: 0; color: #0f766e;">DISPATCH & DELIVERY SLIP</h2>
                        <div style="font-size: 11px; color: #64748b;">{{ $tenant->name ?? 'Tailoring Atelier' }}</div>
                    </td>
                    <td style="text-align: right;">
                        <strong style="font-size: 16px;">#{{ $delivery->delivery_number }}</strong><br>
                        <span style="font-size: 11px; background: #ccfbf1; color: #0f766e; padding: 2px 6px; border-radius: 4px; font-weight: bold;">
                            {{ strtoupper(str_replace('_', ' ', $delivery->delivery_type)) }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <table class="meta-table">
            <tr>
                <td style="width: 50%;">
                    <strong>Recipient Information:</strong><br>
                    Name: <strong>{{ $delivery->recipient_name }}</strong><br>
                    Phone: {{ $delivery->recipient_phone }}<br>
                    Address: {{ $delivery->delivery_address ?? 'Pickup from Shop Atelier' }}
                </td>
                <td style="width: 50%;">
                    <strong>Order Details:</strong><br>
                    Order Ref: #{{ $order->order_number }}<br>
                    Scheduled Date: {{ $delivery->scheduled_date->format('M d, Y') }}<br>
                    Courier / Staff: {{ $delivery->courier_name ?? $delivery->deliveryStaff?->name ?? 'Direct Pickup' }}<br>
                    @if($delivery->tracking_number)
                    Tracking #: <strong>{{ $delivery->tracking_number }}</strong>
                    @endif
                </td>
            </tr>
        </table>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px; border-radius: 4px; margin-top: 10px;">
            <strong>Items Dispatched:</strong>
            <ul style="margin: 5px 0 0 15px; padding: 0;">
                @foreach($order->items as $item)
                <li>{{ $item->quantity }}x {{ $item->item_name }}</li>
                @endforeach
            </ul>
        </div>

        <table style="width: 100%; margin-top: 35px;">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <div style="border-top: 1px solid #94a3b8; width: 80%; margin: 0 auto; padding-top: 5px; font-size: 11px;">
                        Dispatched By (Staff Signature)
                    </div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div style="border-top: 1px solid #94a3b8; width: 80%; margin: 0 auto; padding-top: 5px; font-size: 11px;">
                        Customer Signature & Proof of Delivery
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
