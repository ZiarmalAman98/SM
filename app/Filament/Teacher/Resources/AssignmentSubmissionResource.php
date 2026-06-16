<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\AssignmentSubmissionResource\Pages;
use App\Models\AssignmentSubmission;
use Filament\Facades\Filament;
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
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoSection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;

class AssignmentSubmissionResource extends Resource
{
    protected static ?string $model = AssignmentSubmission::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): string
    {
        return __('Homeworks');
    }

    public static function getModelLabel(): string
    {
        return __('Homework Submission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Homework Submissions');
    }

    /** 🔐 Only submissions for assignments of the logged-in teacher */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas(
                'assignment',
                fn(Builder $q) =>
                $q->where('teacher_id', Filament::auth()->id())
            );
    }

    /** 🔐 Badge count for this teacher */
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereHas(
            'assignment',
            fn(Builder $q) =>
            $q->where('teacher_id', Filament::auth()->id())
        )->count();
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make()->schema([
                Select::make('assignment_id')
                    ->label(__('Assignment'))
                    ->relationship(
                        name: 'assignment',
                        titleAttribute: 'title',
                        modifyQueryUsing: fn(Builder $q) =>
                        $q->where('teacher_id', Filament::auth()->id())
                    )
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
                    ->required(),

                Select::make('status')
                    ->label(__('Status'))
                    ->native(false)
                    ->options([
                        'pending'  => __('Pending'),
                        'reviewed' => __('Reviewed'),
                        'graded'   => __('Graded'),
                    ])
                    ->default('pending')
                    ->required(),

                RichEditor::make('description')
                    ->label(__('Assignment Description'))
                    ->fileAttachmentsDirectory('assignmentSubmission')
                    ->columnSpanFull(),

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
            ])->description(__('Assignment Submission Form'))->collapsed(false)->columns(2),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // ⚠️ Do NOT sort on relationship columns to avoid qualifyColumn() issues
                TextColumn::make('assignment.title')
                    ->label(__('Assignment'))
                    ->limit(20)
                    ->searchable()
                    ->tooltip(fn($record) => $record->assignment?->title),

                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->searchable()
                    ->tooltip(fn($record) => $record->student?->name),

                BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->colors([
                        'warning' => fn(string $state): bool => $state === 'pending',
                        'info'    => fn(string $state): bool => $state === 'reviewed',
                        'success' => fn(string $state): bool => $state === 'graded',
                    ])
                    ->formatStateUsing(fn(string $state): string => __($state)),

                ImageColumn::make('file_path')
                    ->label(__('File'))
                    ->getStateUsing(fn() => asset('images/file.png'))
                    ->openUrlInNewTab()
                    ->tooltip(__('View submission file')),

                TextColumn::make('submitted_at')
                    ->label(__('Submitted At'))
                    ->sortable()
                    ->formatStateUsing(
                        fn($state) =>
                        $state ? Jalalian::fromDateTime($state)->format('Y/m/d H:i') : '-'
                    )
                    ->tooltip(fn($record) => $record->submitted_at?->diffForHumans()),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'pending'  => __('Pending'),
                        'reviewed' => __('Reviewed'),
                        'graded'   => __('Graded'),
                    ]),
            ])
            ->actions([
                // ✅ View in a modal (no route needed)
                Tables\Actions\ViewAction::make()
                    ->label(__('View'))
                    ->infolist([
                        InfoSection::make(__('Submission'))
                            ->schema([
                                TextEntry::make('assignment.title')->label(__('Assignment')),
                                TextEntry::make('student.name')->label(__('Student')),
                                TextEntry::make('status')->label(__('Status'))
                                    ->formatStateUsing(fn($state) => __($state)),
                                TextEntry::make('submitted_at')->label(__('Submitted At'))
                                    ->formatStateUsing(
                                        fn($state) =>
                                        $state ? Jalalian::fromDateTime($state)->format('Y/m/d H:i') : '-'
                                    ),
                                TextEntry::make('description')->label(__('Description'))->html(),
                            ])
                            ->columns(2),
                    ]),

                // Optional: direct file download button
                Action::make('download')
                    ->label(__('Download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(
                        fn($record) =>
                        $record->file_path
                            ? Storage::disk('public')->url($record->file_path)
                            : null
                    )
                    ->openUrlInNewTab()
                    ->visible(fn($record) => filled($record->file_path)),

                // Tables\Actions\EditAction::make()->label(__('Edit')),
                // Tables\Actions\DeleteAction::make()->label(__('Delete')),
            ])
            ->emptyStateHeading(__('No submissions found'))
            ->emptyStateDescription(__('Submissions from your students will appear here'));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignmentSubmissions::route('/'),
            // No 'view' page route needed — we use the modal ViewAction above.
            'edit'  => Pages\EditAssignmentSubmission::route('/{record}/edit'),
        ];
    }
}
