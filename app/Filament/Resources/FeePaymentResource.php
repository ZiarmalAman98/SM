<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\FeeType;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\FeePayment;
use Filament\Tables\Table;
use App\Models\SchoolClass;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Morilog\Jalali\Jalalian;
use App\Filament\Resources\FeePaymentResource\Pages;
use Illuminate\Database\Eloquent\Builder;

class FeePaymentResource extends Resource
{
    protected static ?string $model = FeePayment::class;
    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return false;
    }

    public static function getLabel(): string
    {
        return __('Fee Payment');
    }

    public static function getModelLabel(): string
    {
        return __('Fee Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Fee Payments');
    }

    public static function getNavigationLabel(): string
    {
        return __('Fee Payments');
    }

    public static function getNavigationGroup(): string
    {
        return __('Finance');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    TextInput::make('receipt_number')
                        ->label(__('Receipt Number'))
                        ->default(fn() => date('md') . '-' . rand(1000, 9999))
                        ->unique('fee_payments', 'receipt_number', ignoreRecord: true)
                        ->disabled()
                        ->dehydrated(true)
                        ->required()
                        ->columnSpanFull(),

                    Select::make('student_id')
                        ->label(__('Student'))
                        ->options(
                            User::where('type', 'student')
                                ->with('student')
                                ->get()
                                ->mapWithKeys(function ($user) {
                                    $admissionNo = optional($user->student)->admission_no ?? 'N/A';
                                    return [
                                        $user->id => "{$admissionNo} - {$user->name}",
                                    ];
                                }),
                        )
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search) {
                            return User::where('type', 'student')
                                ->with('student')
                                ->where(function ($query) use ($search) {
                                    $query
                                        ->whereHas('student', function ($q) use ($search) {
                                            $q->where('admission_no', 'like', "%{$search}%");
                                        })
                                        ->orWhere('name', 'like', "%{$search}%");
                                })
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(function ($user) {
                                    $admissionNo = optional($user->student)->admission_no ?? 'N/A';
                                    return [
                                        $user->id => "{$admissionNo} - {$user->name}",
                                    ];
                                });
                        })
                        ->required()
                        ->reactive()
                        ->placeholder(__('Select student')),

                    Select::make('class_id')
                        ->label(__('Class'))
                        ->options(
                            fn(Get $get) => SchoolClass::whereHas('studentClasses', function ($query) use ($get) {
                                $query->where('student_id', $get('student_id'));
                                $query->where('status', 'active');
                            })->pluck('class_name', 'id'),
                        )
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->placeholder(__('Select class')),

                    Select::make('fee_type_id')
                        ->label(__('Fees Type'))
                        ->relationship('feeType', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->placeholder(__('Select fee type'))
                        ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set) {
                            $feeType = FeeType::find($state);
                            $set('total_fees', $feeType ? $feeType->default_amount : null);
                        }),

                    TextInput::make('total_fees')
                        ->label(__('Total Fees'))
                        ->numeric()
                        ->required()
                        ->prefix('AFN '),

                    TextInput::make('amount_paid')
                        ->label(__('Amount Paid'))
                        ->numeric()
                        ->reactive()
                        ->prefix('AFN ')
                        ->afterStateUpdated(fn($state, Forms\Get $get, Forms\Set $set) => $set('balance', max(0, $get('total_fees') - $state)))
                        ->required(),

                    Select::make('month')
                        ->label(__('Month (Hijri/Shamsi)'))
                        ->native(false)
                        ->options([
                            'Hamal'   => 'Hamal',
                            'Sawr'    => 'Sawr',
                            'Jawza'   => 'Jawza',
                            'Saratan' => 'Saratan',
                            'Asad'    => 'Asad',
                            'Sunbula' => 'Sunbula',
                            'Mizan'   => 'Mizan',
                            'Aqrab'   => 'Aqrab',
                            'Qaws'    => 'Qaws',
                            'Jadi'    => 'Jadi',
                            'Dalw'    => 'Dalw',
                            'Hoot'    => 'Hoot',
                        ])
                        ->required()
                        ->placeholder(__('Select month'))
                        ->reactive(),

                    Forms\Components\DatePicker::make('payment_date')
                        ->label(__('Payment Date'))
                        ->jalali()
                        ->locale('fa')
                        ->default(now())
                        ->required()
                        ->columnSpanFull(),
                ])
                ->description(__('Fee Payment Form'))
                ->collapsed(false)
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('receipt_number')
                    ->label(__('Receipt #'))
                    ->searchable()
                    ->badge()
                    ->sortable(),

                TextColumn::make('student.name')
                    ->label(__('Student'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('student.father_name')
                    ->label(__('Father Name'))
                    ->searchable()
                    ->default('-'),

                TextColumn::make('class.class_name')
                    ->label(__('Class'))
                    ->sortable(),

                TextColumn::make('feeType.name')
                    ->label(__('Fee Type'))
                    ->sortable(),

                TextColumn::make('total_fees')
                    ->label(__('Total Fees'))
                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' AFN')
                    ->sortable(),

                TextColumn::make('amount_paid')
                    ->label(__('Amount Paid'))
                    ->formatStateUsing(fn($state) => number_format($state, 2) . ' AFN')
                    ->sortable(),

                TextColumn::make('due_payment')
                    ->label(__('Due Payment'))
                    ->formatStateUsing(fn($record) => number_format($record->due_payment, 2) . ' AFN')
                    ->color(fn($record) => $record->due_payment > 0 ? 'danger' : 'success'),

                BadgeColumn::make('month')
                    ->label(__('Month (Hijri/Shamsi)'))
                    ->formatStateUsing(function ($state, $record) {
                        // If already Hijri, show as is
                        $hijri = ['Hamal', 'Sawr', 'Jawza', 'Saratan', 'Asad', 'Sunbula', 'Mizan', 'Aqrab', 'Qaws', 'Jadi', 'Dalw', 'Hoot'];
                        if (in_array($state, $hijri, true)) {
                            return $state;
                        }

                        // Fallback: derive from payment_date if legacy English month is stored
                        if ($record?->payment_date) {
                            $j = Jalalian::fromDateTime($record->payment_date);
                            $map = [
                                1  => 'Hamal',
                                2  => 'Sawr',
                                3  => 'Jawza',
                                4  => 'Saratan',
                                5  => 'Asad',
                                6  => 'Sunbula',
                                7  => 'Mizan',
                                8  => 'Aqrab',
                                9  => 'Qaws',
                                10 => 'Jadi',
                                11 => 'Dalw',
                                12 => 'Hoot',
                            ];
                            return $map[$j->getMonth()] ?? $state;
                        }

                        return $state;
                    })
                    ->colors([
                        'Hamal'   => 'blue',
                        'Sawr'    => 'green',
                        'Jawza'   => 'orange',
                        'Saratan' => 'purple',
                        'Asad'    => 'yellow',
                        'Sunbula' => 'gray',
                        'Mizan'   => 'pink',
                        'Aqrab'   => 'teal',
                        'Qaws'    => 'red',
                        'Jadi'    => 'cyan',
                        'Dalw'    => 'lime',
                        'Hoot'    => 'indigo',
                    ]),

                TextColumn::make('payment_date')
                    ->label(__('Payment Date'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->formatStateUsing(fn($state) => Jalalian::fromDateTime($state)->format('Y/m/d H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('print_receipt')
                    ->label(__('Print Receipt'))
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('fee_payments.print_receipt', ['fee_payment_id' => [$record->id]]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->label(__('Delete Selected')),

                BulkAction::make('print_receipt')
                    ->label(__('Print Receipt'))
                    ->action(function (\Illuminate\Support\Collection $records, array $data) {
                        $feeIds = $records->pluck('id')->toArray();

                        if (empty($feeIds)) {
                            Notification::make()
                                ->title(__('No item selected'))
                                ->danger()
                                ->send();
                            return;
                        }

                        return redirect()->route('fee_payments.print_receipt', ['fee_payment_id' => $feeIds]);
                    }),
            ])
            ->filters([
                TernaryFilter::make('due_payments')
                    ->label(__('Show Due Payments'))
                    ->native(false)
                    ->trueLabel(__('Only Due Payments'))
                    ->falseLabel(__('Fully Paid'))
                    ->queries(
                        true: fn($query) => $query->whereRaw('total_fees > amount_paid'),
                        false: fn($query) => $query->whereRaw('total_fees = amount_paid')
                    ),

                SelectFilter::make('student_id')
                    ->native(false)
                    ->label(__('Student'))
                    ->options(
                        User::where('type', 'student')
                            ->with('student')
                            ->get()
                            ->mapWithKeys(function ($user) {
                                $admissionNo = optional($user->student)->admission_no ?? 'N/A';
                                return [
                                    $user->id => "{$admissionNo} - {$user->name}",
                                ];
                            }),
                    )
                    ->searchable()
                    ->query(function (Builder $query, $data) {
                        if (!empty($data['value'])) {
                            $query->where('student_id', $data['value']);
                        }
                    }),

                SelectFilter::make('month')
                    ->label(__('Month (Hijri/Shamsi)'))
                    ->native(false)
                    ->options([
                        'Hamal'   => 'Hamal',
                        'Sawr'    => 'Sawr',
                        'Jawza'   => 'Jawza',
                        'Saratan' => 'Saratan',
                        'Asad'    => 'Asad',
                        'Sunbula' => 'Sunbula',
                        'Mizan'   => 'Mizan',
                        'Aqrab'   => 'Aqrab',
                        'Qaws'    => 'Qaws',
                        'Jadi'    => 'Jadi',
                        'Dalw'    => 'Dalw',
                        'Hoot'    => 'Hoot',
                    ]),

                SelectFilter::make('class_id')
                    ->label(__('Class'))
                    ->native(false)
                    ->options(SchoolClass::pluck('class_name', 'id')),

                Tables\Filters\Filter::make('payment_date')
                    ->label(__('Payment Date'))
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('From'))
                            ->jalali()
                            ->locale('fa'),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('Until'))
                            ->jalali()
                            ->locale('fa'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if ($data['from'] && $data['until']) {
                            $from = Jalalian::fromDateTime($data['from'])->format('Y/m/d');
                            $until = Jalalian::fromDateTime($data['until'])->format('Y/m/d');
                            return __('From :from to :until', ['from' => $from, 'until' => $until]);
                        }

                        if ($data['from']) {
                            $from = Jalalian::fromDateTime($data['from'])->format('Y/m/d');
                            return __('From :date', ['date' => $from]);
                        }

                        if ($data['until']) {
                            $until = Jalalian::fromDateTime($data['until'])->format('Y/m/d');
                            return __('Until :date', ['date' => $until]);
                        }

                        return null;
                    }),
            ])
            ->defaultSort('payment_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [];
    }
}
