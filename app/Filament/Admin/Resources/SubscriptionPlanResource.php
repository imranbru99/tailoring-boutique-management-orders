<?php

namespace App\Filament\Admin\Resources;

use App\Models\SubscriptionPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionPlanResource extends Resource
{
    protected static ?string $model = SubscriptionPlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'SaaS Plans & Billing';
    protected static ?string $navigationLabel = 'Subscription Plans';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('SaaS Tier Definition')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. Starter Atelier, Pro Fashion Studio, Enterprise Couture'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),
                        Forms\Components\TextInput::make('price_monthly')
                            ->numeric()
                            ->prefix('$')
                            ->default(29.00)
                            ->required(),
                        Forms\Components\TextInput::make('price_yearly')
                            ->numeric()
                            ->prefix('$')
                            ->default(290.00)
                            ->required(),
                        Forms\Components\TextInput::make('max_branches')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        Forms\Components\TextInput::make('max_staff')
                            ->numeric()
                            ->default(5)
                            ->required(),
                        Forms\Components\TextInput::make('max_orders_per_month')
                            ->numeric()
                            ->default(150)
                            ->required(),
                        Forms\Components\TextInput::make('max_storage_mb')
                            ->numeric()
                            ->suffix('MB')
                            ->default(1024)
                            ->required(),
                        Forms\Components\Toggle::make('is_featured')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('price_monthly')
                    ->money('USD')
                    ->label('Monthly'),
                Tables\Columns\TextColumn::make('price_yearly')
                    ->money('USD')
                    ->label('Yearly'),
                Tables\Columns\TextColumn::make('max_branches')
                    ->label('Branches')
                    ->badge(),
                Tables\Columns\TextColumn::make('max_staff')
                    ->label('Staff Limit')
                    ->badge(),
                Tables\Columns\TextColumn::make('max_orders_per_month')
                    ->label('Orders/Mo')
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
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
            'index' => SubscriptionPlanResource\Pages\ListSubscriptionPlans::route('/'),
            'create' => SubscriptionPlanResource\Pages\CreateSubscriptionPlan::route('/create'),
            'edit' => SubscriptionPlanResource\Pages\EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
