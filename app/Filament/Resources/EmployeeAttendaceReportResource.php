<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeAttendaceReportResource\Pages;
use App\Filament\Resources\EmployeeAttendaceReportResource\RelationManagers;
use App\Models\EmployeeAttendaceReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\EmployeeAttendance;
use Filament\Tables\Filters\Filter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class EmployeeAttendaceReportResource extends Resource
{
    protected static ?string $model = EmployeeAttendance::class;
    protected static ?int $navigationSort = 6;
    public static function getLabel(): string
    {
        return __('Employee Report');
    }

    public static function getModelLabel(): string
    {
        return __('Employee Report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Employee Reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('Attendance Reports');
    }

    public static function getNavigationGroup(): string
    {
        return __('Attendance');
    }


    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                // Define the form schema if needed
                // In this case, you might want to define fields related to employee attendance
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.name')
                    ->label(__('Employee'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('date')
                    ->label(__('Date'))
                    ->jalaliDate()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status_export')
                    ->label(__('Status'))
                    ->state(fn($record) => $record->status ? __('Present') : __('Absent'))
                    ->visible(false), // hidden from table UI, shown only in export

                Tables\Columns\IconColumn::make('status')
                    ->label(__('Status'))
                    ->boolean()
                    ->trueIcon('heroicon-m-check-circle')
                    ->falseIcon('heroicon-m-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Recorded At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter by date range
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

                // Filter by status
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Attendance Status'))
                    ->native(false)
                    ->options([
                        1 => __('Present'),
                        0 => __('Absent'),
                    ]),

                // Optional: Filter by employee
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label(__('Employee'))
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->relationship('employee', 'name'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make()
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Define relations if necessary (e.g., for attendance history, etc.)
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeAttendaceReports::route('/'),
        ];
    }
}
