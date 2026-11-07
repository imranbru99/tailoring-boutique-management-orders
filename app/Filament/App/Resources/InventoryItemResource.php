<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\InventoryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryItemResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = InventoryItem::class;
    protected static string $moduleKey = 'inventory';
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Materials & Stock';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Stock Item Profile')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. Horn Buttons 24L, YKK Invisible Zippers 9", Gutermann Silk Thread'),
                        Forms\Components\TextInput::make('sku')
                            ->maxLength(50)
                            ->placeholder('e.g. SK-BTN-01'),
                        Forms\Components\Select::make('category')
                            ->options([
                                'buttons' => 'Buttons & Fasteners',
                                'threads' => 'Sewing & Embroidery Threads',
                                'zippers' => 'Zippers & Sliders',
                                'lining' => 'Linings & Canvasses',
                                'interlining' => 'Interlinings & Collar Fusing',
                                'packaging' => 'Hangers & Garment Bags',
                                'accessories' => 'Embellishments & Trims',
                            ])
                            ->default('buttons')
                            ->required(),
                        Forms\Components\Select::make('unit')
                            ->options([
                                'piece' => 'Pieces (pcs)',
                                'spool' => 'Spools / Cones',
                                'gross' => 'Gross (144 pcs)',
                                'packet' => 'Packets',
                                'meter' => 'Meters (m)',
                            ])
                            ->default('piece')
                            ->required(),
                        Forms\Components\TextInput::make('cost_price')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.50),
                        Forms\Components\TextInput::make('current_stock')
                            ->numeric()
                            ->default(100),
                        Forms\Components\TextInput::make('reorder_level')
                            ->numeric()
                            ->default(20),
                        Forms\Components\TextInput::make('storage_location')
                            ->placeholder('e.g. Drawer C, Bin 12'),
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
                Tables\Columns\TextColumn::make('sku')
                    ->fontFamily('mono')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'primary' => 'buttons',
                        'warning' => 'threads',
                        'info' => 'zippers',
                        'success' => 'lining',
                    ]),
                Tables\Columns\TextColumn::make('current_stock')
                    ->formatStateUsing(fn ($state, $record) => "{$state} {$record->unit}")
                    ->weight('bold')
                    ->color(fn ($record) => $record->current_stock <= $record->reorder_level ? 'danger' : 'success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'buttons' => 'Buttons',
                        'threads' => 'Threads',
                        'zippers' => 'Zippers',
                        'lining' => 'Lining',
                    ]),
                Tables\Filters\Filter::make('low_stock')
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
            'index' => InventoryItemResource\Pages\ListInventoryItems::route('/'),
            'create' => InventoryItemResource\Pages\CreateInventoryItem::route('/create'),
            'edit' => InventoryItemResource\Pages\EditInventoryItem::route('/{record}/edit'),
        ];
    }
}
