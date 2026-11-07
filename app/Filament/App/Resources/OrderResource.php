<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\Customer;
use App\Models\GarmentType;
use App\Models\MeasurementProfile;
use App\Models\Order;
use App\Services\InvoicePaymentService;
use App\Services\OrderWorkflowService;
use App\Services\UnicodePdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = Order::class;
    protected static string $moduleKey = 'orders';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Orders & Production';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Header & Client')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->default(fn () => 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)))
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->full_name} ({$record->customer_code})")
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->required(),
                        Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('order_type')
                            ->options([
                                'custom_tailoring' => 'Custom Bespoke Tailoring',
                                'ready_made' => 'Ready-Made Boutique Garment',
                                'alteration' => 'Garment Alteration & Repair',
                                'bridal' => 'Bridal & Haute Couture',
                                'uniform' => 'Corporate & Uniform Order',
                                'embroidery' => 'Custom Embroidery Project',
                            ])
                            ->default('custom_tailoring')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'inquiry' => 'Inquiry',
                                'quotation' => 'Quotation',
                                'awaiting_advance' => 'Awaiting Advance',
                                'confirmed' => 'Confirmed (In Queue)',
                                'material_preparation' => 'Material Preparation',
                                'cutting' => 'Cutting Workshop',
                                'stitching' => 'Stitching Workshop',
                                'embroidery' => 'Embroidery Studio',
                                'finishing' => 'Hand Finishing & Pressing',
                                'quality_inspection' => 'Quality Inspection',
                                'fitting_required' => 'Fitting Trial Required',
                                'alteration_required' => 'Alteration in Progress',
                                'final_approval' => 'Final Approval Passed',
                                'ready_for_delivery' => 'Ready for Handover',
                                'dispatched' => 'Dispatched with Courier',
                                'delivered' => 'Delivered to Client',
                                'completed' => 'Order Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('inquiry')
                            ->required(),
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Standard Low',
                                'normal' => 'Normal Priority',
                                'high' => 'High Priority',
                                'urgent' => 'Urgent / VIP Rush',
                            ])
                            ->default('normal')
                            ->required(),
                        Forms\Components\DatePicker::make('order_date')
                            ->default(now())
                            ->required(),
                        Forms\Components\DatePicker::make('expected_delivery_date')
                            ->default(now()->addDays(7))
                            ->required(),
                        Forms\Components\Select::make('assigned_tailor_id')
                            ->relationship('assignedTailor', 'name')
                            ->label('Master Tailor')
                            ->searchable(),
                        Forms\Components\Select::make('assigned_designer_id')
                            ->relationship('assignedDesigner', 'name')
                            ->label('Fashion Designer / Stylist')
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make('Garment Items & Measurement Snapshots')
                    ->description('Add custom items. Measurements are snapshotted permanently to safeguard against later alterations to client cards.')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship('items')
                            ->schema([
                                Forms\Components\TextInput::make('item_name')
                                    ->required()
                                    ->placeholder('e.g. Bespoke Tuxedo Jacket, Pleated Trousers, Silk Waistcoat')
                                    ->columnSpan(2),
                                Forms\Components\Select::make('garment_type_id')
                                    ->relationship('garmentType', 'name')
                                    ->label('Garment Type')
                                    ->searchable()
                                    ->columnSpan(1),
                                Forms\Components\Select::make('fabric_type_id')
                                    ->relationship('fabricType', 'name')
                                    ->label('Selected Fabric')
                                    ->searchable()
                                    ->columnSpan(1),
                                Forms\Components\Select::make('measurement_profile_id')
                                    ->relationship('measurementProfile', 'profile_name')
                                    ->label('Body Measurement Card')
                                    ->searchable()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('material_cost')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0.00)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('labor_cost')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0.00)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0.00)
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('total_price')
                                    ->numeric()
                                    ->prefix('$')
                                    ->default(0.00)
                                    ->columnSpan(1),
                                Forms\Components\Textarea::make('special_instructions')
                                    ->placeholder('Specific pleats, monogramming, lining color, lapel width')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(5)
                            ->defaultItems(1)
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Financial Accounting & Order Totals')
                    ->schema([
                        Forms\Components\TextInput::make('additional_charges')
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
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('paid_amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('balance_amount')
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
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
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'inquiry',
                        'info' => fn ($state) => in_array($state, ['quotation', 'awaiting_advance', 'confirmed']),
                        'warning' => fn ($state) => in_array($state, ['cutting', 'stitching', 'embroidery', 'finishing']),
                        'primary' => fn ($state) => in_array($state, ['quality_inspection', 'fitting_required', 'ready_for_delivery']),
                        'success' => fn ($state) => in_array($state, ['delivered', 'completed']),
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'partial',
                        'success' => 'paid',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('balance_amount')
                    ->label('Due Balance')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'gray' => 'low',
                        'info' => 'normal',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'inquiry' => 'Inquiry',
                        'confirmed' => 'Confirmed',
                        'cutting' => 'Cutting',
                        'stitching' => 'Stitching',
                        'quality_inspection' => 'Quality Inspection',
                        'ready_for_delivery' => 'Ready for Delivery',
                        'delivered' => 'Delivered',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'unpaid' => 'Unpaid',
                        'partial' => 'Partially Paid',
                        'paid' => 'Fully Paid',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('transition_status')
                    ->label('Advance Stage')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('new_status')
                            ->label('Target Production Stage')
                            ->options([
                                'confirmed' => 'Confirm Order & Queue Job Cards',
                                'cutting' => 'Move to Cutting',
                                'stitching' => 'Move to Stitching',
                                'finishing' => 'Move to Hand Finishing',
                                'quality_inspection' => 'Submit to Quality Control',
                                'fitting_required' => 'Flag Fitting Trial Required',
                                'ready_for_delivery' => 'Mark Ready for Handover',
                                'delivered' => 'Confirm Delivered to Client',
                                'completed' => 'Close Order (Completed)',
                                'cancelled' => 'Cancel Order',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->placeholder('Artisan transition remarks'),
                    ])
                    ->action(function (Order $record, array $data, OrderWorkflowService $workflow) {
                        try {
                            $workflow->transition($record, $data['new_status'], $data['notes'] ?? null);
                            Notification::make()
                                ->title('Workflow Advanced')
                                ->body("Order #{$record->order_number} transitioned to [{$data['new_status']}].")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Transition Error')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('print_invoice')
                    ->label('Invoice PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->action(function (Order $record, InvoicePaymentService $invoiceService, UnicodePdfService $pdfService) {
                        $invoice = $invoiceService->generateInvoiceForOrder($record);
                        return response()->streamDownload(function () use ($invoice, $pdfService) {
                            echo $pdfService->generateInvoicePdf($invoice)->render();
                        }, "Invoice-{$invoice->invoice_number}.pdf");
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
            'index' => OrderResource\Pages\ListOrders::route('/'),
            'create' => OrderResource\Pages\CreateOrder::route('/create'),
            'edit' => OrderResource\Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
