<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeDiscountResource\Pages;
use App\Filament\Resources\FeeDiscountResource\RelationManagers;
use App\Models\FeeDiscount;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeeDiscountResource extends Resource
{
    protected static ?string $model = FeeDiscount::class;
    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';
    // protected static ?string $navigationGroup = 'Default Data';
    protected static ?int $navigationSort = 6;

    public static function getLabel(): string
    {
        return __('Fee Discount');
    }

    public static function getModelLabel(): string
    {
        return __('Fee Discount');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Fee Discounts');
    }

    public static function getNavigationLabel(): string
    {
        return __('Fee Discounts');
    }

    public static function getNavigationGroup(): string
    {
        return __('Default Data');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('discount_name')
                        ->label(__('Discount Name'))
                        ->required()
                        ->maxLength(100)
                        ->placeholder(__('Enter discount name')),

                    TextInput::make('discount_code')
                        ->label(__('Discount Code'))
                        ->unique(FeeDiscount::class, 'discount_code', ignoreRecord: true)
                        ->required()
                        ->maxLength(50)
                        ->placeholder(__('Enter discount code')),

                    TextInput::make('discount_value')
                        ->label(__('Discount Value'))
                        ->numeric()
                        ->minValue(0)
                        ->default(0.00)
                        ->suffix('Afn')
                        ->placeholder(__('Enter discount value'))
                        ->columnSpanFull(),
                ])
                    ->description(__("Fee Discount Form"))
                    ->collapsed(false)
                    ->columns(2)
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('discount_name')
                    ->label(__('Discount Name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('discount_code')
                    ->label(__('Discount Code'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('discount_value')
                    ->label(__('Discount Value'))
                    ->sortable()
                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' Afn'),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->jalaliDateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('Delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete Selected')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFeeDiscounts::route('/'),
            'create' => Pages\CreateFeeDiscount::route('/create'),
            'edit' => Pages\EditFeeDiscount::route('/{record}/edit'),
        ];
    }
}
