<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\Invoice;
use App\Services\InvoicePaymentService;
use App\Services\UnicodePdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = Invoice::class;
    protected static string $moduleKey = 'billing';
    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationGroup = 'Sales & Finance';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Invoice Header')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->full_name} ({$record->customer_code})")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('invoice_date')
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->default(now()->addDays(14))
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'issued' => 'Issued / Sent',
                                'partially_paid' => 'Partially Paid',
                                'paid' => 'Fully Paid',
                                'overdue' => 'Overdue',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('issued')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Line Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('total_price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Settlement Balances')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\TextInput::make('discount_amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\TextInput::make('tax_rate')
                            ->numeric()
                            ->suffix('%')
                            ->default(0.00),
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\TextInput::make('paid_amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\TextInput::make('balance_amount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order #')
                    ->fontFamily('mono')
                    ->badge(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'issued',
                        'info' => 'partially_paid',
                        'success' => 'paid',
                        'danger' => 'overdue',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->color('success'),
                Tables\Columns\TextColumn::make('balance_amount')
                    ->label('Balance Due')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('invoice_date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'issued' => 'Issued',
                        'partially_paid' => 'Partially Paid',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download_pdf')
                    ->label('Print Invoice')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->action(function (Invoice $record, UnicodePdfService $pdfService) {
                        return response()->streamDownload(function () use ($record, $pdfService) {
                            echo $pdfService->generateInvoicePdf($record)->render();
                        }, "Invoice-{$record->invoice_number}.pdf");
                    }),
                Tables\Actions\Action::make('record_payment')
                    ->label('Receive Pay')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(fn (Invoice $record) => $record->balance_amount),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash in Drawer',
                                'card' => 'Credit / Debit POS Card',
                                'bank_transfer' => 'Bank Wire Transfer',
                                'bkash' => 'bKash Merchant Pay',
                                'nagad' => 'Nagad Digital Wallet',
                                'online' => 'Online Payment Gateway',
                            ])
                            ->default('cash')
                            ->required(),
                        Forms\Components\TextInput::make('transaction_reference')
                            ->placeholder('Card auth code, transaction ID, bank reference'),
                        Forms\Components\Textarea::make('notes')
                            ->placeholder('Payment receipt notes'),
                    ])
                    ->action(function (Invoice $record, array $data, InvoicePaymentService $service) {
                        try {
                            $service->recordPayment(
                                $record->tenant_id,
                                $record->branch_id,
                                $record->customer_id,
                                (float) $data['amount'],
                                $data['payment_method'],
                                $record->order_id,
                                $record->id,
                                false,
                                false,
                                $data['transaction_reference'] ?? null,
                                $data['notes'] ?? null
                            );
                            Notification::make()
                                ->title('Payment Received')
                                ->body("Received payment of \${$data['amount']} on Invoice #{$record->invoice_number}.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Payment Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => InvoiceResource\Pages\ListInvoices::route('/'),
            'create' => InvoiceResource\Pages\CreateInvoice::route('/create'),
            'edit' => InvoiceResource\Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
