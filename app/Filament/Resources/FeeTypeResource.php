<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeTypeResource\Pages;
use App\Filament\Resources\FeeTypeResource\RelationManagers;
use App\Models\FeeType;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeeTypeResource extends Resource
{
    protected static ?string $model = FeeType::class;
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';


    public static function getLabel(): string
    {
        return __('Fee Type');
    }

    public static function getModelLabel(): string
    {
        return __('Fee Type');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Fee Types');
    }

    public static function getNavigationLabel(): string
    {
        return __('Fee Types');
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
                    Forms\Components\TextInput::make('name')
                        ->label(__('Fee Name'))
                        ->required()
                        ->maxLength(100)
                        ->placeholder(__('Enter fee name')),

                    Forms\Components\TextInput::make('default_amount')
                        ->label(__('Default Amount'))
                        ->numeric()
                        ->minValue(0)
                        ->suffix('AFN')
                        ->nullable()
                        ->placeholder(__('Enter amount')),

                    Forms\Components\RichEditor::make('description')
                        ->label(__('Description'))
                        ->fileAttachmentsDirectory('feeType')
                        ->columnSpanFull()
                        ->nullable()
                        ->placeholder(__('Enter description')),
                ])
                    ->description(__("Fee Type Form"))
                    ->collapsed(false)
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Fee Name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label(__('Description'))
                    ->default(__('Not Available'))
                    ->html()
                    ->limit(50),

                Tables\Columns\TextColumn::make('default_amount')
                    ->label(__('Default Amount'))
                    ->formatStateUsing(fn($state) => $state ? number_format($state, 2) . ' AFN' : __('Not Available'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->jalaliDateTime(),
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
            'index' => Pages\ListFeeTypes::route('/'),
            'create' => Pages\CreateFeeType::route('/create'),
            'edit' => Pages\EditFeeType::route('/{record}/edit'),
        ];
    }
}
