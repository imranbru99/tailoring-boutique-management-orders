<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\DesignCatalog;
use App\Services\AiAssistantService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DesignCatalogResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = DesignCatalog::class;
    protected static string $moduleKey = 'designs';
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Tailoring & Design';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Design Profile')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('e.g. Royal Embroidered Sherwani, Classic Peak Lapel Tuxedo'),
                        Forms\Components\Select::make('garment_type_id')
                            ->relationship('garmentType', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(50)
                            ->placeholder('e.g. DSN-SHER-009'),
                        Forms\Components\TextInput::make('collection_name')
                            ->placeholder('e.g. Festive Eid 2026, Royal Bridal Couture'),
                        Forms\Components\TextInput::make('additional_price')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\Toggle::make('is_featured')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Artisan Design Elements')
                    ->schema([
                        Forms\Components\TextInput::make('neck_design')
                            ->placeholder('e.g. Mandarin Band, Deep V-Neck, Sweetheart'),
                        Forms\Components\TextInput::make('collar_design')
                            ->placeholder('e.g. Peak Lapel, Shawl Collar, Cutaway Collar'),
                        Forms\Components\TextInput::make('sleeve_design')
                            ->placeholder('e.g. French Cuff, Bell Sleeve, Bishop Sleeve'),
                        Forms\Components\TextInput::make('pocket_design')
                            ->placeholder('e.g. Jetted Pocket, Flap Pocket, Ticket Pocket'),
                        Forms\Components\TextInput::make('embroidery_design')
                            ->placeholder('e.g. Zari Gold Threadwork, Thread Monogramming, Resham Work')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('stitching_instructions')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Creative Marketing Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('garmentType.name')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('collection_name')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('additional_price')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generate_ai_desc')
                    ->label('AI Polish')
                    ->icon('heroicon-o-cpu-chip')
                    ->color('purple')
                    ->action(function (DesignCatalog $record, AiAssistantService $ai) {
                        $desc = $ai->generateGarmentDescription(
                            $record->title,
                            $record->garmentType?->name ?? 'Garment',
                            'Artisanal Silk & Fine Suiting Wool',
                            'Midnight Navy'
                        );
                        $record->update(['description' => $desc]);
                        Notification::make()
                            ->title('AI Description Generated')
                            ->body('Creative description updated and saved in draft review mode.')
                            ->success()
                            ->send();
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
            'index' => DesignCatalogResource\Pages\ListDesignCatalogs::route('/'),
            'create' => DesignCatalogResource\Pages\CreateDesignCatalog::route('/create'),
            'edit' => DesignCatalogResource\Pages\EditDesignCatalog::route('/{record}/edit'),
        ];
    }
}
