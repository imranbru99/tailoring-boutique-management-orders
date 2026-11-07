<?php

namespace App\Filament\Customer\Resources;

use App\Models\Customer;
use App\Models\MeasurementProfile;
use App\Services\UnicodePdfService;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyMeasurementResource extends Resource
{
    protected static ?string $model = MeasurementProfile::class;
    protected static ?string $navigationIcon = 'heroicon-o-variable';
    protected static ?string $navigationLabel = 'My Measurement Cards';
    protected static ?string $title = 'My Measurements';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $customer = Customer::where('user_id', $user?->id)->orWhere('email', $user?->email)->first();
        $customerId = $customer?->id ?? 0;

        return parent::getEloquentQuery()->where('customer_id', $customerId);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('profile_name')
                    ->weight('bold')
                    ->searchable(),
                Tables\Columns\TextColumn::make('template.name')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('unit')
                    ->badge(),
                Tables\Columns\TextColumn::make('version')
                    ->label('Revision #')
                    ->badge(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->date('M d, Y')
                    ->label('Last Measured'),
            ])
            ->actions([
                Tables\Actions\Action::make('download_card')
                    ->label('Download PDF Card')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->action(function (MeasurementProfile $record, UnicodePdfService $pdfService) {
                        return response()->streamDownload(function () use ($record, $pdfService) {
                            echo $pdfService->generateMeasurementSheetPdf($record->customer, $record)->render();
                        }, "MyMeasurements-{$record->profile_name}.pdf");
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => MyMeasurementResource\Pages\ListMyMeasurements::route('/'),
        ];
    }
}
