<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\StudentClass;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'results';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('exam_id')
                    ->relationship('exam', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Set the related class_id when exam changes
                        $exam = Exam::find($state);
                        $set('class_id', $exam?->class_id);
                    }),

                // Class (show class name, store class_id)
                Forms\Components\Hidden::make('class_id')
                    ->label('Class')
                    ->disabled()
                    ->dehydrated() // Ensures the class_id (not name) is saved
                    ->formatStateUsing(function ($state) {
                        if (!$state)
                            return null;

                        // Get class name by class_id (not exam_id anymore)
                        $class = SchoolClass::find($state);
                        return $class?->class_name ?? 'N/A';
                    })
                    ->live(),

                Forms\Components\Select::make('student_id')
                    ->label('Student')
                    ->options(function (Get $get) {
                        $examId = $get('exam_id');

                        if (!$examId) {
                            return [];
                        }

                        $exam = Exam::with('class')->find($examId);

                        if (!$exam) {
                            return [];
                        }

                        // Get student IDs from student_classes for the exam's class
                        $studentIds = StudentClass::where('class_id', $exam->class_id)
                            ->where('status', 'active')
                            ->pluck('student_id');

                        // Return [id => name] array of students where user type is 'student'
                        return User::whereIn('id', $studentIds)
                            ->where('type', 'student')
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->preload()
                    ->disabled(fn(Get $get) => !$get('exam_id')),

                Forms\Components\TextInput::make('marks')
                    ->numeric()
                    ->label('Marks (0-100)')
                    ->required()
                    ->placeholder('Marks e.g. 90')
                    ->minValue(0)
                    ->maxValue(100),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('exam.name')->searchable()->label('Exam'),
                Tables\Columns\TextColumn::make('class.class_name')->searchable()->label('Class'),
                Tables\Columns\TextColumn::make('student.name')->searchable()->label('Student'),
                Tables\Columns\TextColumn::make('marks')->searchable(),
                Tables\Columns\TextColumn::make('grade')
                    ->label('Grade')
                    ->badge()
                    ->color(function ($state, $record) {
                        $marks = $record->marks;

                        if ($marks < 50) {
                            return 'danger';
                        } elseif ($marks < 80) {
                            return 'warning';
                        } else {
                            return 'success';
                        }
                    }),
                Tables\Columns\TextColumn::make('exam.date')
                    ->searchable()
                    ->sortable()
                    ->label('Exam Date'),
                Tables\Columns\TextColumn::make('created_at')->since()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->since()->toggleable(),
            ])->defaultSort('created_at', 'desc')
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
