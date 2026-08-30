<?php

namespace App\Filament\Student\Resources;

use App\Filament\Student\Resources\StudentClassResource\Pages;
use App\Models\SchoolClass;
use App\Models\StudentClass;
use App\Models\Subject;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentClassResource extends Resource
{
    protected static ?string $model = StudentClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = ('My Classes');
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('Academic');
    }

    public static function getLabel(): string
    {
        return __('My Class');
    }
    
    // public static function navigationLabel(): string
    // {
    //     return __('My Classes');
    // }

    public static function getPluralModelLabel(): string
    {
        return __('My Classes');
    }

    public static function getNavigationLabel(): string
    {
        return __('My Classes');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->where('student_id', Auth::id())->orderBy('created_at', 'desc'))
            ->columns([
                Tables\Columns\TextColumn::make('schoolClass.class_name')
                    ->label(__('Subject Class'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('academic_year')
                    ->label(__('Academic Year'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('Status'))
                    ->sortable()
                    ->formatStateUsing(fn(string $state): string => __(ucfirst($state)))
                    ->colors([
                        'success' => 'active',
                        'warning' => 'completed',
                        'danger' => 'transferred',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Join At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Add any filters you may need
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentClasses::route('/'),
            'view' => Pages\ViewStudentClass::route('/{record}'),
        ];
    }

    public static function infoList(Infolist $infoList): Infolist
    {
        $user = Auth::user();

        $studentClass = DB::table('student_classes')
            ->where('student_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$studentClass) {
            return $infoList->schema([]);
        }

        $class = SchoolClass::find($studentClass->class_id);

        if (!$class) {
            return $infoList->schema([]);
        }

        $subjects = Subject::with(['teacher', 'schedules'])
            ->where('school_class_id', $class->id)
            ->get();

        return $infoList->schema([
            Tabs::make(__('Details'))
                ->tabs([
                    Tabs\Tab::make(__('Class Information'))
                        ->icon('heroicon-o-book-open')
                        ->schema([
                            Section::make(__('Subject Schedule'))
                                ->schema([
                                    RepeatableEntry::make('schoolClass.subjects')
                                        ->label(__('Subjects & Schedule'))
                                        ->schema([
                                            TextEntry::make('name')
                                                ->label(__('Subject')),

                                            TextEntry::make('teacher.name')
                                                ->label(__('Teacher')),

                                            RepeatableEntry::make('schedules')
                                                ->label(__('Schedule'))
                                                ->schema([
                                                    TextEntry::make('day_of_week')->label(__('Day')),
                                                    TextEntry::make('start_time')->label(__('Start Time')),
                                                    TextEntry::make('end_time')->label(__('End Time')),
                                                ])
                                                ->columns(3)
                                                ->columnSpanFull(),
                                        ])
                                        ->default($subjects->toArray())
                                        ->columns(2),
                                ]),
                        ])
                ])
                ->columnSpanFull()
        ]);
    }
}