<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubjectResource\Pages;
use App\Models\SchoolClass;
use App\Models\Subject;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Forms\Components\{TextInput, Select, Repeater, Section, TimePicker};
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;
    protected static ?string $navigationIcon = 'heroicon-o-bookmark-square';
    // protected static ?string $navigationGroup = 'Default Data';
    protected static ?int $navigationSort = 1;
public static function getNavigationGroup(): string
{
    return __('Default Data');
}

public static function getNavigationLabel(): string
{
    return __('Subjects');
}

public static function getModelLabel(): string
{
    return __('Subject');
}

public static function getPluralModelLabel(): string
{
    return __('Subjects');
}

public static function getLabel(): string
{
    return __('Subject');
}
   public static function form(Forms\Form $form): Forms\Form
{
    return $form
        ->schema([
            Section::make()->schema([
                TextInput::make('name')
                    ->label(__('Subject Name'))
                    ->required()
                    ->maxLength(255),

                Select::make('school_class_id')
                    ->label(__('Class'))
                    ->relationship('schoolClass', 'class_name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm(fn(Form $form) => SchoolClassResource::form($form)),

                Select::make('teacher_id')
                    ->label(__('Teacher'))
                    ->options(options: fn() => \App\Models\User::where('type', 'teacher')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                Repeater::make('schedules')
                    ->label(__('Class Schedule'))
                    ->relationship('schedules')
                    ->schema([
                        Select::make('day_of_week')
                            ->label(__('Day of the Week'))
                            ->native(false)
                            ->options([
                                'Monday' => __('Monday'),
                                'Tuesday' => __('Tuesday'),
                                'Wednesday' => __('Wednesday'),
                                'Thursday' => __('Thursday'),
                                'Friday' => __('Friday'),
                                'Saturday' => __('Saturday'),
                                'Sunday' => __('Sunday'),
                            ])
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->required(),

                        TimePicker::make('start_time')
                            ->label(__('Start Time'))
                            ->required(),

                        TimePicker::make('end_time')
                            ->label(__('End Time'))
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $startTime = $get('start_time');
                                if ($startTime && $state <= $startTime) {
                                    $set('end_time', null);
                                    Notification::make()
                                        ->title(__('Invalid End Time'))
                                        ->body(__('End time cannot be before start time.'))
                                        ->danger()
                                        ->send();
                                }
                            }),
                    ])
                    ->minItems(1)
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(3),
            ])->description(__("Subject Form"))->collapsed(false)->columns(2)
        ])->columns(3);
}

  public static function table(Tables\Table $table): Tables\Table
{
    return $table
        ->columns([
            TextColumn::make('name')
                ->label(__('Subject Name'))
                ->searchable()
                ->sortable(),

            TextColumn::make('schoolClass.class_name')
                ->label(__('Class'))
                ->sortable(),

            TextColumn::make('teacher.name')
                ->label(__('Teacher'))
                ->sortable(),

            BadgeColumn::make('schedules_count')
                ->label(__('Schedules'))
                ->counts('schedules')
                ->color('primary'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('school_class_id')
                ->label(__('Filter by Class'))
                ->relationship('schoolClass', 'class_name')
                ->searchable()
                ->preload(),

            Tables\Filters\SelectFilter::make('teacher_id')
                ->label(__('Filter by Teacher'))
                ->options(options: fn() => \App\Models\User::where('type', 'teacher')->pluck('name', 'id'))
                ->searchable()
                ->preload(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSubjects::route('/'),
            'create' => Pages\CreateSubject::route('/create'),
            'edit' => Pages\EditSubject::route('/{record}/edit'),
        ];
    }
}
