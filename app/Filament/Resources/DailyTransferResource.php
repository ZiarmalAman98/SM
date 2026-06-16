<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyTransferResource\Pages;
use App\Filament\Resources\DailyTransferResource\RelationManagers;
use App\Models\DailyTransfer;
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
        return 'Daily Balance';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('destination')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('transfer_date')
                    ->required()
                    ->jalali()
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->money('AFG'),
                Tables\Columns\TextColumn::make('destinationUser.name')
                    ->label('Destination Staff')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('reference'),
                Tables\Columns\TextColumn::make('transfer_date')
                    ->jalaliDate(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_transactions')
                    ->url(fn(DailyTransfer $record) => TransactionResource::getUrl('index', [
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

    public static function getRelations(): array
    {
        return [
            // RelationManagers\TransactionsRelationManager::class,
        ];
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
