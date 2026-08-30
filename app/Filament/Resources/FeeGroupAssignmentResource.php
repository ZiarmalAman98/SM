<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeeGroupAssignmentResource\Pages;
use App\Models\FeeGroupAssignment;
use App\Models\SchoolClass;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\MorphToSelect\Type;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\FeeGroup;
use App\Models\FeeDiscount;
use Filament\Forms\Components\Section;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class FeeGroupAssignmentResource extends Resource
{
    protected static ?string $model = FeeGroupAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 7;
    public static function getLabel(): string
    {
        return __('Fees Assignment');
    }

    public static function getModelLabel(): string
    {
        return __('Fees Assignment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Fees Assignments');
    }

    public static function getNavigationLabel(): string
    {
        return __('Fees Assignments');
    }

    public static function getNavigationGroup(): string
    {
        return __('Default Data');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        /* ─ Fee group ─ */
                        Forms\Components\Select::make('fee_group_id')
                            ->label(__('Fee Group'))
                            ->options(FeeGroup::pluck('group_name', 'id'))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->placeholder(__('Select fee group')),

                        /* ─ Effective date ─ */
                        Forms\Components\DatePicker::make('effective_date')
                            ->label(__('Effective Date'))
                            ->jalali()
                            ->default(Carbon::now())
                            ->placeholder(__('Select effective date')),

                        /* ─ Optional discount ─ */
                        Forms\Components\Select::make('fee_discount_id')
                            ->label(__('Fee Discount'))
                            ->options(FeeDiscount::pluck('discount_name', 'id'))
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->nullable()
                            ->columnSpanFull()
                            ->placeholder(__('Select fee discount (optional)'))
                            ->createOptionForm(fn(Form $form) => FeeDiscountResource::form($form)),

                        /* ─ Polymorphic “Assign To” ─ */
                        MorphToSelect::make('assignable')
                            ->label(__('Assign To'))
                            ->types([
                                /* ——— Student (search admission_no OR user.name) ——— */
                                Type::make(Student::class)
                                    ->label(__('Student'))
                                    ->titleAttribute('admission_no')          // any students column
                                    ->getSearchResultsUsing(function (string $search) {
                                        return Student::query()
                                            ->with('user')
                                            ->where('admission_no', 'like', "%{$search}%")
                                            ->orWhereHas('user', fn($q) =>
                                            $q->where('name', 'like', "%{$search}%"))
                                            ->orderBy('admission_no')
                                            ->limit(50)
                                            ->get()
                                            ->mapWithKeys(fn($s) => [
                                                $s->id => sprintf(
                                                    '%s – %s',
                                                    $s->admission_no,
                                                    optional($s->user)->name ?: __('No Name')
                                                ),
                                            ]);
                                    })
                                    ->getOptionLabelUsing(function ($value): string {
                                        $s = Student::with('user')->find($value);
                                        return $s
                                            ? sprintf(
                                                '%s – %s',
                                                $s->admission_no,
                                                optional($s->user)->name ?: __('No Name')
                                            )
                                            : '';
                                    }),

                                /* ——— Class ——— */
                                Type::make(SchoolClass::class)
                                    ->label(__('Class'))
                                    ->titleAttribute('class_name')
                                    ->getSearchResultsUsing(
                                        fn(string $search) =>
                                        SchoolClass::query()
                                            ->where('class_name', 'like', "%{$search}%")
                                            ->orderBy('class_name')
                                            ->limit(50)
                                            ->pluck('class_name', 'id')
                                    )
                                    ->getOptionLabelUsing(
                                        fn($value): string =>
                                        SchoolClass::find($value)?->class_name ?? ''
                                    ),
                            ])
                            ->searchable()
                            ->columnSpanFull()
                            ->required(),
                    ])
                    ->description(__('Fee Group Assignment Form'))
                    ->collapsed(false)
                    ->columns(2),
            ])
            ->columns(3);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('feeGroup.group_name')->label(__('Fee Group'))->sortable()->searchable(),

                Tables\Columns\TextColumn::make('assignable_type')->label(__('Type'))->formatStateUsing(fn(string $state): string => __(class_basename($state))),

                Tables\Columns\TextColumn::make('assignable_name')
                    ->label(__('Name'))
                    ->getStateUsing(function (FeeGroupAssignment $record): string {
                        if (! class_exists($record->assignable_type)) {
                            return __('Invalid assignment target');
                        }

                        $assignable = $record->assignable;

                        return match (class_basename($record->assignable_type)) {
                            'SchoolClass' => $assignable?->class_name ?? '-',
                            'Student' => trim(($assignable?->admission_no ? "{$assignable->admission_no} - " : '') . ($assignable?->user?->name ?? '')) ?: '-',
                            'User' => $assignable?->name ?? '-',
                            default => $assignable?->name ?? $assignable?->title ?? '-',
                        };
                    }),

                Tables\Columns\TextColumn::make('effective_date')->jalaliDate()->label(__('Effective Date')),

                Tables\Columns\TextColumn::make('feeDiscount.discount_name')->label(__('Fee Discount'))->sortable()->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([Tables\Actions\ViewAction::make()->label(__('View')), Tables\Actions\EditAction::make()->label(__('Edit')), Tables\Actions\DeleteAction::make()->label(__('Delete'))])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected'))]);
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
            'index' => Pages\ListFeeGroupAssignments::route('/'),
            'create' => Pages\CreateFeeGroupAssignment::route('/create'),
            'view' => Pages\ViewFeeGroupAssignment::route('/{record}'),
            'edit' => Pages\EditFeeGroupAssignment::route('/{record}/edit'),
        ];
    }
}
