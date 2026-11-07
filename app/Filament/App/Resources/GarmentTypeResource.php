<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\GarmentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GarmentTypeResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = GarmentType::class;
    protected static string $moduleKey = 'designs';
    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    protected static ?string $navigationGroup = 'Tailoring & Design';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Garment Archetype')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. 2-Piece Suit, Sherwani, Blouse, Kurta, Trousers'),
                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('measurement_template_id')
                            ->relationship('measurementTemplate', 'name')
                            ->label('Associated Measurement Template')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'men' => 'Men',
                                'women' => 'Women',
                                'kids' => 'Kids',
                                'unisex' => 'Unisex',
                            ])
                            ->default('men')
                            ->required(),
                        Forms\Components\TextInput::make('base_price')
                            ->numeric()
                            ->prefix(fn ($record) => $record?->tenant?->currency_symbol ?? '$')
                            ->default(50.00)
                            ->required(),
                        Forms\Components\TextInput::make('estimated_days')
                            ->numeric()
                            ->suffix('days')
                            ->default(7)
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Customization Options & Styling Rules')
                    ->schema([
                        Forms\Components\KeyValue::make('customization_options')
                            ->keyLabel('Style Feature (e.g. Collar Type, Cuff Style, Pocket Style, Vent)')
                            ->valueLabel('Permitted Options (comma separated: e.g. Spread, Mandarin, Cutaway)')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
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
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color('info'),
                Tables\Columns\BadgeColumn::make('gender')
                    ->colors([
                        'info' => 'men',
                        'danger' => 'women',
                        'warning' => 'kids',
                        'success' => 'unisex',
                    ]),
                Tables\Columns\TextColumn::make('base_price')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimated_days')
                    ->suffix(' days')
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
            'index' => GarmentTypeResource\Pages\ListGarmentTypes::route('/'),
            'create' => GarmentTypeResource\Pages\CreateGarmentType::route('/create'),
            'edit' => GarmentTypeResource\Pages\EditGarmentType::route('/{record}/edit'),
        ];
    }
}
