<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = User::class;
    protected static string $moduleKey = 'employees';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Human Resources';
    protected static ?string $navigationLabel = 'Tailors & Staff';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', '!=', 'customer');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Artisan & Employee Profile')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\Select::make('role')
                            ->options([
                                'master_tailor' => 'Master Tailor',
                                'tailor' => 'Tailor / Stitcher',
                                'cutter' => 'Master Cutter & Pattern Maker',
                                'fashion_designer' => 'Fashion Designer / Stylist',
                                'embroidery_specialist' => 'Embroidery Specialist',
                                'finishing_worker' => 'Finishing & Pressing Worker',
                                'quality_inspector' => 'Quality Control Inspector',
                                'general_manager' => 'General Manager',
                                'branch_manager' => 'Branch Manager',
                                'receptionist' => 'Front Desk Receptionist',
                                'inventory_manager' => 'Inventory Manager',
                                'accountant' => 'Financial Accountant',
                                'delivery_staff' => 'Delivery Staff',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('job_title')
                            ->placeholder('e.g. Senior Couture Tailor'),
                        Forms\Components\Select::make('current_branch_id')
                            ->relationship('currentBranch', 'name')
                            ->label('Assigned Atelier Branch')
                            ->searchable(),
                        Forms\Components\TextInput::make('hourly_rate')
                            ->numeric()
                            ->prefix('$')
                            ->default(25.00),
                        Forms\Components\TextInput::make('commission_rate')
                            ->numeric()
                            ->suffix('%')
                            ->default(5.00),
                        Forms\Components\TagsInput::make('skills')
                            ->placeholder('Add artisan specializations (e.g. Tuxedos, Sherwanis, Zari Work)'),
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
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'primary' => fn ($state) => in_array($state, ['master_tailor', 'cutter']),
                        'warning' => fn ($state) => in_array($state, ['tailor', 'embroidery_specialist', 'finishing_worker']),
                        'info' => fn ($state) => in_array($state, ['fashion_designer', 'quality_inspector']),
                        'success' => fn ($state) => in_array($state, ['general_manager', 'branch_manager']),
                    ]),
                Tables\Columns\TextColumn::make('currentBranch.name')
                    ->label('Branch')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('assigned_job_cards_count')
                    ->counts('assignedJobCards')
                    ->label('Active Jobs')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('hourly_rate')
                    ->money('USD')
                    ->label('Rate/Hr'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'master_tailor' => 'Master Tailor',
                        'tailor' => 'Tailor',
                        'cutter' => 'Cutter',
                        'fashion_designer' => 'Designer',
                        'branch_manager' => 'Branch Manager',
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
            'index' => EmployeeResource\Pages\ListEmployees::route('/'),
            'create' => EmployeeResource\Pages\CreateEmployee::route('/create'),
            'edit' => EmployeeResource\Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
