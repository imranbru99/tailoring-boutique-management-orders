<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\Coupon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = Coupon::class;
    protected static string $moduleKey = 'marketing';
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Marketing & CRM';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Promotional Coupon')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('e.g. SUMMER10, WEDDING50, VIPCOUTURE'),
                        Forms\Components\Select::make('discount_type')
                            ->options([
                                'percentage' => 'Percentage (%)',
                                'fixed' => 'Fixed Amount ($)',
                            ])
                            ->default('percentage')
                            ->required(),
                        Forms\Components\TextInput::make('discount_value')
                            ->numeric()
                            ->required()
                            ->default(10.00),
                        Forms\Components\TextInput::make('min_spend')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\TextInput::make('max_discount')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('Leave blank for uncapped'),
                        Forms\Components\TextInput::make('usage_limit')
                            ->numeric()
                            ->placeholder('Max total redemptions'),
                        Forms\Components\DatePicker::make('starts_at'),
                        Forms\Components\DatePicker::make('expires_at'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('discount_type')
                    ->colors([
                        'primary' => 'percentage',
                        'success' => 'fixed',
                    ]),
                Tables\Columns\TextColumn::make('discount_value')
                    ->formatStateUsing(fn ($state, $record) => $record->discount_type === 'percentage' ? "{$state}%" : "\${$state}")
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('used_count')
                    ->label('Redeemed')
                    ->badge(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => CouponResource\Pages\ListCoupons::route('/'),
            'create' => CouponResource\Pages\CreateCoupon::route('/create'),
            'edit' => CouponResource\Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}
