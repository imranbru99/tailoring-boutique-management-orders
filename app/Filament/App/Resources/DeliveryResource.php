<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\Delivery;
use App\Services\UnicodePdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DeliveryResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = Delivery::class;
    protected static string $moduleKey = 'deliveries';
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Orders & Production';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Dispatch & Handover')
                    ->schema([
                        Forms\Components\TextInput::make('delivery_number')
                            ->default(fn () => 'DEL-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)))
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('delivery_type')
                            ->options([
                                'shop_pickup' => 'Counter / Atelier Pickup',
                                'home_delivery' => 'Dedicated Atelier Home Drop',
                                'courier' => 'Third-Party Courier Express',
                            ])
                            ->default('shop_pickup')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'ready' => 'Ready for Pickup / Dispatch',
                                'dispatched' => 'Dispatched with Courier',
                                'delivered' => 'Delivered & Signed',
                                'failed' => 'Delivery Failed',
                                'returned' => 'Returned to Atelier',
                            ])
                            ->default('ready')
                            ->required(),
                        Forms\Components\TextInput::make('recipient_name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('recipient_phone')
                            ->tel()
                            ->required()
                            ->maxLength(50),
                        Forms\Components\DatePicker::make('scheduled_date')
                            ->default(now()->toDateString())
                            ->required(),
                        Forms\Components\TextInput::make('delivery_fee')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\TextInput::make('courier_name')
                            ->placeholder('e.g. DHL, FedEx, RedX, Steadfast'),
                        Forms\Components\TextInput::make('tracking_number')
                            ->placeholder('e.g. TRK-992384729'),
                        Forms\Components\Select::make('delivery_staff_id')
                            ->relationship('deliveryStaff', 'name')
                            ->label('Delivery Staff')
                            ->searchable(),
                        Forms\Components\Textarea::make('delivery_address')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('delivery_number')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->searchable()
                    ->label('Order #')
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('delivery_type')
                    ->colors([
                        'primary' => 'shop_pickup',
                        'warning' => 'home_delivery',
                        'info' => 'courier',
                    ]),
                Tables\Columns\TextColumn::make('recipient_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('recipient_phone')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'ready',
                        'info' => 'dispatched',
                        'success' => 'delivered',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('scheduled_date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('delivery_type')
                    ->options([
                        'shop_pickup' => 'Pickup',
                        'home_delivery' => 'Home Delivery',
                        'courier' => 'Courier',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'ready' => 'Ready',
                        'dispatched' => 'Dispatched',
                        'delivered' => 'Delivered',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print_slip')
                    ->label('Delivery Slip')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->action(function (Delivery $record, UnicodePdfService $pdfService) {
                        return response()->streamDownload(function () use ($record, $pdfService) {
                            echo $pdfService->generateDeliverySlipPdf($record)->render();
                        }, "Delivery-{$record->delivery_number}.pdf");
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
            'index' => DeliveryResource\Pages\ListDeliveries::route('/'),
            'create' => DeliveryResource\Pages\CreateDelivery::route('/create'),
            'edit' => DeliveryResource\Pages\EditDelivery::route('/{record}/edit'),
        ];
    }
}
