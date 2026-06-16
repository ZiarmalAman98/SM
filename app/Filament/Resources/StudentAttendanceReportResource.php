<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentAttendanceReportResource\Pages;
use App\Models\Attendance;
use App\Models\StudentAttendance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class StudentAttendanceReportResource extends Resource
{
    /**
     * The underlying Eloquent model that this Resource corresponds to.
     */
    protected static ?string $model = Attendance::class;

    /**
     * Position in the navigation sidebar.
     */
    protected static ?int $navigationSort = 2;

    /* ---------------------------------------------------------------------
     | Labels & Navigation
     |--------------------------------------------------------------------- */

    public static function getLabel(): string
    {
        return __('Student Report');
    }

    public static function getModelLabel(): string
    {
        return __('Student Report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Student Reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('Student Attendance Reports');
    }

    public static function getNavigationGroup(): string
    {
        return __('Attendance');
    }

    /* ---------------------------------------------------------------------
     | Form (not used – read‑only report)
     |--------------------------------------------------------------------- */

    public static function form(Form $form): Form
    {
        return $form;
    }

    /* ---------------------------------------------------------------------
     | Table
     |--------------------------------------------------------------------- */

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Student name
                Tables\Columns\TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->sortable()
                    ->searchable(),

                // Student father name
                Tables\Columns\TextColumn::make('student.father_name')
                    ->label(__('Father Name'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                // Subject (toggleable)
                Tables\Columns\TextColumn::make('subject.name')
                    ->label(__('Subject'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Date of the attendance entry
                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->jalaliDate()
                    ->sortable(),

                // Hidden text column for exported status (Present / Absent)
                Tables\Columns\TextColumn::make('status_export')
                    ->label(__('Status'))
                    ->state(fn($record) => $record->status ? __('Present') : __('Absent'))
                    ->visible(false),

                // Icon column for visual status indicator
                Tables\Columns\IconColumn::make('status')
                    ->label(__('Status'))
                    ->boolean()
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                // Timestamp (toggleable)
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Recorded At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                /* -------------------------- Date range -------------------------- */
                Filter::make('date')
                    ->label(__('Date Range'))
                    ->form([
                        Forms\Components\DatePicker::make('from')->jalali()->label(__('From')),
                        Forms\Components\DatePicker::make('until')->jalali()->label(__('Until')),
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

                /* -------------------------- Attendance Status -------------------------- */
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Attendance Status'))
                    ->native(false)
                    ->options([
                        1 => __('Present'),
                        0 => __('Absent'),
                    ]),

                /* -------------------------- Subject Filter -------------------------- */
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label(__('Subject'))
                    ->native(false)
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),

                /* -------------------------- Student Filter -------------------------- */
                Tables\Filters\SelectFilter::make('student_id')
                    ->label(__('Student'))
                    ->native(false)
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make(),
            ])
            ->defaultSort('date', 'desc');
    }

    /* ---------------------------------------------------------------------
     | Relations
     |--------------------------------------------------------------------- */

    public static function getRelations(): array
    {
        return [];
    }

    /* ---------------------------------------------------------------------
     | Pages
     |--------------------------------------------------------------------- */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentAttendanceReports::route('/'),
        ];
    }
}
