<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\MeasurementProfile;
use App\Models\MeasurementTemplate;
use App\Models\MeasurementVersion;
use App\Services\UnicodePdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MeasurementProfileResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = MeasurementProfile::class;
    protected static string $moduleKey = 'measurements';
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Clients & Profiles';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Metadata')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->full_name} ({$record->customer_code})")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('template_id')
                            ->relationship('template', 'name')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->required(),
                        Forms\Components\TextInput::make('profile_name')
                            ->required()
                            ->placeholder('e.g. Slim Fit Tuxedo, Everyday Cotton Shirt, Wedding Sherwani'),
                        Forms\Components\Select::make('unit')
                            ->options([
                                'inch' => 'Inches (in)',
                                'cm' => 'Centimeters (cm)',
                            ])
                            ->default('inch')
                            ->required(),
                        Forms\Components\Select::make('measured_by')
                            ->relationship('measuredBy', 'name')
                            ->label('Measured By Tailor')
                            ->searchable(),
                        Forms\Components\Toggle::make('is_default')
                            ->label('Mark as primary profile for client')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('Anatomical Measurements')
                    ->description('Key-value measurement entries matching the garment pattern.')
                    ->schema([
                        Forms\Components\KeyValue::make('values')
                            ->keyLabel('Measurement Point (e.g. Chest, Waist, Inseam, Neck)')
                            ->valueLabel('Value (e.g. 38.5)')
                            ->reorderable()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('fitting_notes')
                            ->placeholder('Slope shoulder, forward neck, arch back, erect posture notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('profile_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('template.name')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('unit')
                    ->badge(),
                Tables\Columns\TextColumn::make('version')
                    ->label('Revision')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('download_pdf')
                    ->label('Print Sheet')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->action(function (MeasurementProfile $record, UnicodePdfService $pdfService) {
                        return response()->streamDownload(function () use ($record, $pdfService) {
                            echo $pdfService->generateMeasurementSheetPdf($record->customer, $record)->render();
                        }, "Measurement-{$record->customer->customer_code}-{$record->profile_name}.pdf");
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
            'index' => MeasurementProfileResource\Pages\ListMeasurementProfiles::route('/'),
            'create' => MeasurementProfileResource\Pages\CreateMeasurementProfile::route('/create'),
            'edit' => MeasurementProfileResource\Pages\EditMeasurementProfile::route('/{record}/edit'),
        ];
    }
}
