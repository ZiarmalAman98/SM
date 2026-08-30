<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParentInvoicePaymentResource\Pages;
use App\Models\ParentInvoice;
use App\Models\ParentInvoicePayment;
use App\Services\ParentInvoiceBuilder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Morilog\Jalali\Jalalian;

class ParentInvoicePaymentResource extends Resource
{
    protected static ?string $model = ParentInvoicePayment::class;
    // protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): string
    {
        return __('Finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('Invoice Payments');
    }

    public static function getModelLabel(): string
    {
        return __('Invoice Payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Invoice Payments');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Payment Information'))
                    ->schema([
                        Forms\Components\TextInput::make('receipt_number')
                            ->label(__('Receipt Number'))
                            ->default(fn() => ParentInvoicePayment::generateReceiptNumber())
                            ->unique('parent_invoice_payments', 'receipt_number', ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(true)
                            ->required(),

                        Forms\Components\Select::make('parent_invoice_id')
                            ->label(__('Invoice'))
                            ->options(fn(?ParentInvoicePayment $record) => self::invoiceOptions($record))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->default(fn() => request()->integer('parent_invoice_id') ?: null)
                            ->disabled(fn(?ParentInvoicePayment $record) => filled($record))
                            ->afterStateUpdated(function ($state, Forms\Set $set): void {
                                $invoice = ParentInvoice::find($state);
                                $set('invoice_total', $invoice?->total_amount ?? 0);
                                $set('already_paid', $invoice?->paid_amount ?? 0);
                                $set('remaining_balance', self::familyOutstandingBalance($invoice));
                                $set('amount', $invoice?->balance ?? 0);
                            }),

                        Forms\Components\TextInput::make('invoice_total')
                            ->label(__('Invoice Total'))
                            ->prefix('AFN ')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn(?ParentInvoicePayment $record) => self::selectedInvoice($record)?->total_amount ?? 0),

                        Forms\Components\TextInput::make('already_paid')
                            ->label(__('Already Paid'))
                            ->prefix('AFN ')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn(?ParentInvoicePayment $record) => self::selectedInvoice($record)?->paid_amount ?? 0),

                        Forms\Components\TextInput::make('remaining_balance')
                            ->label(__('Family Remaining Balance'))
                            ->prefix('AFN ')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn(?ParentInvoicePayment $record) => $record
                                ? (self::familyOutstandingBalance($record->invoice) + (float) $record->amount)
                                : self::familyOutstandingBalance(self::selectedInvoice($record))),

                        Forms\Components\TextInput::make('amount')
                            ->label(__('Payment Amount'))
                            ->prefix('AFN ')
                            ->numeric()
                            ->rules(['numeric', 'min:0.01'])
                            ->default(fn(?ParentInvoicePayment $record) => $record?->amount ?? self::selectedInvoice($record)?->balance ?? 0)
                            ->required(),

                        Forms\Components\DatePicker::make('payment_date')
                            ->label(__('Payment Date'))
                            ->jalali()
                            ->locale('fa')
                            ->default(now())
                            ->required(),

                        Forms\Components\Select::make('payment_method')
                            ->label(__('Payment Method'))
                            ->options([
                                'cash' => __('Cash'),
                                'bank' => __('Bank'),
                                'mobile_money' => __('Mobile Money'),
                                'card' => __('Card'),
                                'other' => __('Other'),
                            ])
                            ->native(false)
                            ->default('cash')
                            ->required(),

                        Forms\Components\TextInput::make('reference_number')
                            ->label(__('Reference Number'))
                            ->maxLength(255),

                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['invoice.parentGuardian.user']))
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label(__('Receipt #'))
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('invoice.invoice_number')
                    ->label(__('Invoice #'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice.family_code')
                    ->label(__('Family Code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice.parentGuardian.user.name')
                    ->label(__('Parent'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('invoice.billing_month')
                    ->label(__('Month'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('invoice.billing_year')
                    ->label(__('Year'))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label(__('Amount'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('Method'))
                    ->badge()
                    ->formatStateUsing(fn($state) => __(str_replace('_', ' ', ucfirst((string) $state)))),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label(__('Payment Date'))
                    ->formatStateUsing(fn($state) => $state ? Jalalian::fromDateTime($state)->format('Y/m/d') : '-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('reference_number')
                    ->label(__('Reference'))
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label(__('Payment Method'))
                    ->options([
                        'cash' => __('Cash'),
                        'bank' => __('Bank'),
                        'mobile_money' => __('Mobile Money'),
                        'card' => __('Card'),
                        'other' => __('Other'),
                    ])
                    ->native(false),
                Tables\Filters\SelectFilter::make('billing_month')
                    ->label(__('Billing Month'))
                    ->options(\App\Services\ParentInvoiceBuilder::MONTHS)
                    ->native(false)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] ?? null,
                            fn(Builder $query, string $month): Builder => $query->whereHas(
                                'invoice',
                                fn(Builder $query): Builder => $query->where('billing_month', $month),
                            ),
                        );
                    }),
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
                                $data['from'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('print')
                    ->label(__('Print'))
                    ->icon('heroicon-o-printer')
                    ->url(fn(ParentInvoicePayment $record) => route('parent-invoice-payments.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('payment_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParentInvoicePayments::route('/'),
            'create' => Pages\CreateParentInvoicePayment::route('/create'),
            'view' => Pages\ViewParentInvoicePayment::route('/{record}'),
            'edit' => Pages\EditParentInvoicePayment::route('/{record}/edit'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Receipt Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('receipt_number')
                            ->label(__('Receipt Number'))
                            ->badge(),
                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('Paid Amount'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                        Infolists\Components\TextEntry::make('payment_date')
                            ->label(__('Payment Date'))
                            ->jalaliDate(),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label(__('Payment Method'))
                            ->badge()
                            ->formatStateUsing(fn($state) => __(ucwords(str_replace('_', ' ', (string) $state)))),
                        Infolists\Components\TextEntry::make('reference_number')
                            ->label(__('Reference Number'))
                            ->default('-'),
                        Infolists\Components\TextEntry::make('notes')
                            ->label(__('Notes'))
                            ->default('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Infolists\Components\Section::make(__('Invoice Details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice.invoice_number')
                            ->label(__('Invoice Number'))
                            ->badge(),
                        Infolists\Components\TextEntry::make('invoice.family_code')
                            ->label(__('Family Code')),
                        Infolists\Components\TextEntry::make('invoice.parentGuardian.user.name')
                            ->label(__('Parent')),
                        Infolists\Components\TextEntry::make('invoice.billing_month')
                            ->label(__('Billing Month')),
                        Infolists\Components\TextEntry::make('invoice.billing_year')
                            ->label(__('Billing Year')),
                        Infolists\Components\TextEntry::make('invoice.total_amount')
                            ->label(__('Invoice Total'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                        Infolists\Components\TextEntry::make('invoice.paid_amount')
                            ->label(__('Total Paid'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                        Infolists\Components\TextEntry::make('invoice.balance')
                            ->label(__('Remaining Balance'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                        Infolists\Components\TextEntry::make('invoice.status')
                            ->label(__('Invoice Status'))
                            ->badge(),
                    ])
                    ->columns(3),
            ]);
    }

    private static function invoiceOptions(?ParentInvoicePayment $record = null): array
    {
        return ParentInvoice::with(['parentGuardian.user'])
            ->where(function (Builder $query) use ($record) {
                $query
                    ->where(function (Builder $query): void {
                        $query
                            ->where('status', '!=', 'cancelled')
                            ->where('balance', '>', 0);
                    })
                    ->when($record?->parent_invoice_id, fn(Builder $query, int $invoiceId) => $query->orWhere('id', $invoiceId));
            })
            ->orderByDesc('id')
            ->get()
            ->mapWithKeys(function (ParentInvoice $invoice) {
                $parentName = trim(($invoice->parentGuardian?->user?->name ?? '') . ' ' . ($invoice->parentGuardian?->user?->last_name ?? ''));
                $balance = number_format((float) $invoice->balance, 2);

                return [
                    $invoice->id => "{$invoice->invoice_number} - {$invoice->family_code} - {$parentName} ({$balance} AFN)",
                ];
            })
            ->all();
    }

    public static function familyOutstandingBalance(?ParentInvoice $invoice): float
    {
        if (! $invoice) {
            return 0;
        }

        return (float) ParentInvoice::query()
            ->where('parent_guardian_id', $invoice->parent_guardian_id)
            ->where('status', '!=', 'cancelled')
            ->where('balance', '>', 0)
            ->sum('balance');
    }

    public static function billingMonthNumber(string $month): int
    {
        $index = array_search($month, array_keys(ParentInvoiceBuilder::MONTHS), true);

        return $index === false ? 0 : $index + 1;
    }

    private static function selectedInvoice(?ParentInvoicePayment $record): ?ParentInvoice
    {
        if ($record?->invoice) {
            return $record->invoice;
        }

        $invoiceId = request()->integer('parent_invoice_id');

        return $invoiceId ? ParentInvoice::find($invoiceId) : null;
    }
}
