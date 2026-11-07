<?php

namespace App\Filament\Customer\Resources;

use App\Models\Customer;
use App\Models\Order;
use App\Services\UnicodePdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyOrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'My Bespoke Orders';
    protected static ?string $title = 'My Orders';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $customer = Customer::where('user_id', $user?->id)->orWhere('email', $user?->email)->first();
        $customerId = $customer?->id ?? 0;

        return parent::getEloquentQuery()->where('customer_id', $customerId);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_number')->disabled(),
                Forms\Components\TextInput::make('status')->disabled(),
                Forms\Components\TextInput::make('total_amount')->disabled(),
                Forms\Components\TextInput::make('paid_amount')->disabled(),
                Forms\Components\TextInput::make('balance_amount')->disabled(),
                Forms\Components\DatePicker::make('expected_delivery_date')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->weight('bold')
                    ->fontFamily('mono'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => fn ($state) => in_array($state, ['cutting', 'stitching', 'finishing']),
                        'info' => fn ($state) => in_array($state, ['confirmed', 'quality_inspection']),
                        'success' => fn ($state) => in_array($state, ['ready_for_delivery', 'delivered']),
                        'gray' => 'inquiry',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD'),
                Tables\Columns\TextColumn::make('balance_amount')
                    ->label('Pending Due')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date('M d, Y')
                    ->label('Estimated Handover'),
            ])
            ->actions([
                Tables\Actions\Action::make('download_invoice')
                    ->label('Download Invoice')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->action(function (Order $record, UnicodePdfService $pdfService) {
                        $invoice = $record->invoices()->first();
                        if ($invoice) {
                            return response()->streamDownload(function () use ($invoice, $pdfService) {
                                echo $pdfService->generateInvoicePdf($invoice)->render();
                            }, "Invoice-{$invoice->invoice_number}.pdf");
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => MyOrderResource\Pages\ListMyOrders::route('/'),
        ];
    }
}
