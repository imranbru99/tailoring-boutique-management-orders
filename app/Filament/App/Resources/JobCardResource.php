<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\JobCard;
use App\Services\UnicodePdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JobCardResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = JobCard::class;
    protected static string $moduleKey = 'production';
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationGroup = 'Orders & Production';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Production Job Card')
                    ->schema([
                        Forms\Components\TextInput::make('job_number')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\Select::make('order_id')
                            ->relationship('order', 'order_number')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('stage')
                            ->options([
                                'cutting' => 'Cutting Workshop',
                                'stitching' => 'Stitching & Assembly',
                                'embroidery' => 'Embroidery Studio',
                                'finishing' => 'Hand Finishing & Pressing',
                            ])
                            ->default('cutting')
                            ->required(),
                        Forms\Components\Select::make('assigned_to')
                            ->relationship('assignedTailor', 'name')
                            ->label('Assigned Artisan / Tailor')
                            ->searchable(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Queue',
                                'in_progress' => 'In Progress at Bench',
                                'completed' => 'Completed',
                                'paused' => 'Paused / Awaiting Materials',
                                'rework' => 'Rework Requested',
                            ])
                            ->default('pending')
                            ->required(),
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'normal' => 'Normal',
                                'high' => 'High Priority',
                                'urgent' => 'Rush VIP',
                            ])
                            ->default('normal'),
                        Forms\Components\TextInput::make('estimated_hours')
                            ->numeric()
                            ->suffix('hrs')
                            ->default(2.0),
                        Forms\Components\TextInput::make('actual_hours')
                            ->numeric()
                            ->suffix('hrs')
                            ->default(0.0),
                        Forms\Components\DateTimePicker::make('started_at'),
                        Forms\Components\DateTimePicker::make('completed_at'),
                        Forms\Components\Textarea::make('instructions')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('tailor_notes')
                            ->placeholder('Artisan feedback or notes from workshop floor')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('job_number')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->searchable()
                    ->label('Order #')
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('stage')
                    ->colors([
                        'primary' => 'cutting',
                        'warning' => 'stitching',
                        'info' => 'embroidery',
                        'success' => 'finishing',
                    ]),
                Tables\Columns\TextColumn::make('assignedTailor.name')
                    ->label('Artisan')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'pending',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                        'danger' => 'rework',
                    ]),
                Tables\Columns\TextColumn::make('estimated_hours')
                    ->suffix('h')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'gray' => 'low',
                        'info' => 'normal',
                        'warning' => 'high',
                        'danger' => 'urgent',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('stage')
                    ->options([
                        'cutting' => 'Cutting',
                        'stitching' => 'Stitching',
                        'embroidery' => 'Embroidery',
                        'finishing' => 'Finishing',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print_job_card')
                    ->label('Bench PDF')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->action(function (JobCard $record, UnicodePdfService $pdfService) {
                        return response()->streamDownload(function () use ($record, $pdfService) {
                            echo $pdfService->generateJobCardPdf($record)->render();
                        }, "JobCard-{$record->job_number}.pdf");
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
            'index' => JobCardResource\Pages\ListJobCards::route('/'),
            'create' => JobCardResource\Pages\CreateJobCard::route('/create'),
            'edit' => JobCardResource\Pages\EditJobCard::route('/{record}/edit'),
        ];
    }
}
