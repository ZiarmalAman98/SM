<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Branch;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClassAttendance;
use Filament\Resources\Resource;

use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\ClassAttendanceReportResource\Pages;

// ✅ NEW: imports for Excel & models
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClassAttendanceByClassExport;
use App\Models\SchoolClass; // <-- adjust if your model name differs (e.g., ClassModel)

class ClassAttendanceReportResource extends Resource
{
    protected static ?string $model = ClassAttendance::class;

    public static function getNavigationGroup(): string
    {
        return __('Attendance');
    }

    public static function getModelLabel(): string
    {
        return __('Class Attendance Report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Class Attendance Reports');
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getNavigationLabel(): string
    {
        return __('Class Attendance Report');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('schoolClass.class_name') // ✅ Corrected relationship
                    ->label(__('Class'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('branch.branch_name')
                    ->label(__('Branch'))
                    ->state(fn($record) => $record->schoolClass?->branch?->branch_name)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('date')
                    ->label(__('Date'))
                    ->jalaliDate()
                    ->sortable(),

                TextColumn::make('status_export')
                    ->label(__('Status'))
                    ->state(fn($record) => match ($record->detailed_status) {
                        'present' => __('Present'),
                        'absent' => __('Absent'),
                        'partial' => __('Partial'),
                        'sick' => __('Sick'),
                        'leave' => __('Leave'),
                        default => __('Unknown'),
                    })
                    ->visible(false), // For export only

                BadgeColumn::make('detailed_status')
                    ->label(__('Status'))
                    ->colors([
                        'success' => 'present',
                        'danger' => 'absent',
                        'warning' => 'partial',
                        'info' => 'sick',
                        'secondary' => 'leave',
                    ])
                    ->formatStateUsing(fn($record) => match ($record->detailed_status) {
                        'present' => __('Present'),
                        'absent' => __('Absent'),
                        'partial' => __('Partial'),
                        'sick' => __('Sick'),
                        'leave' => __('Leave'),
                        default => __('Unknown'),
                    })
                    ->sortable(),

                TextColumn::make('leave_reason')
                    ->label(__('Leave Reason'))
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sick_reason')
                    ->label(__('Sick Reason'))
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('leave_type')
                    ->label(__('Leave Type'))
                    ->formatStateUsing(fn($state) => match ($state) {
                        'personal' => __('Personal'),
                        'family' => __('Family'),
                        'emergency' => __('Emergency'),
                        'other' => __('Other'),
                        default => __('N/A'),
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('leave_start_date')
                    ->label(__('Leave Start'))
                    ->jalaliDate()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('leave_end_date')
                    ->label(__('Leave End'))
                    ->jalaliDate()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('Recorded At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('date')
                    ->label(__('Date Range'))
                    ->form([
                        DatePicker::make('from')->jalali()->label(__('From')),
                        DatePicker::make('until')->jalali()->label(__('Until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['until'] ?? null, fn($q, $date) => $q->whereDate('date', '<=', $date));
                    }),

                SelectFilter::make('status')
                    ->label(__('Attendance Status'))
                    ->options([
                        'present' => __('Present'),
                        'absent' => __('Absent'),
                        'partial' => __('Partial'),
                        'sick' => __('Sick'),
                        'leave' => __('Leave'),
                    ]),

                SelectFilter::make('student_id')
                    ->label(__('Student'))
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('class_id') // ✅ must match actual DB column
                    ->label(__('Class'))
                    ->relationship('schoolClass', 'class_name') // ✅ must match fixed model method
                    ->searchable()
                    ->preload(),

                Filter::make('branch')
                    ->label(__('Branch'))
                    ->form([
                        Forms\Components\Select::make('branch_id')
                            ->label(__('Branch'))
                            ->options(Branch::pluck('branch_name', 'id')->toArray())
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['branch_id'] ?? null, function ($q, $branchId) {
                            $q->whereHas('schoolClass', fn($q) => $q->where('branch_id', $branchId));
                        });
                    }),

                Filter::make('leave_sick')
                    ->label(__('Leave & Sick Status'))
                    ->form([
                        Forms\Components\Select::make('leave_sick_status')
                            ->label(__('Status'))
                            ->options([
                                'leave' => __('On Leave'),
                                'sick' => __('Sick'),
                                'both' => __('Both Leave & Sick'),
                                'none' => __('Neither Leave nor Sick'),
                            ])
                            ->searchable(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['leave_sick_status'] ?? null, function ($q, $status) {
                            match ($status) {
                                'leave' => $q->where('is_leave', true),
                                'sick' => $q->where('is_sick', true),
                                'both' => $q->where(function ($query) {
                                    $query->where('is_leave', true)->orWhere('is_sick', true);
                                }),
                                'none' => $q->where('is_leave', false)->where('is_sick', false),
                                default => $q,
                            };
                        });
                    }),

                SelectFilter::make('leave_type')
                    ->label(__('Leave Type'))
                    ->options([
                        'personal' => __('Personal'),
                        'family' => __('Family'),
                        'emergency' => __('Emergency'),
                        'other' => __('Other'),
                    ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->headerActions([
                // Existing simple link (keep if you still need it)
                // Action::make('export')
                //     ->label(__('Export Attendance'))
                //     ->icon('heroicon-m-arrow-down-tray')
                //     ->url(fn() => route('export.class.attendance'))
                //     ->openUrlInNewTab(),

                // ✅ NEW: Export by Class & Date Range — downloads directly
                Action::make('export_by_class')
                    ->label(__('Export by Class'))
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->form([
                        Select::make('class_id')
                            ->label(__('Class'))
                            ->options(fn() => SchoolClass::orderBy('class_name')->pluck('class_name', 'id'))
                            ->searchable()
                            ->required(),

                        DatePicker::make('from')
                            ->label(__('From'))
                            ->jalali()
                            ->native(false),

                        DatePicker::make('until')
                            ->label(__('Until'))
                            ->jalali()
                            ->native(false),
                    ])
                    ->action(function (array $data) {
                        // Optional: basic guard to ensure valid range if both provided
                        if (!empty($data['from']) && !empty($data['until']) && $data['from'] > $data['until']) {
                            throw new \Exception(__('Invalid date range: "From" must be before or equal to "Until".'));
                        }

                        $filename = 'class-attendance-'
                            . ($data['class_id'] ?? 'all')
                            . '-' . now()->format('Ymd_His') . '.xlsx';

                        return Excel::download(
                            new ClassAttendanceByClassExport(
                                classId: (int) $data['class_id'],
                                from: $data['from'] ?? null,
                                until: $data['until'] ?? null
                            ),
                            $filename
                        );
                    }),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassAttendanceReports::route('/'),
        ];
    }
}
