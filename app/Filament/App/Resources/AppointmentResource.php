<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = Appointment::class;
    protected static string $moduleKey = 'appointments';
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Clients & Profiles';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Appointment Details')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'first_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->full_name} ({$record->customer_code})")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('appointment_type')
                            ->options([
                                'measurement' => 'Body Measurement Session',
                                'design_consultation' => 'Design & Fabric Consultation',
                                'fitting' => 'First Fitting Trial',
                                'final_fitting' => 'Final Fitting & Approval',
                                'alteration' => 'Alteration Review',
                                'styling' => 'Wardrobe Styling',
                            ])
                            ->default('measurement')
                            ->required(),
                        Forms\Components\DatePicker::make('appointment_date')
                            ->default(now()->toDateString())
                            ->required(),
                        Forms\Components\TimePicker::make('start_time')
                            ->default('10:00:00')
                            ->required(),
                        Forms\Components\TimePicker::make('end_time')
                            ->default('11:00:00')
                            ->required(),
                        Forms\Components\Select::make('assigned_staff_id')
                            ->relationship('staff', 'name')
                            ->label('Master Tailor / Stylist')
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'scheduled' => 'Scheduled',
                                'confirmed' => 'Confirmed by Client',
                                'in_progress' => 'In Consultation',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                                'no_show' => 'Client No-Show',
                            ])
                            ->default('scheduled')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->placeholder('Special client requests, tea/coffee preference, bridal entourage notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.full_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('appointment_type')
                    ->colors([
                        'primary' => 'measurement',
                        'warning' => 'design_consultation',
                        'info' => 'fitting',
                        'success' => 'final_fitting',
                        'danger' => 'alteration',
                    ]),
                Tables\Columns\TextColumn::make('appointment_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Time Slot')
                    ->formatStateUsing(fn ($state, $record) => substr($state, 0, 5) . ' - ' . substr($record->end_time, 0, 5)),
                Tables\Columns\TextColumn::make('staff.name')
                    ->label('Artisan')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'scheduled',
                        'info' => 'confirmed',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                        'danger' => fn ($state) => in_array($state, ['cancelled', 'no_show']),
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('appointment_type')
                    ->options([
                        'measurement' => 'Measurement',
                        'fitting' => 'Fitting',
                        'design_consultation' => 'Design',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'scheduled' => 'Scheduled',
                        'confirmed' => 'Confirmed',
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
            'index' => AppointmentResource\Pages\ListAppointments::route('/'),
            'create' => AppointmentResource\Pages\CreateAppointment::route('/create'),
            'edit' => AppointmentResource\Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
