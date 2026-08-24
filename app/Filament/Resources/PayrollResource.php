<?php

namespace App\Filament\Resources;

use App\Filament\Exports\PayrollExporter;
use App\Filament\Resources\PayrollResource\Pages;
use App\Models\PayrollParent;
use App\Models\Tax;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action as TableAction;
use Morilog\Jalali\Jalalian;

class PayrollResource extends Resource
{
    protected static ?string $model = PayrollParent::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getModelLabel(): string
    {
        return __('Payroll');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Payrolls');
    }
    public static function getNavigationGroup(): string
    {
        return __('Payroll');
    }
    public static function getNavigationLabel(): string
    {
        return __('Payrolls');
    }

    public static function form(Form $form): Form
    {
        // Pashto month names transliterated to English
        $pashtoMonths = [
            1  => 'Wray',       // وری
            2  => 'Ghway',      // غويی
            3  => 'Ghubargolay',// غبرګولی
            4  => 'Changaakh',  // چنګاښ
            5  => 'Zmaray',     // زمری
            6  => 'Waghay',     // وږی
            7  => 'Tala',       // تله
            8  => 'Laram',      // لړم
            9  => 'Linday',     // لیندۍ
            10 => 'Marghomay',  // مرغومی
            11 => 'Sloaghay',   // سلواغه
            12 => 'Kab'         // کب
        ];

        return $form->schema([
            Section::make(__('Payroll Details'))
                ->schema([
                    TextInput::make('title')->label(__('Title'))->required(),

                    Select::make('month')->label(__('Month'))
                        ->options($pashtoMonths)
                        ->default(Jalalian::now()->getMonth())
                        ->searchable()
                        ->required()
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn (Set $set, Get $get) => self::refreshPayrollItems($set, $get)),

                    Select::make('year')->label(__('Year'))
                        ->options(collect(range(1400, 1450))->mapWithKeys(fn($y) => [$y => $y])->toArray())
                        ->default(Jalalian::now()->getYear())
                        ->searchable()
                        ->required()
                        ->native(false)
                        ->live()
                        ->afterStateUpdated(fn (Set $set, Get $get) => self::refreshPayrollItems($set, $get)),

                    Forms\Components\RichEditor::make('description')->label(__('Description'))->columnSpanFull(),

                    Forms\Components\Repeater::make('payrolls')->label(__('Payroll Items'))->relationship('payrolls')->columnSpanFull()
                        ->columns(3)
                        ->helperText(__('Bonus, advance and deductions are loaded from the selected payroll month. Net salary = base + bonus - advance - deductions - tax.'))
                        ->schema([
                            Select::make('teacher_id')->label(__('Teacher'))
                                ->relationship(
                                    'teacher',
                                    'name',
                                    fn (Builder $query) => $query->whereIn('type', ['staff', 'teacher']),
                                )
                                ->getOptionLabelFromRecordUsing(fn (User $record) => trim($record->name.' '.($record->last_name ?? '')).' ('.__(ucfirst((string) $record->type)).')')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                    self::applyPayrollAmounts($set, $state, $get('../../year'), $get('../../month'));
                                }),
                            TextInput::make('base_salary')
                                ->label(__('Base Salary'))
                                ->numeric()
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                    $set('tax', self::calculateTax((float) $state));
                                    self::recalculateNet($get, $set);
                                }),
                            TextInput::make('bonus')
                                ->label(__('Bonus'))
                                ->numeric()
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, Get $get) => self::recalculateNet($get, $set)),
                            TextInput::make('advance')
                                ->label(__('Advance'))
                                ->numeric()
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, Get $get) => self::recalculateNet($get, $set)),
                            TextInput::make('deductions')
                                ->label(__('Deductions'))
                                ->numeric()
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, Get $get) => self::recalculateNet($get, $set)),
                            TextInput::make('tax')
                                ->label(__('Tax'))
                                ->numeric()
                                ->default(0)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, Get $get) => self::recalculateNet($get, $set)),
                            TextInput::make('net_salary')
                                ->label(__('Net Salary'))
                                ->numeric()
                                ->required()
                                ->columnSpanFull(),
                        ]),
                ])->columns(3)
        ]);
    }

    public static function table(Table $table): Table
    {
            // Pashto month names transliterated to English
        $pashtoMonths = [
            1  => 'Wray',       // وری
            2  => 'Ghway',      // غويی
            3  => 'Ghubargolay',// غبرګولی
            4  => 'Changaakh',  // چنګاښ
            5  => 'Zmaray',     // زمری
            6  => 'Waghay',     // وږی
            7  => 'Tala',       // تله
            8  => 'Laram',      // لړم
            9  => 'Linday',     // لیندۍ
            10 => 'Marghomay',  // مرغومی
            11 => 'Sloaghay',   // سلواغه
            12 => 'Kab'         // کب
        ];

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')->label(__('Title'))->searchable()->sortable(),
                TextColumn::make('description')->label(__('Description'))->limit(50)->html()->default(__('N/A')),
                TextColumn::make('month')->label(__('Month'))
                    ->formatStateUsing(fn($state) => $pashtoMonths[$state] ?? $state),
                TextColumn::make('year')->label(__('Year')),
                TextColumn::make('created_at')->label(__('Created At'))
                    ->sortable()
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d')),
            ])
            ->filters([
                Filter::make('year')->form([
                    Select::make('year')
                        ->options(collect(range(1400, 1450))->mapWithKeys(fn($y) => [$y => $y])->toArray())
                        ->native(false),
                ])->query(fn(Builder $q, array $data) => $q->when($data['year'], fn($qr) => $qr->where('year', $data['year']))),

                Filter::make('month')->form([
                    Select::make('month')
                        ->options($pashtoMonths)
                        ->native(false)
                ])->query(fn(Builder $q, array $data) => $q->when($data['month'], fn($qr) => $qr->where('month', $data['month']))),

                Filter::make('created_at_range')->form([
                    DatePicker::make('start_date')->label(__('Start Date')),
                    DatePicker::make('end_date')->label(__('End Date')),
                ])->query(
                    fn(Builder $q, array $data) =>
                    $q->when($data['start_date'], fn($qr) => $qr->whereDate('created_at', '>=', $data['start_date']))
                        ->when($data['end_date'], fn($qr) => $qr->whereDate('created_at', '<=', $data['end_date']))
                ),
            ])
            ->actions([
                TableAction::make('print')->label(__('Print'))->icon('heroicon-o-printer')
                    ->url(fn($record) => route('payrolls.print', ['id' => $record->id]))->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }

    public static function jalaliMonthRange(mixed $year, mixed $month): array
    {
        $year = (int) ($year ?: Jalalian::now()->getYear());
        $month = (int) ($month ?: Jalalian::now()->getMonth());
        $month = max(1, min(12, $month));

        $start = (new Jalalian($year, $month, 1))->toCarbon()->startOfDay();
        $end = (new Jalalian($year, $month, 1))->addMonths()->subDays(1)->toCarbon()->endOfDay();

        return [$start->toDateString(), $end->toDateString()];
    }

    public static function calculateTax(float $baseSalary): float
    {
        $tax = Tax::query()
            ->where('min_amount', '<=', $baseSalary)
            ->where(function (Builder $query) use ($baseSalary): void {
                $query->whereNull('max_amount')
                    ->orWhere('max_amount', '>=', $baseSalary);
            })
            ->orderByDesc('min_amount')
            ->first();

        if (! $tax) {
            return 0;
        }

        return round(
            ($baseSalary * ((float) ($tax->percentage ?? 0)) / 100) + (float) ($tax->fixed_amount ?? 0),
            2,
        );
    }

    public static function computePayrollAmounts(?int $userId, mixed $year, mixed $month): array
    {
        $user = $userId ? User::query()->with(['teacher', 'staff'])->find($userId) : null;
        $baseSalary = (float) ($user?->teacher?->basic_salary ?? $user?->staff?->basic_salary ?? 0);
        [$start, $end] = self::jalaliMonthRange($year, $month);

        $bonuses = $user
            ? (float) $user->bonuses()->whereBetween('date', [$start, $end])->sum('amount')
            : 0;
        $advance = $user
            ? (float) $user->advances()->whereBetween('date', [$start, $end])->sum('amount')
            : 0;
        $deductions = $user
            ? (float) $user->deductions()->whereBetween('date', [$start, $end])->sum('amount')
            : 0;
        $tax = self::calculateTax($baseSalary);

        return [
            'base_salary' => round($baseSalary, 2),
            'bonus' => round($bonuses, 2),
            'advance' => round($advance, 2),
            'deductions' => round($deductions, 2),
            'tax' => round($tax, 2),
            'net_salary' => round($baseSalary + $bonuses - $advance - $deductions - $tax, 2),
        ];
    }

    public static function applyPayrollAmounts(Set $set, mixed $userId, mixed $year, mixed $month): void
    {
        foreach (self::computePayrollAmounts($userId ? (int) $userId : null, $year, $month) as $field => $value) {
            $set($field, $value);
        }
    }

    public static function refreshPayrollItems(Set $set, Get $get): void
    {
        foreach ($get('payrolls') ?? [] as $itemKey => $item) {
            $userId = $item['teacher_id'] ?? null;

            if (blank($userId)) {
                continue;
            }

            foreach (self::computePayrollAmounts((int) $userId, $get('year'), $get('month')) as $field => $value) {
                $set("payrolls.{$itemKey}.{$field}", $value);
            }
        }
    }

    public static function recalculateNet(Get $get, Set $set): void
    {
        $net = (float) ($get('base_salary') ?? 0)
            + (float) ($get('bonus') ?? 0)
            - (float) ($get('advance') ?? 0)
            - (float) ($get('deductions') ?? 0)
            - (float) ($get('tax') ?? 0);

        $set('net_salary', round($net, 2));
    }
}
