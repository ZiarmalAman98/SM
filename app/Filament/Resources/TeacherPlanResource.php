<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherPlanResource\Pages;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeacherPlan;
use App\Models\User;
use App\Exports\TeacherPlanWordExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;

class TeacherPlanResource extends Resource
{
    protected static ?string $model = TeacherPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Calendar';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Plan Information')
                    ->description('Fill in the details of your teaching plan')
                    ->icon('heroicon-o-academic-cap')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('teacher_id')
                                    ->label('Teacher')
                                    ->relationship('teacher', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->options(User::whereHas('roles', function ($query) {
                                        $query->where('name', 'teacher');
                                    })->pluck('name', 'id'))
                                    ->placeholder('Select a teacher')
                                    ->columnSpan(1),

                                Forms\Components\Select::make('subject_id')
                                    ->label('Subject')
                                    ->relationship('subject', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Select a subject')
                                    ->columnSpan(1),
                            ])
                            ->columns(2),

                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('class_id')
                                    ->label('Class')
                                    ->relationship('schoolClass', 'class_name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Select a class')
                                    ->columnSpan(1),

                                Forms\Components\DatePicker::make('date')
                                    ->required()
                                    ->default(now())
                                    ->jalali()
                                    ->maxDate(now())
                                    ->displayFormat('M d, Y')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('student_count')
                                    ->label('Number of Students')
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(65535)
                                    ->suffix('students')
                                    ->columnSpan(1),
                            ])
                            ->columns(3),

                        Forms\Components\TextInput::make('topic_covered')
                            ->label('Topic Covered')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter the main topic covered in this lesson')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('details')
                            ->label('Lesson Details')
                            ->rows(6)
                            ->placeholder('Describe the lesson activities, materials used, and any additional notes...')
                            ->helperText('Provide detailed information about the lesson content and teaching methods')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Subject')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('schoolClass.class_name')
                    ->label('Class')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->jalaliDate()
                    ->sortable(),

                Tables\Columns\TextColumn::make('topic_covered')
                    ->label('Topic')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('student_count')
                    ->label('Students')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Teacher')
                    ->options(
                        User::whereHas('roles', function ($query) {
                            $query->where('name', 'teacher');
                        })->pluck('name', 'id')
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'class_name')
                    ->searchable()
                    ->preload(),

                Filter::make('date')
                    ->label('Plan Date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->jalali()
                            ->default(now())
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('date_until')
                            ->jalali()
                            ->default(now())
                            ->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Add relations if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeacherPlans::route('/'),
            'create' => Pages\CreateTeacherPlan::route('/create'),
            'edit' => Pages\EditTeacherPlan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
