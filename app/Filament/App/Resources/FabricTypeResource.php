<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\FabricType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FabricTypeResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = FabricType::class;
    protected static string $moduleKey = 'fabrics';
    protected static ?string $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Materials & Stock';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Fabric Profile')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. Pure Egyptian Giza Cotton, Italian Super 150s Wool, Banarasi Silk'),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('e.g. FAB-EGY-001'),
                        Forms\Components\Select::make('material_type')
                            ->options([
                                'cotton' => '100% Cotton',
                                'silk' => 'Silk / Raw Silk',
                                'wool' => 'Fine Suiting Wool / Cashmere',
                                'linen' => 'Pure Linen',
                                'velvet' => 'Velvet',
                                'chiffon' => 'Chiffon & Georgette',
                                'denim' => 'Denim & Twill',
                                'blended' => 'Polyester Blend',
                            ])
                            ->default('cotton')
                            ->required(),
                        Forms\Components\Select::make('unit')
                            ->options([
                                'meter' => 'Meters (m)',
                                'yard' => 'Yards (yd)',
                                'piece' => 'Pieces / Thaan',
                                'kg' => 'Kilograms (kg)',
                            ])
                            ->default('meter')
                            ->required(),
                        Forms\Components\TextInput::make('color')
                            ->placeholder('e.g. Royal Blue, Ivory, Charcoal'),
                        Forms\Components\TextInput::make('pattern')
                            ->placeholder('e.g. Solid, Pinstripe, Houndstooth, Floral Jaal'),
                        Forms\Components\TextInput::make('width')
                            ->placeholder('e.g. 44 inches, 58 inches'),
                        Forms\Components\TextInput::make('storage_location')
                            ->placeholder('e.g. Atelier Rack B-4, Roll Shelf 2'),
                    ])->columns(2),

                Forms\Components\Section::make('Valuation & Stock Thresholds')
                    ->schema([
                        Forms\Components\TextInput::make('cost_per_unit')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(15.00),
                        Forms\Components\TextInput::make('selling_price_per_unit')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->default(35.00),
                        Forms\Components\TextInput::make('current_stock')
                            ->numeric()
                            ->required()
                            ->default(50.00),
                        Forms\Components\TextInput::make('reorder_level')
                            ->numeric()
                            ->required()
                            ->default(10.00),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
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
                Tables\Columns\TextColumn::make('code')
                    ->fontFamily('mono')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('material_type')
                    ->colors([
                        'primary' => 'cotton',
                        'warning' => 'silk',
                        'danger' => 'wool',
                        'success' => 'linen',
                    ]),
                Tables\Columns\TextColumn::make('current_stock')
                    ->label('In Stock')
                    ->formatStateUsing(fn ($state, $record) => "{$state} {$record->unit}")
                    ->weight('bold')
                    ->color(fn ($record) => $record->current_stock <= $record->reorder_level ? 'danger' : 'success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('selling_price_per_unit')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->label('Price / Unit'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('material_type')
                    ->options([
                        'cotton' => 'Cotton',
                        'silk' => 'Silk',
                        'wool' => 'Wool',
                        'linen' => 'Linen',
                    ]),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock Warning')
                    ->query(fn ($query) => $query->whereColumn('current_stock', '<=', 'reorder_level')),
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
            'index' => FabricTypeResource\Pages\ListFabricTypes::route('/'),
            'create' => FabricTypeResource\Pages\CreateFabricType::route('/create'),
            'edit' => FabricTypeResource\Pages\EditFabricType::route('/{record}/edit'),
        ];
    }
}
