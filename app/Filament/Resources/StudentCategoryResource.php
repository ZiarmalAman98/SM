<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentCategoryResource\Pages;
use App\Models\StudentCategory;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentCategoryResource extends Resource
{
    protected static ?string $model = StudentCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('Student Management');
    }

public static function getNavigationLabel(): string
{
    return __('Student Categories');
}

public static function getModelLabel(): string
{
    return __('Student Category');
}

public static function getPluralModelLabel(): string
{
    return __('Student Categories');
}

public static function getLabel(): string
{
    return __('Student Category');
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->label('Category Name')
                        ->required()
                        ->columnSpanFull()
                        ->maxLength(100),

                    RichEditor::make('description')
                        ->fileAttachmentsDirectory('student_category')
                        ->columnSpanFull()
                        ->label('Category Description'),
                ])->description("Student Category Form")->collapsed(false)->columns(2)
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Group Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->html()
                    ->searchable()
                    ->default('Not Available')
                    ->label('Description')
                    ->limit(50),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentCategories::route('/'),
            'create' => Pages\CreateStudentCategory::route('/create'),
            'edit' => Pages\EditStudentCategory::route('/{record}/edit'),
        ];
    }
}
