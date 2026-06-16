<?php

namespace App\Filament\Resources\EvaluationQuestionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('teacher_id')
                            ->relationship('teacher', 'name')
                            ->label('Teacher')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),

                        Forms\Components\Select::make('student_id')
                            ->relationship('student', 'name')
                            ->label('Student')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Anonymous'),

                        Forms\Components\Select::make('rating')
                            ->label('Rating')
                            ->options([
                                1 => '1 - Very Poor',
                                2 => '2 - Poor',
                                3 => '3 - Average',
                                4 => '4 - Good',
                                5 => '5 - Excellent',
                            ])
                            ->native(false)
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Textarea::make('comment')
                    ->label('Comment')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('academic_year')
                    ->label('Academic Year')
                    ->default(date('Y'))
                    ->numeric()
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.name')
                    ->label('Student')
                    ->default('Anonymous')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('question.question_text')
                    ->label('Question')
                    ->wrap()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->question->question_text),

                // ⭐️ Rating with stars
                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(function ($state) {
                        return str_repeat('⭐', $state);
                    })
                    ->sortable(),

                TextColumn::make('academic_year')
                    ->label('Year')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
