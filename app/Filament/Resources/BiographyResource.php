<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BiographyResource\Pages;
use App\Models\Biography;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BiographyResource extends Resource
{
    protected static ?string $model = Biography::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    public static function getNavigationGroup(): string
    {
        return __('Staff Management');
    }
    protected static ?string $navigationLabel = 'Biographies';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('father_name')->required(),
                        Forms\Components\TextInput::make('grand_father_name')->required(),
                        Forms\Components\TextInput::make('family_name'),
                        Forms\Components\TextInput::make('age')->numeric()->minValue(0),
                        Forms\Components\TextInput::make('citizenship_tazkira'),
                        Forms\Components\TextInput::make('nationality')->required(),
                        Forms\Components\TextInput::make('father_occupation')->required(),
                        Forms\Components\TextInput::make('mother_language')->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Addresses')
                    ->schema([
                        Forms\Components\Fieldset::make('Permanent Address')
                            ->schema([
                                Forms\Components\TextInput::make('permanent_province'),
                                Forms\Components\TextInput::make('permanent_district'),
                                Forms\Components\TextInput::make('permanent_village'),
                            ])->columns(3),

                        Forms\Components\Fieldset::make('Current Address')
                            ->schema([
                                Forms\Components\TextInput::make('current_province'),
                                Forms\Components\TextInput::make('current_district'),
                                Forms\Components\TextInput::make('current_village'),
                            ])->columns(3),
                    ]),

                Forms\Components\Section::make('Family Relations')
                    ->schema([
                        Forms\Components\TextInput::make('brother_name'),
                        Forms\Components\TextInput::make('uncle_name'),
                        Forms\Components\TextInput::make('maternal_uncle_name'),
                        Forms\Components\TextInput::make('maternal_uncle_son_name'),
                        Forms\Components\TextInput::make('paternal_uncle_son_name'),
                    ])->columns(2),

                Forms\Components\Section::make('Other Info')
                    ->schema([
                        Forms\Components\TextInput::make('contact_number')->tel(),
                        Forms\Components\Textarea::make('notes')->rows(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('father_name')->searchable(),
                Tables\Columns\TextColumn::make('grand_father_name'),
                Tables\Columns\TextColumn::make('nationality'),
                Tables\Columns\TextColumn::make('father_occupation'),
                Tables\Columns\TextColumn::make('contact_number'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Year')
                    ->jalaliDate('Y'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),


                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn(Biography $record) => route('biographies.print', $record))
                    ->openUrlInNewTab(), // open in a new window/tab

                Tables\Actions\Action::make('card')
                    ->label('Biography Card')
                    ->icon('heroicon-o-identification')
                    ->url(fn(Biography $record) => route('biographies.card', $record))
                    ->openUrlInNewTab(), // open in a new window/tab
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBiographies::route('/'),
            'create' => Pages\CreateBiography::route('/create'),
            'edit' => Pages\EditBiography::route('/{record}/edit'),
            'view' => Pages\ViewBiography::route('/{record}'),
        ];
    }
}
