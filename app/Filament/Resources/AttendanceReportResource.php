<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceReportResource\Pages;
use App\Filament\Resources\AttendanceReportResource\RelationManagers;
use App\Models\Attendance;
use App\Models\AttendanceReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AttendanceReportResource extends Resource
{
    protected static ?string $model = Attendance::class;
protected static ?int $navigationSort = 2;
public static function getNavigationGroup(): string
{
    return __('Attendance');
}
public static function getNavigationLabel(): string
{
    return __('Student Attendance Reports');
}


public static function getLabel(): string
{
    return __('Student Attendance Report');
}


public static function getModelLabel(): string
{
    return __('Student Report');
}

public static function getPluralModelLabel(): string
{
    return __('Student Reports');
}



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->sortable()
                    ->searchable()
                    ->tooltip(fn ($record) => $record->student->name),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label(__('Teacher'))
                    ->sortable()
                    ->searchable()
                    ->tooltip(fn ($record) => $record->teacher->name),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label(__('Subject'))
                    ->sortable()
                    ->searchable()
                    ->tooltip(fn ($record) => $record->subject->name),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->date()
                    ->sortable()
                    ->tooltip(fn ($record) => $record->date->format('l, F j, Y')),

                Tables\Columns\TextColumn::make('status_export')
                    ->label(__('Status'))
                    ->state(fn($record) => $record->status ? __('Present') : __('Absent'))
                    ->visible(false),

                Tables\Columns\IconColumn::make('status')
                    ->label(__('Status'))
                    ->boolean()
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->status ? __('Present') : __('Absent')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Recorded At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('date')
                    ->label(__('Date Range'))
                    ->form([
                        Forms\Components\DatePicker::make('from')->label(__('From')),
                        Forms\Components\DatePicker::make('until')->label(__('Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label(__('Subject'))
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder(__('All Subjects')),

                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Attendance Status'))
                    ->options([
                        1 => __('Present'),
                        0 => __('Absent'),
                    ])
                    ->placeholder(__('All Statuses')),

                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label(__('Teacher'))
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder(__('All Teachers')),

                Tables\Filters\SelectFilter::make('student_id')
                    ->label(__('Student'))
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder(__('All Students')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label(__('Delete Selected')),
                ExportBulkAction::make()
                    ->label(__('Export Selected'))
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading(__('No records found'))
            ->emptyStateDescription(__('Create your first record by clicking the button below.'))
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Create Record')),
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
            'index' => Pages\ListAttendanceReports::route('/'),
            // 'create' => Pages\CreateAttendanceReport::route('/create'),
            // 'edit' => Pages\EditAttendanceReport::route('/{record}/edit'),
        ];
    }
}
