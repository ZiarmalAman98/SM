<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParentInvoiceResource\Pages;
use App\Models\InventorySale;
use App\Models\ParentGuardian;
use App\Models\ParentInvoice;
use App\Services\ParentInvoiceBuilder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Morilog\Jalali\Jalalian;

class ParentInvoiceResource extends Resource
{
    protected static ?string $model = ParentInvoice::class;
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string
    {
        return __('Invoices');
    }

    public static function getNavigationLabel(): string
    {
        return __('Invoices');
    }

    public static function getModelLabel(): string
    {
        return __('Invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Invoices');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Invoice Information'))
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label(__('Invoice Number'))
                            ->default(fn() => ParentInvoice::generateInvoiceNumber())
                            ->disabled()
                            ->dehydrated(true),

                        Forms\Components\Select::make('parent_guardian_id')
                            ->label(__('Family Code / Parent'))
                            ->options(fn() => self::parentOptions())
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->disabled(fn(?ParentInvoice $record) => filled($record))
                            ->afterStateUpdated(function ($state, Forms\Get $get, Forms\Set $set): void {
                                $set('inventory_sale_ids', $state ? self::inventorySaleIds((int) $state) : []);
                                self::refreshPreview($get, $set);
                            }),

                        Forms\Components\Select::make('billing_month')
                            ->label(__('Billing Month'))
                            ->options(ParentInvoiceBuilder::MONTHS)
                            ->native(false)
                            ->required()
                            ->live()
                            ->disabled(fn(?ParentInvoice $record) => filled($record))
                            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => self::refreshPreview($get, $set)),

                        Forms\Components\TextInput::make('billing_year')
                            ->label(__('Billing Year'))
                            ->numeric()
                            ->minValue(1400)
                            ->maxValue(1500)
                            ->default((int) Jalalian::now()->getYear())
                            ->required()
                            ->live(onBlur: true)
                            ->disabled(fn(?ParentInvoice $record) => filled($record))
                            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => self::refreshPreview($get, $set)),

                        Forms\Components\Select::make('inventory_sale_ids')
                            ->label(__('Inventory Sales'))
                            ->options(fn(Forms\Get $get) => self::inventorySaleOptions(
                                $get('parent_guardian_id') ? (int) $get('parent_guardian_id') : null,
                            ))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->helperText(__('Select unpaid inventory sales to include in this invoice.'))
                            ->visible(fn(?ParentInvoice $record) => blank($record))
                            ->afterStateUpdated(fn(Forms\Get $get, Forms\Set $set) => self::refreshPreview($get, $set)),

                        Forms\Components\DatePicker::make('invoice_date')
                            ->label(__('Invoice Date'))
                            ->jalali()
                            ->locale('fa')
                            ->default(now())
                            ->required(),

                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('Due Date'))
                            ->jalali()
                            ->locale('fa')
                            ->default(now()->addDays(7)),

                        Forms\Components\TextInput::make('previous_balance')
                            ->label(__('Previous Balance'))
                            ->numeric()
                            ->prefix('AFN ')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0),

                        Forms\Components\TextInput::make('subtotal')
                            ->label(__('Current Month Fees'))
                            ->numeric()
                            ->prefix('AFN ')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0),

                        Forms\Components\TextInput::make('total_amount')
                            ->label(__('Total Invoice'))
                            ->numeric()
                            ->prefix('AFN ')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0),

                        Forms\Components\TextInput::make('balance')
                            ->label(__('Balance'))
                            ->numeric()
                            ->prefix('AFN ')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(0),

                        Forms\Components\Textarea::make('notes')
                            ->label(__('Notes'))
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make(__('Students And Fee Types'))
                    ->schema([
                        Forms\Components\Repeater::make('preview_items')
                            ->label(__('Invoice Lines'))
                            ->dehydrated(false)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->schema([
                                Forms\Components\TextInput::make('student_name')
                                    ->label(__('Student'))
                                    ->disabled(),
                                Forms\Components\TextInput::make('class_name')
                                    ->label(__('Class'))
                                    ->disabled(),
                                Forms\Components\TextInput::make('fee_type_name')
                                    ->label(__('Fee Type'))
                                    ->disabled(),
                                Forms\Components\TextInput::make('description')
                                    ->label(__('Description'))
                                    ->disabled()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('gross_amount')
                                    ->label(__('Gross'))
                                    ->prefix('AFN ')
                                    ->disabled(),
                                Forms\Components\TextInput::make('discount_amount')
                                    ->label(__('Discount'))
                                    ->prefix('AFN ')
                                    ->disabled(),
                                Forms\Components\TextInput::make('amount')
                                    ->label(__('Net Amount'))
                                    ->prefix('AFN ')
                                    ->disabled(),
                            ])
                            ->columns(3)
                            ->visible(fn(?ParentInvoice $record) => blank($record)),

                        Forms\Components\Placeholder::make('invoice_lines')
                            ->label(__('Invoice Lines'))
                            ->content(fn(?ParentInvoice $record) => self::invoiceLinesTable($record))
                            ->columnSpanFull()
                            ->visible(fn(?ParentInvoice $record) => filled($record)),
                    ]),

                Forms\Components\Section::make(__('Payment History'))
                    ->schema([
                        Forms\Components\Placeholder::make('payment_history')
                            ->label(__('Payments'))
                            ->content(fn(?ParentInvoice $record) => self::paymentHistoryTable($record))
                            ->columnSpanFull(),
                    ])
                    ->visible(fn(?ParentInvoice $record) => filled($record)),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('Invoice Information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice_number')
                            ->label(__('Invoice Number'))
                            ->badge(),
                        Infolists\Components\TextEntry::make('family_code')
                            ->label(__('Family Code')),
                        Infolists\Components\TextEntry::make('parentGuardian.user.name')
                            ->label(__('Parent')),
                        Infolists\Components\TextEntry::make('billing_month')
                            ->label(__('Billing Month')),
                        Infolists\Components\TextEntry::make('billing_year')
                            ->label(__('Billing Year')),
                        Infolists\Components\TextEntry::make('invoice_date')
                            ->label(__('Invoice Date'))
                            ->jalaliDate(),
                        Infolists\Components\TextEntry::make('due_date')
                            ->label(__('Due Date'))
                            ->jalaliDate(),
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('Status'))
                            ->badge(),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make(__('Invoice Lines'))
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('student.name')
                                    ->label(__('Student'))
                                    ->default(__('Previous Balance')),
                                Infolists\Components\TextEntry::make('schoolClass.class_name')
                                    ->label(__('Class'))
                                    ->default('-'),
                                Infolists\Components\TextEntry::make('feeType.name')
                                    ->label(__('Fee Type'))
                                    ->default(__('Previous Balance')),
                                Infolists\Components\TextEntry::make('description')
                                    ->label(__('Description'))
                                    ->columnSpan(2),
                                Infolists\Components\TextEntry::make('gross_amount')
                                    ->label(__('Gross'))
                                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                                Infolists\Components\TextEntry::make('discount_amount')
                                    ->label(__('Discount'))
                                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                                Infolists\Components\TextEntry::make('amount')
                                    ->label(__('Net Amount'))
                                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                            ])
                            ->columns(3),
                    ]),

                Infolists\Components\Section::make(__('Totals'))
                    ->schema([
                        Infolists\Components\TextEntry::make('previous_balance')
                            ->label(__('Previous Balance'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                        Infolists\Components\TextEntry::make('subtotal')
                            ->label(__('Current Month Fees'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                        Infolists\Components\TextEntry::make('total_amount')
                            ->label(__('Total Invoice'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                        Infolists\Components\TextEntry::make('paid_amount')
                            ->label(__('Paid Amount'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                        Infolists\Components\TextEntry::make('balance')
                            ->label(__('Balance'))
                            ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                        Infolists\Components\TextEntry::make('notes')
                            ->label(__('Notes'))
                            ->columnSpanFull()
                            ->default('-'),
                    ])
                    ->columns(4),

                Infolists\Components\Section::make(__('Payment History'))
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('payments')
                            ->label('')
                            ->schema([
                                Infolists\Components\TextEntry::make('receipt_number')
                                    ->label(__('Receipt #')),
                                Infolists\Components\TextEntry::make('amount')
                                    ->label(__('Amount'))
                                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN'),
                                Infolists\Components\TextEntry::make('payment_date')
                                    ->label(__('Payment Date'))
                                    ->jalaliDate(),
                                Infolists\Components\TextEntry::make('payment_method')
                                    ->label(__('Method'))
                                    ->badge(),
                                Infolists\Components\TextEntry::make('reference_number')
                                    ->label(__('Reference'))
                                    ->default('-'),
                                Infolists\Components\TextEntry::make('notes')
                                    ->label(__('Notes'))
                                    ->default('-'),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->with(['parentGuardian.user'])
                ->withSum('payments as payments_paid_amount', 'amount'))
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('Invoice #'))
                    ->searchable()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('family_code')
                    ->label(__('Family Code'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('parentGuardian.user.name')
                    ->label(__('Parent'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('billing_month')
                    ->label(__('Month'))
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('billing_year')
                    ->label(__('Year'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('Total'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payments_paid_amount')
                    ->label(__('Paid'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label(__('Balance'))
                    ->formatStateUsing(fn($state) => number_format((float) $state, 2) . ' AFN')
                    ->color(fn(ParentInvoice $record) => $record->balance > 0 ? 'danger' : 'success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge()
                    ->colors([
                        'warning' => 'issued',
                        'info' => 'partial',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('billing_month')
                    ->label(__('Month'))
                    ->options(ParentInvoiceBuilder::MONTHS)
                    ->native(false),

                Tables\Filters\SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options([
                        'issued' => __('Issued'),
                        'partial' => __('Partial'),
                        'paid' => __('Paid'),
                        'cancelled' => __('Cancelled'),
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('add_payment')
                    ->label(__('Add Payment'))
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->url(fn(ParentInvoice $record) => \App\Filament\Resources\ParentInvoicePaymentResource::getUrl('create', [
                        'parent_invoice_id' => $record->id,
                    ]))
                    ->visible(fn(ParentInvoice $record) => $record->balance > 0 && $record->status !== 'cancelled'),
                Tables\Actions\Action::make('print')
                    ->label(__('Print'))
                    ->icon('heroicon-o-printer')
                    ->url(fn(ParentInvoice $record) => route('parent-invoices.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParentInvoices::route('/'),
            'create' => Pages\CreateParentInvoice::route('/create'),
            'view' => Pages\ViewParentInvoice::route('/{record}'),
            'edit' => Pages\EditParentInvoice::route('/{record}/edit'),
        ];
    }

    private static function parentOptions(): array
    {
        return ParentGuardian::with('user')
            ->orderBy('family_code')
            ->get()
            ->mapWithKeys(function (ParentGuardian $parentGuardian) {
                $name = trim(($parentGuardian->user?->name ?? '') . ' ' . ($parentGuardian->user?->last_name ?? ''));

                return [
                    $parentGuardian->id => "{$parentGuardian->family_code} - {$name}",
                ];
            })
            ->all();
    }

    private static function refreshPreview(Forms\Get $get, Forms\Set $set): void
    {
        $preview = app(ParentInvoiceBuilder::class)->preview(
            $get('parent_guardian_id') ? (int) $get('parent_guardian_id') : null,
            $get('billing_month'),
            $get('billing_year') ? (int) $get('billing_year') : null,
            array_map('intval', $get('inventory_sale_ids') ?: []),
        );

        $set('preview_items', $preview['items']);
        $set('previous_balance', $preview['previous_balance']);
        $set('subtotal', $preview['subtotal']);
        $set('total_amount', $preview['total_amount']);
        $set('balance', $preview['balance']);
    }

    private static function invoiceLinesTable(?ParentInvoice $record): HtmlString
    {
        if (! $record) {
            return new HtmlString('');
        }

        $items = $record->items()
            ->with(['student', 'schoolClass', 'feeType'])
            ->get();

        if ($items->isEmpty()) {
            return new HtmlString('<div class="text-sm text-gray-500">No invoice lines found.</div>');
        }

        $rows = $items
            ->map(function ($item, int $index): string {
                $student = e($item->student?->name ?? __('Previous Balance'));
                $class = e($item->schoolClass?->class_name ?? '-');
                $feeType = e($item->feeType?->name ?? __('Previous Balance'));
                $description = e($item->description);
                $grossAmount = e(number_format((float) ($item->gross_amount ?: $item->amount), 2) . ' AFN');
                $discountAmount = e(number_format((float) $item->discount_amount, 2) . ' AFN');
                $amount = e(number_format((float) $item->amount, 2) . ' AFN');
                $number = $index + 1;

                return <<<HTML
                    <tr>
                        <td>{$number}</td>
                        <td>{$student}</td>
                        <td>{$class}</td>
                        <td>{$feeType}</td>
                        <td>{$description}</td>
                        <td style="text-align: right; white-space: nowrap;">{$grossAmount}</td>
                        <td style="text-align: right; white-space: nowrap;">{$discountAmount}</td>
                        <td style="text-align: right; white-space: nowrap;">{$amount}</td>
                    </tr>
                HTML;
            })
            ->implode('');

        return new HtmlString(<<<HTML
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 8px; border: 1px solid #d1d5db;">#</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #d1d5db;">Student</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #d1d5db;">Class</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #d1d5db;">Fee Type</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #d1d5db;">Description</th>
                            <th style="text-align: right; padding: 8px; border: 1px solid #d1d5db;">Gross</th>
                            <th style="text-align: right; padding: 8px; border: 1px solid #d1d5db;">Discount</th>
                            <th style="text-align: right; padding: 8px; border: 1px solid #d1d5db;">Net</th>
                        </tr>
                    </thead>
                    <tbody>{$rows}</tbody>
                </table>
            </div>
        HTML);
    }

    private static function inventorySaleOptions(?int $parentGuardianId): array
    {
        if (! $parentGuardianId) {
            return [];
        }

        return self::unpaidInventorySaleQuery($parentGuardianId)
            ->with('student')
            ->orderByDesc('sale_date')
            ->get()
            ->mapWithKeys(function (InventorySale $sale): array {
                $studentName = trim(($sale->student?->name ?? '') . ' ' . ($sale->student?->last_name ?? ''));
                $customer = $studentName !== '' ? $studentName : ($sale->customer_name ?: __('No customer'));

                return [
                    $sale->id => "{$sale->sale_no} - {$customer} - " . number_format((float) $sale->balance, 2) . ' AFN',
                ];
            })
            ->all();
    }

    private static function inventorySaleIds(int $parentGuardianId): array
    {
        return self::unpaidInventorySaleQuery($parentGuardianId)
            ->orderBy('sale_date')
            ->orderBy('id')
            ->pluck('id')
            ->map(fn($id): int => (int) $id)
            ->all();
    }

    private static function unpaidInventorySaleQuery(int $parentGuardianId): Builder
    {
        return InventorySale::query()
            ->where('parent_guardian_id', $parentGuardianId)
            ->whereNull('parent_invoice_id')
            ->where('status', '!=', 'cancelled')
            ->where('balance', '>', 0)
            ->where(function (Builder $query): void {
                $query->where('payment_destination', 'unpaid')
                    ->orWhere('sale_type', 'admission');
            });
    }

    private static function paymentHistoryTable(?ParentInvoice $record): HtmlString
    {
        if (! $record) {
            return new HtmlString('');
        }

        $payments = $record->payments()
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        if ($payments->isEmpty()) {
            return new HtmlString('<div class="text-sm text-gray-500">No payments recorded yet.</div>');
        }

        $rows = $payments
            ->map(function ($payment): string {
                $receipt = e($payment->receipt_number);
                $amount = e(number_format((float) $payment->amount, 2) . ' AFN');
                $date = e($payment->payment_date ? Jalalian::fromDateTime($payment->payment_date)->format('Y/m/d') : '-');
                $method = e(ucwords(str_replace('_', ' ', (string) $payment->payment_method)));
                $reference = e($payment->reference_number ?? '-');

                return <<<HTML
                    <tr>
                        <td>{$receipt}</td>
                        <td style="text-align: right; white-space: nowrap;">{$amount}</td>
                        <td>{$date}</td>
                        <td>{$method}</td>
                        <td>{$reference}</td>
                    </tr>
                HTML;
            })
            ->implode('');

        return new HtmlString(<<<HTML
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding: 8px; border: 1px solid #d1d5db;">Receipt #</th>
                            <th style="text-align: right; padding: 8px; border: 1px solid #d1d5db;">Amount</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #d1d5db;">Date</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #d1d5db;">Method</th>
                            <th style="text-align: left; padding: 8px; border: 1px solid #d1d5db;">Reference</th>
                        </tr>
                    </thead>
                    <tbody>{$rows}</tbody>
                </table>
            </div>
        HTML);
    }
}
