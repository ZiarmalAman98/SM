<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeGroupResource\Pages;
use App\Filament\Resources\FeeGroupResource\RelationManagers;
use App\Models\FeeGroup;
use App\Models\FeeType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class FeeGroupResource extends Resource
{
    protected static ?string $model = FeeGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';
    protected static ?int $navigationSort = 3;

    public static function getLabel(): string
    {
        return __('Fee Group');
    }

    public static function getModelLabel(): string
    {
        return __('Fee Group');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Fee Groups');
    }

    public static function getNavigationLabel(): string
    {
        return __('Fee Groups');
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
                    Forms\Components\TextInput::make('group_name')
                        ->label(__('Group Name'))
                        ->columnSpanFull()
                        ->required()
                        ->maxLength(100)
                        ->placeholder(__('Enter group name')),

                    Forms\Components\RichEditor::make('description')
                        ->label(__('Description'))
                        ->columnSpanFull()
                        ->fileAttachmentsDirectory('schoolClass')
                        ->nullable()
                        ->placeholder(__('Enter description')),

                    Repeater::make('feeGroupFeeTypes')
                        ->label(__('Assign Fee Types'))
                        ->relationship('feeGroupFeeTypes')
                        ->schema([
                            Select::make('fee_type_id')
                                ->label(__('Fee Type'))
                                ->options(FeeType::pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->preload()
                                ->placeholder(__('Select fee type'))
                                ->createOptionForm(fn(Form $form) => FeeTypeResource::form($form)),

                            TextInput::make('amount')
                                ->label(__('Amount (Afn)'))
                                ->numeric()
                                ->minValue(0)
                                ->default(0.00)
                                ->placeholder(__('Enter amount')),
                        ])
                        ->columnSpanFull()
                        ->columns(2),
                ])->description(__("Fee Group Form"))->collapsed(false)->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group_name')
                    ->label(__('Group Name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->html()
                    ->searchable()
                    ->default(__('Not Available'))
                    ->label(__('Description'))
                    ->limit(50),

                Tables\Columns\TextColumn::make('feeTypes.name')
                    ->label(__('Fee Types'))
                    ->listWithLineBreaks(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->jalaliDateTime(),
            ])->defaultSort('created_at', 'desc')
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
            'index' => Pages\ListFeeGroups::route('/'),
            'create' => Pages\CreateFeeGroup::route('/create'),
            'edit' => Pages\EditFeeGroup::route('/{record}/edit'),
        ];
    }
}
