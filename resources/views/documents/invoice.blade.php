<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $direction }}">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            color: #1f2937;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header-table, .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .logo-title {
            font-size: 24px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .subtitle {
            color: #64748b;
            font-size: 11px;
        }
        .invoice-badge {
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
            text-align: right;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            margin-top: 20px;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #f1f5f9;
            color: #334155;
            text-align: left;
            padding: 10px;
            font-size: 12px;
            border-bottom: 2px solid #cbd5e1;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        .total-row td {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #94a3b8;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-paid { background: #dcfce7; color: #166534; }
        .badge-partial { background: #fef9c3; color: #854d0e; }
        .badge-unpaid { background: #fee2e2; color: #991b1b; }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <h1 class="logo-title">{{ $tenant->name ?? 'Tailoring & Boutique Management' }}</h1>
                <div class="subtitle">{{ $tenant->business_type ?? 'Boutique Atelier' }}</div>
                <div style="margin-top: 6px; font-size: 11px; color: #475569;">
                    {{ $tenant->address ?? 'Main Fashion Studio' }}<br>
                    Phone: {{ $tenant->phone ?? '+1 (555) 000-0000' }} | Email: {{ $tenant->email ?? 'info@tailor.test' }}
                </div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="invoice-badge">INVOICE</div>
                <div style="font-weight: bold; margin-top: 4px;">#{{ $invoice->invoice_number }}</div>
                <div style="font-size: 11px; color: #64748b;">
                    Date: {{ $invoice->invoice_date->format('M d, Y') }}<br>
                    Due Date: {{ $invoice->due_date->format('M d, Y') }}<br>
                    Status: <span class="badge {{ $invoice->status === 'paid' ? 'badge-paid' : ($invoice->status === 'partially_paid' ? 'badge-partial' : 'badge-unpaid') }}">
                        {{ strtoupper(str_replace('_', ' ', $invoice->status)) }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <table class="meta-box" style="width: 100%;">
        <tr>
            <td style="width: 50%;">
                <strong style="color: #475569; font-size: 11px; text-transform: uppercase;">Billed To:</strong><br>
                <span style="font-size: 14px; font-weight: bold; color: #0f172a;">{{ $customer->full_name }}</span><br>
                Client Code: {{ $customer->customer_code }}<br>
                Phone: {{ $customer->phone }}<br>
                Email: {{ $customer->email ?? 'N/A' }}
            </td>
            <td style="width: 50%;">
                <strong style="color: #475569; font-size: 11px; text-transform: uppercase;">Order Specifications:</strong><br>
                Order Ref: <strong>#{{ $order?->order_number ?? 'Boutique Sale' }}</strong><br>
                Order Type: {{ ucwords(str_replace('_', ' ', $order?->order_type ?? 'Retail')) }}<br>
                Expected Delivery: {{ $order?->expected_delivery_date?->format('M d, Y') ?? 'Immediate' }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 50%;">Item & Tailoring Description</th>
                <th style="width: 15%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Unit Price</th>
                <th style="width: 20%; text-align: right;">Total ({{ $tenant->currency_symbol ?? '$' }})</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                <td style="text-align: right;">{{ number_format($item->total_price, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #94a3b8;">No items listed on invoice.</td>
            </tr>
            @endforelse
            <tr>
                <td colspan="2"></td>
                <td style="text-align: right; font-weight: bold;">Subtotal:</td>
                <td style="text-align: right;">{{ $tenant->currency_symbol ?? '$' }}{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
            <tr>
                <td colspan="2"></td>
                <td style="text-align: right; color: #16a34a;">Discount:</td>
                <td style="text-align: right; color: #16a34a;">-{{ $tenant->currency_symbol ?? '$' }}{{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            @endif
            @if($invoice->tax_amount > 0)
            <tr>
                <td colspan="2"></td>
                <td style="text-align: right;">Tax ({{ $invoice->tax_rate }}%):</td>
                <td style="text-align: right;">+{{ $tenant->currency_symbol ?? '$' }}{{ number_format($invoice->tax_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td colspan="2"></td>
                <td style="text-align: right; color: #0f172a;">Grand Total:</td>
                <td style="text-align: right; color: #4f46e5;">{{ $tenant->currency_symbol ?? '$' }}{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td style="text-align: right; color: #166534; font-weight: bold;">Total Paid:</td>
                <td style="text-align: right; color: #166534; font-weight: bold;">{{ $tenant->currency_symbol ?? '$' }}{{ number_format($invoice->paid_amount, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td style="text-align: right; color: #991b1b; font-weight: bold;">Balance Due:</td>
                <td style="text-align: right; color: #991b1b; font-weight: bold;">{{ $tenant->currency_symbol ?? '$' }}{{ number_format($invoice->balance_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if($invoice->notes)
    <div style="margin-top: 25px; padding: 10px; background: #fafafa; border-left: 3px solid #6366f1; font-size: 11px;">
        <strong>Terms & Tailoring Notes:</strong><br>
        {{ $invoice->notes }}
    </div>
    @endif

    <div class="footer">
        Thank you for choosing {{ $tenant->name ?? 'our atelier' }} for your bespoke fashion. | Generated by Tailoring and Boutique Management System
    </div>
</body>
</html>
