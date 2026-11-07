<?php

namespace App\Filament\App\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersStageChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Order Workflow Stage Distribution';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $tenantId = auth()->user()?->tenant_id;

        $inquiry = Order::where('tenant_id', $tenantId)->where('status', 'inquiry')->count();
        $confirmed = Order::where('tenant_id', $tenantId)->where('status', 'confirmed')->count();
        $cutting = Order::where('tenant_id', $tenantId)->where('status', 'cutting')->count();
        $stitching = Order::where('tenant_id', $tenantId)->where('status', 'stitching')->count();
        $finishing = Order::where('tenant_id', $tenantId)->where('status', 'finishing')->count();
        $qc = Order::where('tenant_id', $tenantId)->where('status', 'quality_inspection')->count();
        $fitting = Order::where('tenant_id', $tenantId)->where('status', 'fitting_required')->count();
        $ready = Order::where('tenant_id', $tenantId)->where('status', 'ready_for_delivery')->count();
        $delivered = Order::where('tenant_id', $tenantId)->where('status', 'delivered')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => [$inquiry, $confirmed, $cutting, $stitching, $finishing, $qc, $fitting, $ready, $delivered],
                    'backgroundColor' => [
                        '#94a3b8', '#38bdf8', '#fbbf24', '#f97316', '#a855f7',
                        '#6366f1', '#ec4899', '#10b981', '#059669',
                    ],
                ],
            ],
            'labels' => [
                'Inquiry', 'Confirmed', 'Cutting', 'Stitching', 'Finishing',
                'Quality Check', 'Fitting Trial', 'Ready Delivery', 'Delivered',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
