<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\MarketingCampaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MarketingCampaignResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = MarketingCampaign::class;
    protected static string $moduleKey = 'marketing';
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Marketing & CRM';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Campaign Broadcast')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. Eid Festive Couture Preview, Bridal Trunk Show SMS'),
                        Forms\Components\Select::make('channel')
                            ->options([
                                'sms' => 'SMS Gateway',
                                'email' => 'Email Newsletter',
                                'whatsapp' => 'WhatsApp Business API',
                            ])
                            ->default('sms')
                            ->required(),
                        Forms\Components\Select::make('audience_type')
                            ->options([
                                'all_customers' => 'All Registered Clients',
                                'vip' => 'VIP Couture Members Only',
                                'bridal' => 'Bridal Clients',
                                'inactive' => 'Inactive (No Orders in 6 Months)',
                            ])
                            ->default('all_customers')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'sent' => 'Broadcast Sent',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->default(now()),
                        Forms\Components\TextInput::make('recipients_count')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->placeholder('Draft your broadcast message. Use {customer_name} for personalization.')
                            ->rows(4)
                            ->columnSpanFull(),
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
                Tables\Columns\BadgeColumn::make('channel')
                    ->colors([
                        'primary' => 'sms',
                        'warning' => 'email',
                        'success' => 'whatsapp',
                    ]),
                Tables\Columns\BadgeColumn::make('audience_type')
                    ->colors([
                        'gray' => 'all_customers',
                        'warning' => 'vip',
                        'danger' => 'bridal',
                    ]),
                Tables\Columns\TextColumn::make('recipients_count')
                    ->label('Recipients')
                    ->badge(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'scheduled',
                        'success' => 'sent',
                    ]),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send_campaign')
                    ->label('Broadcast Now')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (MarketingCampaign $record) {
                        $record->update([
                            'status' => 'sent',
                            'sent_at' => now(),
                            'recipients_count' => max(1, $record->recipients_count),
                        ]);
                        Notification::make()
                            ->title('Campaign Broadcasted')
                            ->body("Campaign [{$record->name}] sent successfully.")
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
            'index' => MarketingCampaignResource\Pages\ListMarketingCampaigns::route('/'),
            'create' => MarketingCampaignResource\Pages\CreateMarketingCampaign::route('/create'),
            'edit' => MarketingCampaignResource\Pages\EditMarketingCampaign::route('/{record}/edit'),
        ];
    }
}
