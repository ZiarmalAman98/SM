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
                        ->native(false),

                    Select::make('year')->label(__('Year'))
                        ->options(collect(range(1400, 1450))->mapWithKeys(fn($y) => [$y => $y])->toArray())
                        ->default(Jalalian::now()->getYear())
                        ->searchable()
                        ->required()
                        ->native(false),

                    Forms\Components\RichEditor::make('description')->label(__('Description'))->columnSpanFull(),

                    Forms\Components\Repeater::make('payrolls')->label(__('Payroll Items'))->relationship('payrolls')->columnSpanFull()
                        ->columns(3)->schema([
                            Select::make('teacher_id')->label(__('Teacher'))
                                ->relationship('teacher', 'name')->searchable()->required()
                                ->afterStateUpdated(function ($state, $set) {
                                    $teacher = User::find($state);
                                    $fixSalary = $teacher?->teacher?->basic_salary ?? $teacher?->staff?->basic_salary;
                                    $set('base_salary', $fixSalary ?? 0);

                                    $bonuses = $teacher?->bonuses()->whereYear('date', now()->year)->whereMonth('date', now()->month)->sum('amount');
                                    $advance = $teacher?->advances()->whereYear('date', now()->year)->whereMonth('date', now()->month)->sum('amount');
                                    $deductions = $teacher?->deductions()->whereYear('date', now()->year)->whereMonth('date', now()->month)->sum('amount');

                                    $set('bonus', $bonuses);
                                    $set('advance', $advance);
                                    $set('deductions', $deductions);

                                    $tax = Tax::where('min_amount', '<=', $fixSalary)->where('max_amount', '>=', $fixSalary)->first();
                                    $calculatedTax = $tax ? (($fixSalary * ($tax->percentage ?? 0) / 100) + ($tax->fixed_amount ?? 0)) : 0;
                                    $set('tax', round($calculatedTax, 2));

                                    $net = $fixSalary + $bonuses - $advance - $deductions - $calculatedTax;
                                    $set('net_salary', round($net, 2));
                                }),
                            TextInput::make('base_salary')->label(__('Base Salary'))->numeric()->required(),
                            TextInput::make('bonus')->label(__('Bonus'))->numeric(),
                            TextInput::make('advance')->label(__('Advance'))->numeric(),
                            TextInput::make('deductions')->label(__('Deductions'))->numeric(),
                            TextInput::make('tax')->label(__('Tax'))->numeric(),
                            TextInput::make('net_salary')->label(__('Net Salary'))->numeric()->required()->columnSpanFull(),
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
}
