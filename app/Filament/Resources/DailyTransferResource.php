<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyTransferResource\Pages;
use App\Filament\Resources\TransactionResource;
use App\Models\DailyTransfer;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DailyTransferResource extends Resource
{
    protected static ?string $model = DailyTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('Daily Balance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Daily Transfers');
    }

    public static function getModelLabel(): string
    {
        return __('Daily Transfer');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Daily Transfers');
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
                Forms\Components\Select::make('destination_user_id')
                    ->label(__('Destination Staff'))
                    ->options(
                        fn () => User::query()
                            ->where('type', 'staff')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('reference')
                    ->label(__('Reference'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('transfer_date')
                    ->label(__('Transfer Date'))
                    ->required()
                    ->jalali()
                    ->locale('fa')
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->money('AFN')
                    ->sortable(),
                Tables\Columns\TextColumn::make('destinationUser.name')
                    ->label(__('Destination Staff'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label(__('Reference'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('transfer_date')
                    ->label(__('Transfer Date'))
                    ->jalaliDate()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_transactions')
                    ->label(__('View Transactions'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (DailyTransfer $record) => TransactionResource::getUrl('index', [
                        'tableFilters' => [
                            'daily_transfer_id' => [
                                'value' => $record->id,
                            ],
                        ],
                    ])),
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
            'index' => Pages\ListDailyTransfers::route('/'),
            'create' => Pages\CreateDailyTransfer::route('/create'),
            'edit' => Pages\EditDailyTransfer::route('/{record}/edit'),
        ];
    }
}
