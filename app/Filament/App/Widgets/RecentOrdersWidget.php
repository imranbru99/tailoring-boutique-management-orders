<?php

namespace App\Filament\App\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $tenantId = auth()->user()?->tenant_id;

        return $table
            ->query(
                Order::query()->where('tenant_id', $tenantId)->latest()->take(6)
            )
            ->heading('Recent Active Tailoring & Boutique Orders')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->weight('bold')
                    ->fontFamily('mono'),
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->label('Client Name')
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('order_type')
                    ->colors(['primary']),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => fn ($state) => in_array($state, ['cutting', 'stitching', 'finishing']),
                        'info' => fn ($state) => in_array($state, ['confirmed', 'quality_inspection']),
                        'success' => fn ($state) => in_array($state, ['ready_for_delivery', 'delivered']),
                        'gray' => 'inquiry',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD'),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date('M d, Y')
                    ->label('Target Delivery'),
            ]);
    }
}
