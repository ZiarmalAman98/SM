<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentVisitResource\Pages;
use App\Filament\Resources\StudentVisitResource\RelationManagers;
use App\Filament\Resources\StudentVisitResource\Widgets\StudentVisitStatsChart;
use App\Models\VisitorLog;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class StudentVisitResource extends Resource
{
    protected static ?string $model = VisitorLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('Reception');
    }

    public static function getNavigationLabel(): string
    {
        return __('Visitors Logs');
    }

    public static function getModelLabel(): string
    {
        return __('Visitor Log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Visitors Logs');
    }

    public static function getLabel(): string
    {
        return __('Visitor Log');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('Visitor Information'))
                ->schema([TextInput::make('visitor_name')->label(__('Visitor Name'))->required()->maxLength(100), TextInput::make('phone_number')->label(__('Phone Number'))->tel()->maxLength(20)->required(), TextInput::make('email')->label(__('Email'))->email()->maxLength(100)->nullable(), DateTimePicker::make('entry_time')->jalali()->label(__('Entry Time'))->default(now())->required(), DateTimePicker::make('exit_time')->jalali()->label(__('Exit Time'))->nullable()])
                ->columns(2),

            Section::make(__('Meeting Details'))
                ->schema([
                    Select::make('person_to_meet')
                        ->label(__('Person to Meet'))
                        ->relationship(name: 'personToMeet', modifyQueryUsing: fn(Builder $query) => $query->with('roles'))
                        ->getOptionLabelUsing(function ($value) {
                            $user = User::with('roles')->find($value);
                            if (!$user) {
                                return null;
                            }

                            $role = $user->roles->first()?->name;
                            return $role ? ucfirst($role) . ": {$user->name}" : "User: {$user->name}";
                        })
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search) {
                            return User::query()
                                ->with('roles')
                                ->where(function ($query) use ($search) {
                                    $query->where('name', 'like', "%{$search}%")->orWhereHas('roles', fn($q) => $q->where('name', 'like', "%{$search}%"));
                                })
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(function ($user) {
                                    $role = $user->roles->first()?->name;
                                    $label = $role ? ucfirst($role) . ": {$user->name}" : "User: {$user->name}";
                                    return [$user->getKey() => $label];
                                });
                        })
                        ->preload()
                        ->options(function () {
                            return User::with('roles')
                                ->limit(100)
                                ->get()
                                ->mapWithKeys(function ($user) {
                                    $role = $user->roles->first()?->name;
                                    $label = $role ? ucfirst($role) . ": {$user->name}" : "User: {$user->name}";
                                    return [$user->getKey() => $label];
                                });
                        })
                        ->nullable(),

                    TextInput::make('purpose')->label(__('Purpose'))->maxLength(255)->nullable(),
                ])
                ->columns(2),

            Section::make(__('Additional Details'))->schema([
                FileUpload::make('photo_path')
                    ->label(__('Capture Visitor Photo'))
                    ->directory('visitor_photos')
                    ->image()
                    ->imageEditor()
                    ->openable() // This enables camera access
                    ->multiple(false)
                    ->nullable()
                    ->hint(__('Use camera to capture visitor photo (mobile devices only)')),

                Textarea::make('notes')->label(__('Notes'))->rows(3)->maxLength(500)->nullable(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visitor_name')->label(__('Visitor Name'))->sortable()->searchable(),

                TextColumn::make('phone_number')->label(__('Phone Number'))->sortable()->searchable(),

                TextColumn::make('email')->label(__('Email'))->sortable()->searchable()->default(__('No Email')),

                TextColumn::make('entry_time')->label(__('Entry Time'))->jalaliDateTime()->sortable(),

                TextColumn::make('exit_time')
                    ->label(__('Exit Time'))
                    ->jalaliDateTime()
                    ->sortable()
                    ->placeholder(__('Not Checked Out')),

                TextColumn::make('personToMeet.name')->label(__('Person to Meet'))->sortable()->searchable()->default(__('Not Specified')),

                TextColumn::make('purpose')->label(__('Purpose'))->default(__('Not Specified'))->limit(30)->tooltip(fn($record) => $record->purpose)->sortable()->searchable(),

                TextColumn::make('notes')->label(__('Notes'))->limit(50)->tooltip(fn($record) => $record->notes ?? __('No Notes'))->default(__('No Notes'))->searchable(),

                TextColumn::make('created_at')->label(__('Created At'))->dateTime('Y-m-d H:i')->jalaliDateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')->label(__('Updated At'))->dateTime('Y-m-d H:i')->jalaliDateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label(__('Print'))
                    ->icon('heroicon-o-printer')
                    ->url(fn(VisitorLog $record) => route('visitor-logs.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()])
            ->defaultSort('entry_time', 'desc');
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
            'index' => Pages\ListStudentVisits::route('/'),
            'create' => Pages\CreateStudentVisit::route('/create'),
            'view' => Pages\ViewStudentVisit::route('/{record}'),
            'edit' => Pages\EditStudentVisit::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [StudentVisitStatsChart::class];
    }
}
