<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\Alteration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlterationResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = Alteration::class;
    protected static string $moduleKey = 'alterations';
    protected static ?string $navigationIcon = 'heroicon-o-scissors';
    protected static ?string $navigationGroup = 'Orders & Production';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Alteration Specifications')
                    ->schema([
                        Forms\Components\TextInput::make('alteration_number')
                            ->default(fn () => 'ALT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)))
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('assigned_tailor_id')
                            ->relationship('assignedTailor', 'name')
                            ->label('Assigned Alteration Tailor')
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Rework',
                                'in_progress' => 'At Bench (In Progress)',
                                'completed' => 'Completed & Re-inspected',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\TextInput::make('additional_charge')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\DatePicker::make('expected_completion_date')
                            ->default(now()->addDays(3)),
                        Forms\Components\Textarea::make('reason')
                            ->required()
                            ->placeholder('e.g. Waist taking in 1 inch, sleeve shortening 0.5 inch')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('adjustment_notes')
                            ->placeholder('Specific chalk marks or pin measurements from trial')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('alteration_number')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->searchable()
                    ->label('Order #')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('reason')
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('assignedTailor.name')
                    ->label('Tailor')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'in_progress',
                        'success' => 'completed',
                    ]),
                Tables\Columns\TextColumn::make('additional_charge')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD'),
                Tables\Columns\TextColumn::make('expected_completion_date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
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
            'index' => AlterationResource\Pages\ListAlterations::route('/'),
            'create' => AlterationResource\Pages\CreateAlteration::route('/create'),
            'edit' => AlterationResource\Pages\EditAlteration::route('/{record}/edit'),
        ];
    }
}
