<?php

namespace App\Filament\App\Widgets;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Delivery;
use App\Models\Expense;
use App\Models\FabricType;
use App\Models\Order;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BusinessStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $tenantId = auth()->user()?->tenant_id;
        $currency = auth()->user()?->tenant?->currency_symbol ?? '$';

        $totalCustomers = Customer::where('tenant_id', $tenantId)->count();
        $totalOrders = Order::where('tenant_id', $tenantId)->count();
        $inProduction = Order::where('tenant_id', $tenantId)
            ->whereIn('status', ['cutting', 'stitching', 'embroidery', 'finishing'])
            ->count();
        $readyDelivery = Order::where('tenant_id', $tenantId)
            ->where('status', 'ready_for_delivery')
            ->count();
        $todayDeliveries = Delivery::where('tenant_id', $tenantId)
            ->whereDate('scheduled_date', now()->toDateString())
            ->count();
        $todayAppointments = Appointment::where('tenant_id', $tenantId)
            ->whereDate('appointment_date', now()->toDateString())
            ->count();

        $monthlyRevenue = (float) Payment::where('tenant_id', $tenantId)
            ->where('is_refund', false)
            ->whereMonth('payment_date', now()->month)
            ->sum('amount');

        $monthlyExpenses = (float) Expense::where('tenant_id', $tenantId)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');

        $netProfit = $monthlyRevenue - $monthlyExpenses;

        $outstandingDues = (float) Order::where('tenant_id', $tenantId)
            ->where('payment_status', '!=', 'paid')
            ->sum('balance_amount');

        $fabricStockValuation = (float) FabricType::where('tenant_id', $tenantId)
            ->selectRaw('SUM(current_stock * cost_per_unit) as val')
            ->value('val') ?? 0.0;

        $lowStockFabrics = FabricType::where('tenant_id', $tenantId)
            ->whereColumn('current_stock', '<=', 'reorder_level')
            ->count();

        return [
            Stat::make('Total Clients', number_format($totalCustomers))
                ->description('Active client book')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([3, 5, 8, 12, 18, 25, $totalCustomers]),

            Stat::make('Orders in Production', number_format($inProduction))
                ->description("{$totalOrders} total orders logged")
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning')
                ->chart([2, 6, 8, 12, 10, $inProduction]),

            Stat::make('Ready for Delivery', number_format($readyDelivery))
                ->description("{$todayDeliveries} deliveries scheduled today")
                ->descriptionIcon('heroicon-m-truck')
                ->color('success')
                ->chart([1, 3, 4, 7, 5, $readyDelivery]),

            Stat::make('Month Net Revenue', "{$currency}" . number_format($monthlyRevenue, 2))
                ->description("Net Profit: {$currency}" . number_format($netProfit, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($netProfit >= 0 ? 'success' : 'danger')
                ->chart([1200, 3200, 4800, 6200, 7900, $monthlyRevenue]),

            Stat::make('Outstanding Receivables', "{$currency}" . number_format($outstandingDues, 2))
                ->description('Pending client balances')
                ->descriptionIcon('heroicon-m-clock')
                ->color($outstandingDues > 0 ? 'danger' : 'gray'),

            Stat::make('Material Inventory Value', "{$currency}" . number_format($fabricStockValuation, 2))
                ->description("{$lowStockFabrics} fabrics below reorder level")
                ->descriptionIcon('heroicon-m-swatch')
                ->color($lowStockFabrics > 0 ? 'warning' : 'info'),
        ];
    }
}
