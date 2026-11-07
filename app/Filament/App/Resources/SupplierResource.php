<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = Supplier::class;
    protected static string $moduleKey = 'suppliers';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Materials & Stock';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Supplier / Mill Profile')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('e.g. Scabal Fabric Mills, Raymond Luxury Cottons, YKK Zippers Corp'),
                        Forms\Components\TextInput::make('code')
                            ->maxLength(50)
                            ->placeholder('e.g. SUP-SCB-01'),
                        Forms\Components\TextInput::make('contact_person')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('tax_id')
                            ->placeholder('Tax ID / VAT #'),
                        Forms\Components\TextInput::make('outstanding_balance')
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\Textarea::make('address')
                            ->rows(2)
                            ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('outstanding_balance')
                    ->label('Payable Balance')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'gray'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => SupplierResource\Pages\ListSuppliers::route('/'),
            'create' => SupplierResource\Pages\CreateSupplier::route('/create'),
            'edit' => SupplierResource\Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
