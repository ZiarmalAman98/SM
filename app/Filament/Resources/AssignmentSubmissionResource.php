<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentSubmissionResource\Pages;
use App\Filament\Resources\AssignmentSubmissionResource\RelationManagers\AssignmentRelationManager;
use App\Filament\Resources\AssignmentSubmissionResource\RelationManagers\StudentRelationManager;
use App\Models\AssignmentSubmission;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Morilog\Jalali\Jalalian;

class AssignmentSubmissionResource extends Resource
{
    protected static ?string $model = AssignmentSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): string
    {
        return __('Assignments');
    }

    public static function getLabel(): string
    {
        return __('Student Report');
    }

    public static function getModelLabel(): string
    {
        return __('Assignment Submission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Assignment Submissions');
    }

    public static function getNavigationLabel(): string
    {
        return __('Assignment Submission');
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Select::make('assignment_id')
                        ->label(__('Assignment'))
                        ->relationship('assignment', 'title')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->placeholder(__('Select Assignment')),

                    Select::make('student_id')
                        ->label(__('Student'))
                        ->relationship('student', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->placeholder(__('Select Student')),

                    DateTimePicker::make('submitted_at')
                        ->label(__('Submitted At'))
                        ->jalali()
                        ->locale('fa')
                        ->default(now())
                        ->required()
                        ->placeholder(__('Select Submission Date')),

                    Select::make('status')
                        ->label(__('Status'))
                        ->native(false)
                        ->options([
                            'pending' => __('Pending'),
                            'reviewed' => __('Reviewed'),
                            'graded' => __('Graded'),
                        ])
                        ->default('pending')
                        ->required()
                        ->placeholder(__('Select Status')),

                    RichEditor::make('description')
                        ->label(__('Assignment Description'))
                        ->fileAttachmentsDirectory('assignmentSubmission')
                        ->columnSpanFull()
                        ->placeholder(__('Enter assignment description here')),

                    FileUpload::make('file_path')
                        ->label(__('Submission File'))
                        ->directory('assignmentSubmission')
                        ->disk('public')
                        ->openable()
                        ->downloadable()
                        ->deletable()
                        ->maxSize(2048)
                        ->helperText(__('Maximum file size: 2MB'))
                        ->columnSpanFull()
                        ->required(),
                ])
                    ->description(__('Assignment Submission Form'))
                    ->collapsed(false)
                    ->columns(2)
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('assignment.title')
                    ->label(__('Assignment'))
                    ->sortable()
                    ->limit(20)
                    ->searchable()
                    ->placeholder(__('Deleted assignment'))
                    ->tooltip(fn($record) => $record->assignment?->title ?? __('Deleted assignment')),

                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->sortable()
                    ->searchable()
                    ->placeholder(__('Deleted student'))
                    ->tooltip(fn($record) => $record->student?->name ?? __('Deleted student')),

                TextColumn::make('student.father_name')
                    ->label(__('F/Name'))
                    ->sortable()
                    ->searchable()
                    ->placeholder(__('Unknown'))
                    ->tooltip(fn($record) => $record->student?->father_name ?? __('Unknown')),

                TextColumn::make('latest_class')
                    ->label(__('Class'))
                    ->getStateUsing(fn ($record) =>
                        $record->student?->latestEnrollment?->schoolClass?->class_name ?? '-'
                            ),





                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->colors([
                        'pending' => 'warning',
                        'reviewed' => 'info',
                        'graded' => 'success',
                    ])
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => __($state)),

                ImageColumn::make('file_path')
                    ->label(__('File'))
                    ->getStateUsing(fn() => asset('images/file.png'))
                    ->openUrlInNewTab()
                    ->tooltip(__('View submission file')),

                TextColumn::make('submitted_at')
                    ->label(__('Submitted At'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d H:i') : '-')
                    ->sortable()
                    ->tooltip(fn($record) => $record->submitted_at?->diffForHumans()),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending' => __('Pending'),
                        'reviewed' => __('Reviewed'),
                        'graded' => __('Graded'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('View'))
                    ->tooltip(__('View submission details')),

                Tables\Actions\EditAction::make()
                    ->label(__('Edit'))
                    ->tooltip(__('Edit submission')),
            ])
            ->emptyStateHeading(__('No submissions found'))
            ->emptyStateDescription(__('Create your first submission'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Submission')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AssignmentRelationManager::class,
            StudentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignmentSubmissions::route('/'),
            'create' => Pages\CreateAssignmentSubmission::route('/create'),
            'edit' => Pages\EditAssignmentSubmission::route('/{record}/edit'),
        ];
    }
}
