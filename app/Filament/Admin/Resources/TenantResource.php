<?php

namespace App\Filament\Admin\Resources;

use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Business Organizations';
    protected static ?string $title = 'Tenants & Tailoring Businesses';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Organization Profile')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. Savile Row Couture Studio, Elegant Stitch Boutique'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),
                        Forms\Components\TextInput::make('domain')
                            ->placeholder('customdomain.com')
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('mode')
                            ->options([
                                'single_business' => 'Single Independent Atelier',
                                'multi_branch' => 'Multi-Branch Fashion House',
                                'saas' => 'Commercial SaaS Tenant',
                                'enterprise' => 'Enterprise Custom Apparel',
                                'demo' => 'Demonstration Sandbox',
                            ])
                            ->default('multi_branch')
                            ->required(),
                        Forms\Components\Select::make('business_type')
                            ->options([
                                'tailoring_shop' => 'Bespoke Tailoring Shop',
                                'boutique' => 'Fashion Boutique & RTW',
                                'fashion_studio' => 'Haute Couture Fashion Studio',
                                'alteration_shop' => 'Alteration & Fitting Specialist',
                                'bridal_studio' => 'Bridal Atelier',
                                'uniform_manufacturer' => 'Uniform & Workwear Manufacturer',
                            ])
                            ->default('boutique')
                            ->required(),
                        Forms\Components\TextInput::make('currency')
                            ->default('USD')
                            ->maxLength(3)
                            ->required(),
                        Forms\Components\TextInput::make('currency_symbol')
                            ->default('$')
                            ->maxLength(10)
                            ->required(),
                        Forms\Components\Select::make('measurement_unit')
                            ->options([
                                'inch' => 'Inches (in)',
                                'cm' => 'Centimeters (cm)',
                            ])
                            ->default('inch')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->email(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Platform Active Status')
                            ->default(true),
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
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
                Tables\Columns\TextColumn::make('slug')
                    ->fontFamily('mono')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('business_type')
                    ->colors(['primary']),
                Tables\Columns\BadgeColumn::make('mode')
                    ->colors([
                        'info' => 'single_business',
                        'warning' => 'multi_branch',
                        'success' => 'saas',
                        'danger' => 'enterprise',
                    ]),
                Tables\Columns\TextColumn::make('branches_count')
                    ->counts('branches')
                    ->label('Branches')
                    ->badge(),
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Staff')
                    ->badge(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Orders')
                    ->badge()
                    ->color('success'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('business_type')
                    ->options([
                        'tailoring_shop' => 'Tailoring Shop',
                        'boutique' => 'Boutique',
                        'fashion_studio' => 'Fashion Studio',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('access_workspace')
                    ->label('Open Workspace')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn (Tenant $record) => url("/app/{$record->slug}"))
                    ->openUrlInNewTab(),
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
            'index' => TenantResource\Pages\ListTenants::route('/'),
            'create' => TenantResource\Pages\CreateTenant::route('/create'),
            'edit' => TenantResource\Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
