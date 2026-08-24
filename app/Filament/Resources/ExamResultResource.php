<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExamResultResource\Pages;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\GradeSystem;
use App\Models\StudentClass;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Filters\Filter;
use Morilog\Jalali\Jalalian;

class ExamResultResource extends Resource
{
    protected static ?string $model = ExamResult::class;
    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function getLabel(): string
    {
        return __('Result');
    }
    public static function getModelLabel(): string
    {
        return __('Result');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Results');
    }
    public static function getNavigationLabel(): string
    {
        return __('Exam Results');
    }
    public static function getNavigationGroup(): string
    {
        return __('Examinations');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\Select::make('exam_id')
                        ->relationship('exam', 'name')
                        ->label(__('Exam'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $exam = Exam::find($state);
                            $set('class_id', $exam?->class_id);
                        }),

                    Forms\Components\Hidden::make('class_id')
                        ->label(__('Class ID'))
                        ->disabled()
                        ->dehydrated(true)
                        ->formatStateUsing(function ($state, Get $get) {
                            $examId = $get('exam_id');
                            if (!$examId) return null;
                            return Exam::find($examId)?->class_id ?? __('N/A');
                        })
                        ->live(),

                    Forms\Components\Select::make('student_id')
                        ->label(__('Student'))
                        ->options(function (Get $get) {
                            $examId = $get('exam_id');
                            if (!$examId) return [];
                            $exam = Exam::with('class')->find($examId);
                            if (!$exam) return [];
                            $studentIds = StudentClass::where('class_id', $exam->class_id)
                                ->where('status', 'active')
                                ->pluck('student_id');
                            return User::whereIn('id', $studentIds)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->required()
                        ->preload()
                        ->disabled(fn(Get $get) => !$get('exam_id')),

                    Forms\Components\Select::make('subject_id')
                        ->label(__('Subject'))
                        ->options(function (Get $get) {
                            $examId = $get('exam_id');
                            $classId = Exam::find($examId)?->class_id ?? null;
                            if (!$classId) return [];
                            return \App\Models\Subject::where('school_class_id', $classId)
                                ->pluck('name', 'id')
                                ->toArray();
                        })
                        ->searchable()
                        ->required()
                        ->preload()
                        ->disabled(fn(Get $get) => !$get('class_id')),

                    Forms\Components\TextInput::make('marks')
                        ->label(__('Total Marks (0-100)'))
                        ->numeric()
                        ->required()
                        ->placeholder(__('Total Marks e.g. 90'))
                        ->minValue(0)
                        ->maxValue(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Set $set) => $set('mark_in_words', marksToWords($state))),

                    Forms\Components\TextInput::make('written_marks')
                        ->label(__('Written Marks'))
                        ->numeric()
                        ->placeholder(__('Written exam marks'))
                        ->minValue(0)
                        ->maxValue(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::syncMarksAndWords($get, $set)),

                    Forms\Components\TextInput::make('recital_marks')
                        ->label(__('Recital Marks'))
                        ->numeric()
                        ->placeholder(__('Recital marks'))
                        ->minValue(0)
                        ->maxValue(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::syncMarksAndWords($get, $set)),

                    Forms\Components\TextInput::make('homework_marks')
                        ->label(__('Homework Marks'))
                        ->numeric()
                        ->placeholder(__('Homework marks'))
                        ->minValue(0)
                        ->maxValue(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::syncMarksAndWords($get, $set)),

                    Forms\Components\TextInput::make('class_activity_marks')
                        ->label(__('Class Activity Marks'))
                        ->numeric()
                        ->placeholder(__('Class activity marks'))
                        ->minValue(0)
                        ->maxValue(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Get $get, Set $set) => self::syncMarksAndWords($get, $set)),

                    Forms\Components\TextInput::make('mark_in_words')
                        ->label(__('Mark in Words'))
                        ->placeholder(__('Mark in words (e.g., Fifty Five)'))
                        ->helperText(__('Filled automatically from the total marks. You can still edit it.'))
                        ->maxLength(255),
                ])
                    ->description(__("Exam Result Form"))
                    ->collapsed(false)
                    ->columns(2)
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exam.name')
                    ->label(__('Exam'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('class.class_name')
                    ->label(__('Class'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label(__('Subject'))
                    ->searchable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->searchable(),

                // Add Father's Name column
                Tables\Columns\TextColumn::make('student.father_name')
                    ->label(__('Father Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('written_marks')
                    ->label(__('Written'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('recital_marks')
                    ->label(__('Recital'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('homework_marks')
                    ->label(__('Homework'))
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('class_activity_marks')
                    ->label(__('Activity'))
                    ->searchable()
                    ->toggleable(),

                    Tables\Columns\TextColumn::make('marks')
                    ->label(__('Total Marks'))
                    ->searchable(),

                // Tables\Columns\TextColumn::make('mark_in_words')
                //     ->label(__('Mark in Words'))
                //     ->searchable()
                //     ->toggleable(),

                Tables\Columns\TextColumn::make('grade')
                    ->label(__('Grade'))
                    ->badge()
                    ->color(function ($state, $record) {
                        $marks = $record->marks;
                        return $marks < 50 ? 'danger' : ($marks < 80 ? 'warning' : 'success');
                    }),

                Tables\Columns\TextColumn::make('exam.date')
                    ->label(__('Exam Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : '-')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->toggleable(true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('Updated At'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->toggleable(true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('exam_id')
                    ->label(__('Exam'))
                    ->searchable()
                    ->preload()
                    ->relationship('exam', 'name'),

                Tables\Filters\SelectFilter::make('student_id')
                    ->label(__('Student'))
                    ->searchable()
                    ->preload()
                    ->relationship('student', 'name'),

                Tables\Filters\SelectFilter::make('class_')
                    ->label(__('Class'))
                    ->searchable()
                    ->preload()
                    ->relationship('class', 'class_name'),

                Tables\Filters\SelectFilter::make('subject_')
                    ->label(__('Subject'))
                    ->searchable()
                    ->preload()
                    ->relationship('subject', 'name'),

                Filter::make('grade')
                    ->label(__('Grade'))
                    ->form([
                        Forms\Components\Select::make('grade')
                            ->label(__('Grade'))
                            ->options(
                                GradeSystem::query()
                                    ->orderBy('from')
                                    ->pluck('title', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->placeholder(__('Select Grade')),
                    ])
                    ->query(function ($query, array $data) {
                        if (!$data['grade']) return $query;
                        $grade = GradeSystem::find($data['grade']);
                        return $grade ? $query->whereBetween('marks', [$grade->from, $grade->to]) : $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['grade']) return null;
                        return __('Grade: :grade', ['grade' => GradeSystem::find($data['grade'])?->title]);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExamResults::route('/'),
            'create' => Pages\CreateExamResult::route('/create'),
            'edit' => Pages\EditExamResult::route('/{record}/edit'),
            'enter' => Pages\EnterExamMarks::route('/enter'),
            'view' => Pages\ViewExamResult::route('/{record}'),
        ];
    }

    public static function syncMarksAndWords(Get $get, Set $set): void
    {
        $total = (float) ($get('written_marks') ?? 0)
            + (float) ($get('recital_marks') ?? 0)
            + (float) ($get('homework_marks') ?? 0)
            + (float) ($get('class_activity_marks') ?? 0);

        $hasComponents = filled($get('written_marks'))
            || filled($get('recital_marks'))
            || filled($get('homework_marks'))
            || filled($get('class_activity_marks'));

        if ($hasComponents) {
            $set('marks', $total);
            $set('mark_in_words', marksToWords($total));

            return;
        }

        $set('mark_in_words', marksToWords($get('marks')));
    }
}
