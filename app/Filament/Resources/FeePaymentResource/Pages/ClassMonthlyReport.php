<?php

namespace App\Filament\Resources\FeePaymentResource\Pages;

use App\Filament\Resources\FeePaymentResource;
use App\Models\FeeGroupAssignment;
use App\Models\FeePayment;
use App\Models\FeeType;
use App\Models\SchoolClass;
use App\Models\User;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use Filament\Notifications\Notification;

use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;

use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Resources\Pages\Page as ResourcePage;

class ClassMonthlyReport extends ResourcePage implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = FeePaymentResource::class;
    protected static string $view = 'filament.resources.fee-payment-resource.pages.class-monthly-report';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public ?int $class_id = null;
    public ?string $month = null;
    public ?int $fee_type_id = null;

    public function getTitle(): string|Htmlable
    {
        return __('Class Monthly Fee Report');
    }

    public function mount(): void
    {
        $this->form->fill();
        $j = Jalalian::fromCarbon(now());
        $this->month = $this->hijriMonths()[$j->getMonth()] ?? null;
    }

    protected function hijriMonths(): array
    {
        return [
            1 => 'Hamal',
            2 => 'Sawr',
            3 => 'Jawza',
            4 => 'Saratan',
            5 => 'Asad',
            6 => 'Sunbula',
            7 => 'Mizan',
            8 => 'Aqrab',
            9 => 'Qaws',
            10 => 'Jadi',
            11 => 'Dalw',
            12 => 'Hoot',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make(__('Filters'))
                ->icon('heroicon-o-funnel')
                ->schema([
                    Grid::make(['default' => 1, 'md' => 3])
                        ->schema([
                            Forms\Components\Select::make('class_id')
                                ->label(__('Class'))
                                ->native(false)
                                ->options(fn() => SchoolClass::pluck('class_name', 'id'))
                                ->searchable()
                                ->required()
                                ->reactive(),

                            Forms\Components\Select::make('month')
                                ->label(__('Month (Hijri/Shamsi)'))
                                ->native(false)
                                ->options([
                                    'Hamal' => 'Hamal',
                                    'Sawr' => 'Sawr',
                                    'Jawza' => 'Jawza',
                                    'Saratan' => 'Saratan',
                                    'Asad' => 'Asad',
                                    'Sunbula' => 'Sunbula',
                                    'Mizan' => 'Mizan',
                                    'Aqrab' => 'Aqrab',
                                    'Qaws' => 'Qaws',
                                    'Jadi' => 'Jadi',
                                    'Dalw' => 'Dalw',
                                    'Hoot' => 'Hoot',
                                ])
                                ->required()
                                ->reactive(),

                            Forms\Components\Select::make('fee_type_id')
                                ->label(__('Fee Type (Optional)'))
                                ->native(false)
                                ->options(fn() => FeeType::pluck('name', 'id'))
                                ->searchable()
                                ->placeholder(__('All types'))
                                ->reactive(),
                        ]),
                ])
                ->footerActions([
                    FormAction::make('generate')
                        ->label(__('Generate Report'))
                        ->icon('heroicon-o-funnel')
                        ->color('primary')
                        ->action('generateReport'),
                ]),
        ];
    }

    public function generateReport(): void
    {
        $data = $this->form->getState();

        $this->class_id = $data['class_id'];
        $this->month = $data['month'];
        $this->fee_type_id = $data['fee_type_id'] ?? null;

        if (!$this->class_id || !$this->month) {
            Notification::make()
                ->title(__('Please select Class and Month'))
                ->danger()
                ->send();
            return;
        }

        // Refresh the table
        $this->resetTable();

        Notification::make()
            ->title(__('Report generated successfully'))
            ->success()
            ->send();
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query($this->getBaseQuery())
            ->columns([
                TextColumn::make('student.admission_no')
                    ->label(__('Admission #'))
                    ->sortable()
                    ->searchable()
                    ->placeholder('N/A'),

                TextColumn::make('name')
                    ->label(__('Student'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('father_name')
                    ->label(__('Father Name'))
                    ->searchable()
                    ->default('-'),

                TextColumn::make('total_fees_for_month')
                    ->label(__('Total Fees (Month)'))
                    ->getStateUsing(fn($record) => $this->sumFor($record->id, 'total_fees'))
                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' AFN'),

                TextColumn::make('amount_paid_for_month')
                    ->label(__('Amount Paid (Month)'))
                    ->getStateUsing(fn($record) => $this->sumFor($record->id, 'amount_paid'))
                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' AFN'),

                TextColumn::make('balance_for_month')
                    ->label(__('Balance'))
                    ->getStateUsing(function ($record) {
                        $total = $this->sumFor($record->id, 'total_fees');
                        $paid  = $this->sumFor($record->id, 'amount_paid');
                        return max(0, $total - $paid);
                    })
                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' AFN')
                    ->color(function ($record) {
                        $total = $this->sumFor($record->id, 'total_fees');
                        $paid  = $this->sumFor($record->id, 'amount_paid');
                        return $paid >= $total && $total > 0 ? 'success' : ($paid > 0 ? 'warning' : 'danger');
                    }),

                BadgeColumn::make('status_for_month')
                    ->label(__('Status'))
                    ->getStateUsing(function ($record) {
                        $total = $this->sumFor($record->id, 'total_fees');
                        $paid  = $this->sumFor($record->id, 'amount_paid');
                        if ($total <= 0 && $paid <= 0) return __('Uninvoiced');
                        if ($paid <= 0) return __('Unpaid');
                        if ($paid < $total) return __('Partial');
                        return __('Paid');
                    })
                    ->colors([
                        'Uninvoiced' => 'gray',
                        'Unpaid'     => 'danger',
                        'Partial'    => 'warning',
                        'Paid'       => 'success',
                    ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Export'))
                    ->color('primary')
                    ->url(fn() => $this->getExportUrl())
                    ->openUrlInNewTab(),
            ])
            ->striped()
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(25);
    }

    protected function getExportUrl(): string
    {
        if (!$this->class_id || !$this->month) {
            return '#';
        }

        $params = [
            'class_id' => $this->class_id,
            'month' => $this->month,
        ];

        if ($this->fee_type_id) {
            $params['fee_type_id'] = $this->fee_type_id;
        }

        return route('fee-payments.export-class-report', $params);
    }

    protected function getBaseQuery(): Builder
    {
        if (!$this->class_id) {
            return User::query()->whereRaw('1=0');
        }

        return User::query()
            ->where('type', 'student')
            ->with(['student'])
            ->whereHas('studentClasses', function (Builder $q) {
                $q->where('class_id', $this->class_id)->where('status', 'active');
            });
    }

    protected function sumFor(int $studentId, string $field): float
    {
        if (!$this->class_id || !$this->month) return 0.0;

        $q = FeePayment::query()
            ->where('student_id', $studentId)
            ->where('class_id', $this->class_id)
            ->where('month', $this->month);

        if ($this->fee_type_id) {
            $q->where('fee_type_id', $this->fee_type_id);
        }

        $fromPayments = (float) $q->sum($field);

        // If asking for total_fees and no payment record exists yet,
        // fall back to the fee amount assigned to this class via FeeGroupAssignment
        if ($field === 'total_fees' && $fromPayments === 0.0) {
            return $this->getAssignedFeeForClass();
        }

        return $fromPayments;
    }

    protected function getAssignedFeeForClass(): float
    {
        $assignment = FeeGroupAssignment::with(['feeGroup.feeGroupFeeTypes'])
            ->where('assignable_type', SchoolClass::class)
            ->where('assignable_id', $this->class_id)
            ->first();

        if (!$assignment) return 0.0;

        $feeTypes = $assignment->feeGroup->feeGroupFeeTypes;

        if ($this->fee_type_id) {
            $feeTypes = $feeTypes->where('fee_type_id', $this->fee_type_id);
        }

        return (float) $feeTypes->sum('amount');
    }
}
