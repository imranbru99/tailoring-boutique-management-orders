<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomerResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = Customer::class;
    protected static string $moduleKey = 'customers';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Clients & Profiles';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Client Identity & Contact')
                    ->schema([
                        Forms\Components\TextInput::make('customer_code')
                            ->default(fn () => 'CUST-' . strtoupper(substr(uniqid(), -5)))
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('last_name')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('alt_phone')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'child' => 'Child',
                                'other' => 'Other',
                            ]),
                        Forms\Components\DatePicker::make('date_of_birth'),
                        Forms\Components\Select::make('customer_type')
                            ->options([
                                'regular' => 'Regular Retail',
                                'vip' => 'VIP Couture',
                                'bridal' => 'Bridal Client',
                                'wholesale' => 'Wholesale / Bulk',
                                'corporate' => 'Corporate Uniform',
                            ])
                            ->default('regular')
                            ->required(),
                        Forms\Components\Select::make('preferred_communication')
                            ->options([
                                'phone' => 'Phone Call',
                                'sms' => 'SMS Notification',
                                'whatsapp' => 'WhatsApp Messenger',
                                'email' => 'Email',
                            ])
                            ->default('phone'),
                    ])->columns(2),

                Forms\Components\Section::make('Location & Preferences')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('postal_code')
                            ->maxLength(20),
                        Forms\Components\TagsInput::make('tags')
                            ->placeholder('Add style tags: e.g. Slim-fit, Italian Cut, Silk Lover')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->placeholder('Artisan notes, skin sensitivities, posture preferences')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_code')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-m-phone'),
                Tables\Columns\BadgeColumn::make('customer_type')
                    ->colors([
                        'primary' => 'regular',
                        'warning' => 'vip',
                        'danger' => 'bridal',
                        'success' => 'wholesale',
                    ]),
                Tables\Columns\TextColumn::make('measurement_profiles_count')
                    ->counts('measurementProfiles')
                    ->label('Profiles')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('outstanding_balance')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_type')
                    ->options([
                        'regular' => 'Regular',
                        'vip' => 'VIP',
                        'bridal' => 'Bridal',
                        'wholesale' => 'Wholesale',
                    ]),
                Tables\Filters\SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'child' => 'Child',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'index' => CustomerResource\Pages\ListCustomers::route('/'),
            'create' => CustomerResource\Pages\CreateCustomer::route('/create'),
            'edit' => CustomerResource\Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
