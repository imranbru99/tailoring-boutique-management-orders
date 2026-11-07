<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Concerns\HasModuleGate;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    use HasModuleGate;

    protected static ?string $model = Expense::class;
    protected static string $moduleKey = 'accounting';
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Sales & Finance';
    protected static ?string $navigationLabel = 'Expenses & Cashbook';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Expense Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('e.g. Workshop Thread Restock, Atelier Electricity Bill, Tailor Tea & Refreshments'),
                        Forms\Components\Select::make('expense_category_id')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\DatePicker::make('expense_date')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('payment_method')
                            ->options([
                                'cash' => 'Cash Petty Drawer',
                                'card' => 'Business Card',
                                'bank_transfer' => 'Bank Wire',
                                'bkash' => 'bKash / Mobile',
                            ])
                            ->default('cash')
                            ->required(),
                        Forms\Components\TextInput::make('reference_number')
                            ->placeholder('Voucher #, Receipt Ref, Cheque No'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('amount')
                    ->money(fn ($record) => $record->tenant?->currency ?? 'USD')
                    ->sortable()
                    ->weight('bold')
                    ->color('danger'),
                Tables\Columns\BadgeColumn::make('payment_method')
                    ->colors([
                        'primary' => 'cash',
                        'info' => 'card',
                        'success' => 'bank_transfer',
                    ]),
                Tables\Columns\TextColumn::make('expense_date')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('expense_category_id')
                    ->relationship('category', 'name'),
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
            'index' => ExpenseResource\Pages\ListExpenses::route('/'),
            'create' => ExpenseResource\Pages\CreateExpense::route('/create'),
            'edit' => ExpenseResource\Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
