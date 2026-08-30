<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\HomeworkSubmissionResource\Pages;
use App\Filament\Student\Resources\HomeworkSubmissionResource\RelationManagers;
use App\Models\Assignment;
use App\Models\HomeworkRequest;
use App\Models\AssignmentSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HomeworkSubmissionResource extends Resource
{
    protected static ?string $model = AssignmentSubmission::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 2;

    public static function getModelLabel(): string
    {
        return __('Homeworks Submission');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Homeworks Submissions');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('assignment_id')
                    ->label(__('Homework Request'))
                    ->required()
                    ->options(function () {
                        $studentId = auth()->id();
            
                        if (!$studentId) {
                            return [];
                        }

                        return Assignment::whereHas('subject', function ($query) use ($studentId) {
                            $query->whereHas('schoolClass', function ($subQuery) use ($studentId) {
                                $subQuery->whereHas('studentClasses', function ($subQuery) use ($studentId) {
                                    $subQuery->where('student_id', $studentId)
                                        ->where('status', 'active');
                                });
                            });
                        })
                            ->where('deadline', '>=', now())
                            ->pluck('title', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('file_path')
                    ->label(__('Submission File'))
                    ->directory('assignmentSubmission')
                    ->disk('public')
                    ->openable()
                    ->columnSpanFull()
                    ->downloadable()
                    ->deletable()
                    ->maxSize(2048)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->where('student_id', \Illuminate\Support\Facades\Auth::user()->id)->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('assignment.title')
                    ->label(__('Homework Title'))
                    ->sortable()
                    ->url(fn($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => __(ucfirst($state)))
                    ->colors([
                        'success' => 'reviewed',
                        'primary' => 'pending',
                        'secondary' => 'in_progress',   
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Submit At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListHomeworkSubmissions::route('/'),
            'create' => Pages\CreateHomeworkSubmission::route('/create'),
            'view' => Pages\ViewHomeworkSubmission::route('/{record}'),
            'edit' => Pages\EditHomeworkSubmission::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Homeworks');
    }
}