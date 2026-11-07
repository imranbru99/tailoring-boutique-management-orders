<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\MeasurementTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MeasurementTemplateResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = MeasurementTemplate::class;
    protected static string $moduleKey = 'measurements';
    protected static ?string $navigationIcon = 'heroicon-o-variable';
    protected static ?string $navigationGroup = 'Clients & Profiles';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Template Specifications')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. Mens Suit & Trouser, Womens Lehenga & Blouse'),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(50)
                            ->placeholder('e.g. TPL-SUIT-01'),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                                'children' => 'Children',
                                'all' => 'Universal',
                            ])
                            ->default('all')
                            ->required(),
                        Forms\Components\Select::make('unit')
                            ->options([
                                'inch' => 'Inches (in)',
                                'cm' => 'Centimeters (cm)',
                            ])
                            ->default('inch')
                            ->required(),
                        Forms\Components\TextInput::make('garment_category')
                            ->default('Formal Wear')
                            ->maxLength(100),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Measurement Fields Configuration')
                    ->description('Define the exact body parameters the tailor must measure for this garment archetype.')
                    ->schema([
                        Forms\Components\Repeater::make('fields')
                            ->schema([
                                Forms\Components\TextInput::make('key')
                                    ->required()
                                    ->placeholder('e.g. chest, neck, sleeve_length, waist, inseam')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('label')
                                    ->required()
                                    ->placeholder('e.g. Chest Circumference, Neck Line')
                                    ->columnSpan(2),
                                Forms\Components\Select::make('category')
                                    ->options([
                                        'upper_body' => 'Upper Body',
                                        'lower_body' => 'Lower Body',
                                        'arms_sleeves' => 'Arms & Sleeves',
                                        'neck_collar' => 'Neck & Collar',
                                        'posture' => 'Posture & Stance',
                                    ])
                                    ->default('upper_body')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('default_value')
                                    ->placeholder('e.g. 38.0')
                                    ->columnSpan(1),
                                Forms\Components\Toggle::make('is_required')
                                    ->default(true)
                                    ->columnSpan(1),
                            ])
                            ->columns(6)
                            ->defaultItems(4)
                            ->collapsible()
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
                Tables\Columns\BadgeColumn::make('gender')
                    ->colors([
                        'info' => 'male',
                        'danger' => 'female',
                        'warning' => 'children',
                        'success' => 'all',
                    ]),
                Tables\Columns\TextColumn::make('unit')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('profiles_count')
                    ->counts('profiles')
                    ->label('Client Profiles Recorded')
                    ->badge(),
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
            'index' => MeasurementTemplateResource\Pages\ListMeasurementTemplates::route('/'),
            'create' => MeasurementTemplateResource\Pages\CreateMeasurementTemplate::route('/create'),
            'edit' => MeasurementTemplateResource\Pages\EditMeasurementTemplate::route('/{record}/edit'),
        ];
    }
}
