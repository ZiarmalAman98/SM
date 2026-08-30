<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxResource\Pages;
use App\Models\Tax;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Morilog\Jalali\Jalalian;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): string
    {
        return __('Payroll');
    }

    public static function getNavigationLabel(): string
    {
        return __('Taxes');
    }

    public static function getModelLabel(): string
    {
        return __('Tax');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Taxes');
    }

    public static function getLabel(): string
    {
        return __('Tax');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('min_amount')
                        ->numeric()
                        ->required()
                        ->label(__('Minimum Amount')),

                    Forms\Components\TextInput::make('max_amount')
                        ->numeric()
                        ->nullable()
                        ->label(__('Maximum Amount')),

                    Forms\Components\TextInput::make('fixed_amount')
                        ->numeric()
                        ->nullable()
                        ->label(__('Fixed Amount')),

                    Forms\Components\TextInput::make('percentage')
                        ->numeric()
                        ->step(0.01)
                        ->nullable()
                        ->label(__('Percentage (%)')),
                ])
                    ->description(__("Tax Form"))
                    ->collapsed(false)
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('min_amount')
                    ->sortable()
                    ->searchable()
                    ->label(__('Min Amount')),

                Tables\Columns\TextColumn::make('max_amount')
                    ->sortable()
                    ->searchable()
                    ->label(__('Max Amount')),

                Tables\Columns\TextColumn::make('fixed_amount')
                    ->sortable()
                    ->searchable()
                    ->label(__('Fixed Amount')),

                Tables\Columns\TextColumn::make('percentage')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($state) => $state ? $state . '%' : '-')
                    ->label(__('Percentage')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->sortable()
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i')),
            ])
            ->filters([
                Tables\Filters\Filter::make(__('Has Percentage'))
                    ->query(fn($query) => $query->whereNotNull('percentage')),

                Tables\Filters\Filter::make(__('Has Fixed Amount'))
                    ->query(fn($query) => $query->whereNotNull('fixed_amount')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTaxes::route('/'),
            'create' => Pages\CreateTax::route('/create'),
            'edit' => Pages\EditTax::route('/{record}/edit'),
        ];
    }
}
