<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeSystemResource\Pages;
use App\Filament\Resources\GradeSystemResource\RelationManagers;
use App\Models\GradeSystem;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GradeSystemResource extends Resource
{
    protected static ?string $model = GradeSystem::class;
    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

  public static function getLabel(): string
{
    return __('Grade System');
}

public static function getModelLabel(): string
{
    return __('Grade System');
}

public static function getPluralModelLabel(): string
{
    return __('Grade Systems');
}

public static function getNavigationLabel(): string
{
    return __('Grade Systems');
}

public static function getNavigationGroup(): string
{
    return __('Examinations'); // Changed from 'Exam' to 'Examinations' for better grouping
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('from')
                        ->label(__('From (0)'))
                        ->numeric()
                        ->required()
                        ->placeholder(__('Enter minimum score')),

                    Forms\Components\TextInput::make('to')
                        ->label(__('To (100)'))
                        ->numeric()
                        ->required()
                        ->placeholder(__('Enter maximum score')),

                    Forms\Components\TextInput::make('title')
                        ->label(__('Grade Title'))
                        ->required()
                        ->maxLength(10)
                        ->placeholder(__('Enter grade title'))
                        ->columnSpanFull(),
                ])
                ->description(__("Grade System Form"))
                ->collapsed(false)
                ->columns(2)
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from')
                    ->label(__('From')),

                Tables\Columns\TextColumn::make('to')
                    ->label(__('To')),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('Grade')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),
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
            'index' => Pages\ListGradeSystems::route('/'),
            'create' => Pages\CreateGradeSystem::route('/create'),
            'edit' => Pages\EditGradeSystem::route('/{record}/edit'),
        ];
    }
}
