<?php

namespace App\Filament\App\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueTrendChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue & Financial Inflow Trend';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        $months = [];
        $values = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $months[] = $monthLabel;

            $rev = (float) Payment::where('tenant_id', $tenantId)
                ->where('is_refund', false)
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');

            $values[] = $rev;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Net Received ($)',
                    'data' => $values,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'fill' => true,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
