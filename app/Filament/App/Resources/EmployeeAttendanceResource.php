<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\EmployeeAttendance;
use App\Models\User;
use App\Services\AttendanceHubService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeAttendanceResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = EmployeeAttendance::class;
    protected static string $moduleKey = 'attendance';
    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $navigationGroup = 'Human Resources';
    protected static ?string $navigationLabel = 'Staff Attendance';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance Record')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name', fn ($q) => $q->where('role', '!=', 'customer'))
                            ->label('Employee / Artisan')
                            ->searchable()
                            ->required(),
                        Forms\Components\DatePicker::make('attendance_date')
                            ->default(now()->toDateString())
                            ->required(),
                        Forms\Components\TimePicker::make('check_in')
                            ->default('09:00:00'),
                        Forms\Components\TimePicker::make('check_out')
                            ->default('18:00:00'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'present' => 'Present on Time',
                                'late' => 'Late Arrival',
                                'half_day' => 'Half Day',
                                'absent' => 'Absent',
                                'leave' => 'Authorized Leave',
                                'holiday' => 'Public Holiday',
                            ])
                            ->default('present')
                            ->required(),
                        Forms\Components\TextInput::make('working_hours')
                            ->numeric()
                            ->suffix('hrs')
                            ->default(8.0),
                        Forms\Components\TextInput::make('overtime_hours')
                            ->numeric()
                            ->suffix('hrs')
                            ->default(0.0),
                        Forms\Components\Select::make('source')
                            ->options([
                                'manual' => 'Manual Entry',
                                'biometric_device' => 'Biometric Fingerprint Terminal',
                                'facial_terminal' => 'Facial Recognition Scanner',
                                'mobile_app' => 'Mobile App Geo-Punch',
                            ])
                            ->default('manual'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('user.job_title')
                    ->label('Designation')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'present',
                        'warning' => 'late',
                        'info' => 'half_day',
                        'danger' => 'absent',
                    ]),
                Tables\Columns\TextColumn::make('check_in')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('check_out')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('working_hours')
                    ->suffix('h')
                    ->sortable(),
                Tables\Columns\TextColumn::make('overtime_hours')
                    ->suffix('h')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('attendance_date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present' => 'Present',
                        'late' => 'Late',
                        'half_day' => 'Half Day',
                        'absent' => 'Absent',
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
            'index' => EmployeeAttendanceResource\Pages\ListEmployeeAttendances::route('/'),
            'create' => EmployeeAttendanceResource\Pages\CreateEmployeeAttendance::route('/create'),
            'edit' => EmployeeAttendanceResource\Pages\EditEmployeeAttendance::route('/{record}/edit'),
        ];
    }
}
