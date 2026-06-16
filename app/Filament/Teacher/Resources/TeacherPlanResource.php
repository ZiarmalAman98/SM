<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\TeacherPlanResource\Pages;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\TeacherPlan;
use App\Exports\TeacherPlanWordExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class TeacherPlanResource extends Resource
{
    protected static ?string $model = TeacherPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Academic';
    protected static ?int    $navigationSort  = 30;

    public static function getEloquentQuery(): Builder
    {
        // Scope everything to the logged-in teacher
        return parent::getEloquentQuery()
            ->where('teacher_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Plan Information')->schema([
                // Hidden: auto-fill teacher_id
                Forms\Components\Hidden::make('teacher_id')
                    ->default(fn() => Auth::id())
                    ->required(),

                Forms\Components\Select::make('subject_id')
                    ->label('Subject')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('class_id')
                    ->label('Class')
                    ->relationship('schoolClass', 'class_name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\DatePicker::make('date')
                    ->required()
                    ->default(now())
                    ->jalali()
                    ->maxDate(now()),

                Forms\Components\TextInput::make('student_count')
                    ->label('Number of Students')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(65535),
                Forms\Components\TextInput::make('topic_covered')
                    ->label('Topic Covered')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),


                Forms\Components\Textarea::make('details')
                    ->label('Lesson Details')
                    ->rows(4)
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                // Teacher filter removed (always current user)
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
                        Forms\Components\DatePicker::make('date_from')->jalali()->label('From Date'),
                        Forms\Components\DatePicker::make('date_until')->jalali()->label('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['date_from'] ?? null, fn(Builder $q, $d) => $q->whereDate('date', '>=', $d))
                            ->when($data['date_until'] ?? null, fn(Builder $q, $d) => $q->whereDate('date', '<=', $d));
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
            ->headerActions([
                // Export to Word for class/year/month (no teacher selector)
                Tables\Actions\Action::make('exportToWord')
                    ->label('Export to Word')
                    ->icon('heroicon-o-document-arrow-down')
                    ->modalHeading('Export Teacher Plan to Word')
                    ->form([
                        Forms\Components\Select::make('class_id')
                            ->label('Class')
                            ->options(
                                fn() =>
                                SchoolClass::query()
                                    ->orderBy('class_name')
                                    ->pluck('class_name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('year')
                            ->label('Year (Solar Hijri)')
                            ->options(function () {
                                $y = Jalalian::fromCarbon(now())->getYear();
                                $years = [];
                                foreach (range($y, $y - 4) as $yy) {
                                    $years[(string) $yy] = (string) $yy;
                                }
                                return $years;
                            })
                            ->default(fn() => (string) Jalalian::fromCarbon(now())->getYear())
                            ->required(),

                        Forms\Components\Select::make('month')
                            ->label('Month (Solar Hijri)')
                            ->options([
                                '01' => 'Hamal',
                                '02' => 'Sawar',
                                '03' => 'Jawza',
                                '04' => 'Saratan',
                                '05' => 'Asad',
                                '06' => 'Sunbula',
                                '07' => 'Mizan',
                                '08' => 'Aqrab',
                                '09' => 'Qaws',
                                '10' => 'Jadi',
                                '11' => 'Dalwa',
                                '12' => 'Hoot',
                            ])
                            ->default(fn() => str_pad((string) Jalalian::fromCarbon(now())->getMonth(), 2, '0', STR_PAD_LEFT))
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // Uses the class-only export you already have
                        $export = new TeacherPlanWordExport(
                            (int) $data['class_id'],
                            (int) $data['year'],
                            (string) $data['month']
                        );

                        return $export->download(
                            "teacher-plan-class{$data['class_id']}-{$data['year']}-{$data['month']}.docx"
                        );
                    }),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTeacherPlans::route('/'),
            'create' => Pages\CreateTeacherPlan::route('/create'),
            'edit'   => Pages\EditTeacherPlan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }
}
