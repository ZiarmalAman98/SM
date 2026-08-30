<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolClassResource\Pages;
use App\Filament\Resources\SchoolClassResource\RelationManagers\SubjectsRelationManager;
use App\Models\SchoolClass;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action as TableAction;

class SchoolClassResource extends Resource
{
    protected static ?string $model = SchoolClass::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    // protected static ?string $navigationGroup = 'Default Data';
    protected static ?int $navigationSort = 1; // after Branch: create class
    public static function getNavigationGroup(): string
    {
        return __('Default Data');
    }

    public static function getNavigationLabel(): string
    {
        return __('School Classes');
    }

    public static function getModelLabel(): string
    {
        return __('School Class');
    }

    public static function getPluralModelLabel(): string
    {
        return __('School Classes');
    }

    public static function getLabel(): string
    {
        return __('School Class');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('class_name')
                            ->label(__('Class Name'))
                            ->required()
                            ->maxLength(100),

                        Forms\Components\Select::make('branch_id')
                            ->label(__('Branch'))
                            ->relationship('branch', 'branch_name')
                            ->searchable()
                            ->required()
                            ->preload(),
                        Forms\Components\Select::make('teacher_id')
                            ->label(__('Class Teacher'))
                            ->options(fn() => \App\Models\User::where('type', 'teacher')->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->preload()
                            ->columnSpanFull()
                            ->nullable(),

                        Forms\Components\RichEditor::make('description')
                            ->label(__('Description'))
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('schoolClass')
                            ->nullable(),
                    ])
                    ->description(__('School Class Form'))
                    ->collapsed(false)
                    ->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('class_name')
                    ->label(__('Class Name'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('branch.branch_name')
                    ->label(__('Branch'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label(__('Class Teacher'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->html()
                    ->searchable()
                    ->default(__('Not Available'))
                    ->label(__('Description'))
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->jalaliDateTime(),
            ])
            ->filters([])
            ->actions([
                TableAction::make('print')
                    ->label(__('Print Timetable'))
                    ->color('blue')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('classes.print-timetable', ['id' => $record->id]))
                    ->openUrlInNewTab(),

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
            SubjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchoolClasses::route('/'),
            'create' => Pages\CreateSchoolClass::route('/create'),
            'edit' => Pages\EditSchoolClass::route('/{record}/edit'),
        ];
    }
}
