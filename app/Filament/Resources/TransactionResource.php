<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('Daily Balance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Transactions');
    }

    public static function getModelLabel(): string
    {
        return __('Transaction');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Transactions');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label(__('Amount'))
                    ->required()
                    ->numeric()
                    ->prefix('AFN'),
                Forms\Components\Select::make('type')
                    ->label(__('Type'))
                    ->options([
                        'income' => __('Income'),
                        'expense' => __('Expense'),
                        'transfer_out' => __('Transfer Out'),
                        'transfer_in' => __('Transfer In'),
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\DatePicker::make('occurred_on')
                    ->label(__('Date'))
                    ->jalali()
                    ->locale('fa')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->label(__('Description'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('daily_transfer_id')
                    ->label(__('Daily Transfer'))
                    ->relationship('dailyTransfer', 'reference')
                    ->searchable()
                    ->preload()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('occurred_on')
                    ->label(__('Date'))
                    ->formatStateUsing(fn ($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('AFN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('Type'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'income' => __('Income'),
                        'expense' => __('Expense'),
                        'transfer_out' => __('Transfer Out'),
                        'transfer_in' => __('Transfer In'),
                        default => (string) $state,
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'income', 'transfer_in' => 'success',
                        'expense', 'transfer_out' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('source_type')
                    ->label(__('Source'))
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        \App\Models\Income::class => __('Income'),
                        \App\Models\Expense::class => __('Expense'),
                        \App\Models\ParentInvoicePayment::class => __('Invoice Payment'),
                        default => $state ? class_basename($state) : __('Manual'),
                    }),
                Tables\Columns\TextColumn::make('dailyTransfer.reference')
                    ->label(__('Transfer Reference'))
                    ->placeholder(__('Pending')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->jalaliDateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options([
                        'income' => __('Income'),
                        'expense' => __('Expense'),
                        'transfer_out' => __('Transfer Out'),
                        'transfer_in' => __('Transfer In'),
                    ]),
                Tables\Filters\SelectFilter::make('daily_transfer_id')
                    ->label(__('Daily Transfer'))
                    ->relationship('dailyTransfer', 'reference'),
                Tables\Filters\Filter::make('pending')
                    ->label(__('Pending Transfer'))
                    ->query(fn (Builder $query) => $query->whereNull('daily_transfer_id')->whereIn('type', ['income', 'expense'])),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
