<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = Payment::class;
    protected static string $moduleKey = 'billing';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Sales & Finance';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Transaction')
                    ->schema([
                        Forms\Components\TextInput::make('payment_number')
                            ->default(fn () => 'PAY-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)))
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
                        Forms\Components\Select::make('invoice_id')
                            ->relationship('invoice', 'invoice_number')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash',
                                'card' => 'POS Card',
                                'bank_transfer' => 'Bank Wire Transfer',
                                'bkash' => 'bKash',
                                'nagad' => 'Nagad',
                                'online' => 'Online Gateway',
                            ])
                            ->default('cash')
                            ->required(),
                        Forms\Components\DatePicker::make('payment_date')
                            ->default(now())
                            ->required(),
                        Forms\Components\TextInput::make('transaction_reference')
                            ->placeholder('Transaction ID, Card authorization ref'),
                        Forms\Components\Toggle::make('is_advance')
                            ->label('Mark as Advance Deposit')
                            ->default(false),
                        Forms\Components\Toggle::make('is_refund')
                            ->label('Is Client Refund')
                            ->default(false),
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payment_number')
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
                    ->badge(),
                Tables\Columns\BadgeColumn::make('payment_method')
                    ->colors([
                        'primary' => 'cash',
                        'info' => 'card',
                        'warning' => 'bank_transfer',
                        'success' => fn ($state) => in_array($state, ['bkash', 'nagad']),
                    ]),
                Tables\Columns\TextColumn::make('amount')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->sortable()
                    ->weight('bold')
                    ->color(fn ($record) => $record->is_refund ? 'danger' : 'success'),
                Tables\Columns\IconColumn::make('is_advance')
                    ->label('Advance')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_refund')
                    ->label('Refund')
                    ->boolean(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'card' => 'Card',
                        'bank_transfer' => 'Bank Transfer',
                        'bkash' => 'bKash',
                        'nagad' => 'Nagad',
                    ]),
                Tables\Filters\TernaryFilter::make('is_advance')
                    ->label('Advance Deposits'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => PaymentResource\Pages\ListPayments::route('/'),
            'create' => PaymentResource\Pages\CreatePayment::route('/create'),
            'edit' => PaymentResource\Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
