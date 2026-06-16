<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentClassResource\Pages;
use App\Models\SchoolClass;
use App\Models\StudentClass;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class StudentClassResource extends Resource
{
    protected static ?string $model = StudentClass::class;
    protected static ?int $navigationSort = 4;
    public static function getNavigationGroup(): string
    {
        return __('Account Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Student Classes');
    }

    public static function getModelLabel(): string
    {
        return __('Student Class');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Student Classes');
    }

    public static function getLabel(): string
    {
        return __('Student Class');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    Forms\Components\Select::make('student_id')
                        ->label(__('Student'))
                        ->options(User::where('type', 'student')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('class_id')->label(__('Class'))->relationship('schoolClass', 'class_name')->searchable()->preload()->required(),

                    Forms\Components\Select::make('academic_year')
                        ->label(__('Academic Year'))
                        ->options(array_combine(range(2010, 2099), range(2010, 2099)))
                        ->searchable()
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->label(__('Status'))
                        ->native(false)
                        ->options([
                            'active' => __('Active'),
                            'completed' => __('Completed'),
                            'transferred' => __('Transferred'),
                        ])
                        ->default('active')
                        ->required(),
                ])
                ->description(__('Student Class Form'))
                ->collapsed(false)
                ->columns(2),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')->label(__('Student'))->sortable()->searchable(),
                Tables\Columns\TextColumn::make('student.father_name')->label(__('Father Name'))->sortable()->searchable(),

                Tables\Columns\TextColumn::make('schoolClass.class_name')->label(__('Class'))->sortable()->searchable(),

                Tables\Columns\TextColumn::make('academic_year')->label(__('Year'))->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => ucfirst(__($state)))
                    ->colors([
                        'success' => 'active',
                        'warning' => 'completed',
                        'danger' => 'transferred',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('student_id')->label(__('Student'))->relationship('student', 'name')->searchable()->preload(),

                Tables\Filters\SelectFilter::make('class_id')->label(__('Class'))->relationship('schoolClass', 'class_name')->searchable()->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => __('Active'),
                        'completed' => __('Completed'),
                        'transferred' => __('Transferred'),
                    ])
                    ->native(false)
                    ->label(__('Filter by Status')),

                Tables\Filters\SelectFilter::make('academic_year')
                    ->options(array_combine(range(2010, 2099), range(2010, 2099)))
                    ->label(__('Filter by Academic Year'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('print_appreciation_letter')
                    ->label('Print Letter')
                    ->icon('heroicon-m-printer')
                    ->url(fn($record) => route('student-classes.print-letter', ['studentClass' => $record->getKey()]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                BulkAction::make('transfer')
                    ->label(__('Transfer Student'))
                    ->action(function (\Illuminate\Support\Collection $records, array $data) {
                        DB::beginTransaction();

                        try {
                            foreach ($records as $record) {
                                // Check if student has failed (total marks < 400)
                                $totalMarks = $record->student->exam_scores()->where('student_id', $record->student_id)->sum('marks');

                                if ($totalMarks < 400) {
                                    throw new \Exception(__('Student :name cannot be transferred because they have failed (Total marks: :marks/400).', ['name' => $record->student->name, 'marks' => $totalMarks]));
                                }

                                // Check for duplicate before proceeding
                                $exists = StudentClass::where('student_id', $record->student_id)->where('class_id', $data['class_id'])->where('academic_year', $data['academic_year'])->exists();

                                if ($exists) {
                                    throw new \Exception(__('Student :name already exists in the selected class and year.', ['name' => $record->student->name]));
                                }

                                StudentClass::create([
                                    'student_id' => $record->student_id,
                                    'class_id' => $data['class_id'],
                                    'academic_year' => $data['academic_year'],
                                    'status' => 'active',
                                ]);

                                $record->update(['status' => 'completed']);
                            }

                            DB::commit();

                            Notification::make()->title(__('Students transferred successfully.'))->success()->send();
                        } catch (\Throwable $e) {
                            DB::rollBack();

                            Notification::make()->title(__('Transfer failed'))->body($e->getMessage())->danger()->send();
                        }
                    })
                    ->form([
                        Forms\Components\Select::make('academic_year')
                            ->label(__('Academic Year'))
                            ->options(array_combine(range(2010, 2099), range(2010, 2099)))
                            ->searchable()
                            ->required(),

                        Forms\Components\Select::make('class_id')->label(__('New Class'))->options(SchoolClass::pluck('class_name', 'id'))->searchable()->required(),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentClasses::route('/'),
            'create' => Pages\CreateStudentClass::route('/create'),
            'edit' => Pages\EditStudentClass::route('/{record}/edit'),
        ];
    }
}
