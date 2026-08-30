<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentResource\Pages;
use App\Filament\Resources\AssignmentResource\RelationManagers\TeacherRelationManager;
use App\Models\Assignment;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Morilog\Jalali\Jalalian;

class AssignmentResource extends Resource
{
    protected static ?string $model = Assignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('Assignments');
    }

    public static function getLabel(): string
    {
        return __('Assignment');
    }

    public static function getModelLabel(): string
    {
        return __('Assignment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Assignments');
    }

    public static function getNavigationLabel(): string
    {
        return __('Assignment');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                TextInput::make('title')
                    ->label(__('Title'))
                    ->placeholder(__('Title'))
                    ->required()
                    ->maxLength(255),

                Select::make('teacher_id')
                    ->label(__('Teacher'))
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->placeholder(__('Select Teacher')),

                Select::make('subject_id')
                    ->label(__('Subject'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder(__('Select Subject'))
                    ->options(
                        fn(callable $get) =>
                        Subject::where('teacher_id', $get('teacher_id'))->pluck('name', 'id')
                    ),

                DatePicker::make('deadline')
                    ->jalali()              // Enable Jalali calendar
                    ->locale('fa')          // Persian locale (patched labels)
                    ->default(now())        // Default to current date
                    ->required()           // Make it required
                    ->label(__('Deadline')) // Set the label
                    ->displayFormat('Y-m-d') // Optional: Keep display in consistent format
                    ->native(true),

                RichEditor::make('description')
                    ->label(__('Description'))
                    ->columnSpanFull()
                    ->placeholder(__('Enter assignment details')),

                FileUpload::make('file_path')
                    ->label(__('Assignment File'))
                    ->directory('assignments')
                    ->disk('public')
                    ->openable()
                    ->downloadable()
                    ->deletable()
                    ->maxSize(2048)
                    ->helperText(__('Max size: 2MB'))
                    ->columnSpanFull(),
            ])->description(__('Assignment Form'))->collapsed(false)->columns(2)
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->sortable()
                    ->limit(20)
                    ->searchable(),

                TextColumn::make('teacher.name')
                    ->label(__('Teacher'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('subject.name')
                    ->label(__('Subject'))
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->label(__('Description'))
                    ->limit(20)
                    ->html()
                    ->icon('heroicon-m-document-duplicate')
                    ->iconPosition('after')
                    ->copyable()
                    ->copyMessage(__('Description copied'))
                    ->copyMessageDuration(1500)
                    ->tooltip(
                        fn(TextColumn $column): ?string =>
                        strlen(strip_tags(html_entity_decode($column->getState()))) > $column->getCharacterLimit()
                            ? html_entity_decode(strip_tags($column->getState()))
                            : null
                    ),

                TextColumn::make('deadline')
                    ->label(__('Deadline'))
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d H:i') : '-'),

                ImageColumn::make('file_path')
                    ->label(__('File'))
                    ->getStateUsing(fn() => asset('images/file.png'))
                    ->openUrlInNewTab(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d H:i') : '-'),

                TextColumn::make('created_at')
                    ->label(__('Time Since'))
                    ->alignCenter()
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->diffForHumans())
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('Upcoming Deadlines')
                    ->label(__('Upcoming Assignments'))
                    ->query(fn($query) => $query->where('deadline', '>=', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('View')),
                Tables\Actions\EditAction::make()->label(__('Edit')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TeacherRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssignments::route('/'),
            'create' => Pages\CreateAssignment::route('/create'),
            'edit' => Pages\EditAssignment::route('/{record}/edit'),
        ];
    }
}
