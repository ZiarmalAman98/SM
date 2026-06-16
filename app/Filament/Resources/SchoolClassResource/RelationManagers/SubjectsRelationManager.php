<?php

namespace App\Filament\Resources\SchoolClassResource\RelationManagers;

use App\Filament\Resources\SchoolClassResource;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'subjects';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Subject Name')
                    ->required()
                    ->maxLength(255),

                Select::make('teacher_id')
                    ->label('Teacher')
                    ->options(options: fn() => \App\Models\User::where('type', 'teacher')->pluck('name', 'id')) // Filter only students
                    ->searchable()
                    ->preload()
                    ->required(),

                // Repeater for subject schedule
                Repeater::make('schedules')
                    ->label('Class Schedule')
                    ->relationship('schedules')
                    ->schema([
                        Select::make('day_of_week')
                            ->label('Day of the Week')
                            ->native(false)
                            ->options([
                                'Monday' => 'Monday',
                                'Tuesday' => 'Tuesday',
                                'Wednesday' => 'Wednesday',
                                'Thursday' => 'Thursday',
                                'Friday' => 'Friday',
                                'Saturday' => 'Saturday',
                                'Sunday' => 'Sunday',
                            ])
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->required(),

                        TimePicker::make('start_time')
                            ->label('Start Time')
                            ->required(),

                        TimePicker::make('end_time')
                            ->label('End Time')
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $startTime = $get('start_time'); // Get the selected start time

                                if ($startTime && $state <=  $startTime) {
                                    $set('end_time', null); // Reset end_time if invalid
                                    Notification::make()
                                        ->title('Invalid End Time')
                                        ->body('End time cannot be before start time.')
                                        ->danger()
                                        ->send();
                                }
                            }),
                    ])
                    ->minItems(1)
                    ->collapsible()
                    ->columnSpanFull()
                    ->columns(3),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('name')
                    ->label('Subject Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('schoolClass.class_name')
                    ->label('Class')
                    ->sortable(),

                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->sortable(),

                BadgeColumn::make('schedules_count')
                    ->label('Schedules')
                    ->counts('schedules')
                    ->color('primary'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
