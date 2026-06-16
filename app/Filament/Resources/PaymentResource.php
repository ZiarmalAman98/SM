<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Payroll;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Morilog\Jalali\Jalalian;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function getModelLabel(): string
    {
        return __('Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Payments');
    }

    public static function getNavigationGroup(): string
    {
        return __('Payroll');
    }

    public static function getNavigationLabel(): string
    {
        return __('Payments');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\Select::make('payroll_parent_id')
                            ->label(__('Payroll Parent'))
                            ->dehydrated(false)
                            ->options(function ($get, $record) {
                                if ($record && $record->payroll_id) {
                                    $payroll = Payroll::find($record->payroll_id);
                                    if ($payroll && $payroll->payroll_parent_id) {
                                        return \App\Models\PayrollParent::where('id', $payroll->payroll_parent_id)->pluck('title', 'id');
                                    }
                                }
                                return \App\Models\PayrollParent::pluck('title', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->default(function ($get, $record) {
                                if ($record && $record->payroll_id) {
                                    $payroll = Payroll::find($record->payroll_id);
                                    return $payroll?->payroll_parent_id;
                                }
                                return null;
                            })
                            ->afterStateUpdated(function ($state, $set) {
                                $set('payroll_id', null);
                                $set('amount', 0);
                            })
                            ->placeholder(__('Select payroll parent')),

                        Forms\Components\Select::make('payroll_id')
                            ->label(__('Payroll'))
                            ->options(function ($get, $record) {
                                $parentId = $get('payroll_parent_id');
                                if (!$parentId && $record && $record->payroll_id) {
                                    $payroll = Payroll::find($record->payroll_id);
                                    $parentId = $payroll?->payroll_parent_id;
                                }
                                if (!$parentId) {
                                    return [];
                                }
                                return Payroll::where('payroll_parent_id', $parentId)
                                    ->with('teacher')
                                    ->get()
                                    ->mapWithKeys(function ($payroll) {
                                        return [
                                            $payroll->id => "{$payroll->teacher->name} ({$payroll->payroll_number})",
                                        ];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->preload()
                            ->live()
                            ->default(function ($get, $record) {
                                return $record?->payroll_id;
                            })
                            ->disabled(fn($get) => !$get('payroll_parent_id'))
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $netSalary = Payroll::find($state)?->net_salary ?? 0;
                                    $set('amount', $netSalary);
                                } else {
                                    $set('amount', 0);
                                }
                            })
                            ->placeholder(__('Select payroll')),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('Payment Amount'))
                            ->numeric()
                            ->step(0.01)
                            ->required()
                            ->prefix('AFN ')
                            ->default(0)
                            ->placeholder(__('Enter amount')),

                        // Jalali DatePicker
                        Forms\Components\DatePicker::make('payment_date')
                            ->jalali()
                            ->locale('fa')
                            ->required()
                            ->default(Carbon::now())
                            ->columnSpanFull()
                            ->placeholder(__('Select payment date'))
                            ->label(__('Payment Date')),

                        Forms\Components\RichEditor::make('details')
                            ->label(__('Payment Details'))
                            ->nullable()
                            ->fileAttachmentsDirectory('payment-details')
                            ->columnSpanFull()
                            ->placeholder(__('Enter payment details')),
                    ])
                    ->description(__('Payment Form'))
                    ->collapsed(false)
                    ->columns(3),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('payroll.payrollParent.title')
                    ->label(__('Payroll Parent'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('payroll.teacher.name')
                    ->label(__('Employee / Teacher'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('payroll.payroll_number')
                    ->label(__('Payroll Number'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->formatStateUsing(fn($state) => 'AFN ' . number_format($state, 2))
                    ->sortable()
                    ->searchable(),

                // Format Jalali date in table column
                Tables\Columns\TextColumn::make('payment_date')
                    ->label(__('Payment Date'))
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return '-';
                        }
                        try {
                            return Jalalian::fromDateTime($state)->format('Y/m/d');
                        } catch (\Exception $e) {
                            return $state;
                        }
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('details')
                    ->label(__('Details'))
                    ->wrap()
                    ->limit(50)
                    ->html()
                    ->searchable()
                    ->toggleable()
                    ->default(__('N/A')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('recent_payments')
                    ->label(__('Recent Payments'))
                    ->query(fn($query) => $query->where('payment_date', '>=', now()->subMonth())),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('Edit')),
                Tables\Actions\DeleteAction::make()->label(__('Delete')),
                Tables\Actions\Action::make('print')
                    ->label(__('Print'))
                    ->icon('heroicon-o-printer')
                    ->url(fn(Payment $record) => route('print.payment', $record)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('Delete Selected')),
                    ExportBulkAction::make()->label(__('Export Selected')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
