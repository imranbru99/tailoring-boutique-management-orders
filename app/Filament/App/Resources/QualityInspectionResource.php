<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\QualityInspection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QualityInspectionResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = QualityInspection::class;
    protected static string $moduleKey = 'quality_control';
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Orders & Production';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Inspection Overview')
                    ->schema([
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('inspector_id')
                            ->relationship('inspector', 'name')
                            ->searchable()
                            ->default(fn () => auth()->id())
                            ->required(),
                        Forms\Components\DatePicker::make('inspection_date')
                            ->default(now()->toDateString())
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'passed' => 'Passed (Zero Defects)',
                                'rework_required' => 'Rework Required (Minor Corrections)',
                                'failed' => 'Failed Quality Audit',
                            ])
                            ->default('passed')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Quality Checklist Audit')
                    ->schema([
                        Forms\Components\KeyValue::make('checklist_results')
                            ->keyLabel('Checklist Point (e.g. Stitching Tension, Measurement Accuracy, Hemming, Lining Fit)')
                            ->valueLabel('Audit Result (Pass / Variance Notes / Fail)')
                            ->default([
                                'Stitching Quality' => 'Passed - Clean even tension',
                                'Measurement Tolerance' => 'Passed - Within 0.25 inch limit',
                                'Fabric Flaw Check' => 'Passed - No snags or discoloration',
                                'Finishing & Pressing' => 'Passed - Creases crisp, buttons secure',
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('defect_notes')
                            ->placeholder('Describe any variance, pulled threads, or seam alignment issues')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('action_taken')
                            ->placeholder('Remedial actions routed to workshop bench')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.order_number')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('inspector.name')
                    ->label('Inspector')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'passed',
                        'warning' => 'rework_required',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('inspection_date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'passed' => 'Passed',
                        'rework_required' => 'Rework Required',
                        'failed' => 'Failed',
                    ]),
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
            'index' => QualityInspectionResource\Pages\ListQualityInspections::route('/'),
            'create' => QualityInspectionResource\Pages\CreateQualityInspection::route('/create'),
            'edit' => QualityInspectionResource\Pages\EditQualityInspection::route('/{record}/edit'),
        ];
    }
}
