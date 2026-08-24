@php
    use Morilog\Jalali\Jalalian;

    $schoolName = trim($settings['app_name'] ?? config('app.name', 'School Management'));
    $logo = $settings['app_logo_url'] ?? asset('schools/cosmos.png');
    $schoolAddress = $settings['school_address_line'] ?? ($settings['address'] ?? 'Kabul, Afghanistan');
    $schoolContactLine = $settings['support_phone_display'] ?? '';
    $parent = $invoice->parentGuardian?->user;
    $parentName = trim(($parent?->name ?? '') . ' ' . ($parent?->last_name ?? ''));
    $invoiceDate = $invoice->invoice_date ? Jalalian::fromDateTime($invoice->invoice_date)->format('Y/m/d') : '-';
    $dueDate = $invoice->due_date ? Jalalian::fromDateTime($invoice->due_date)->format('Y/m/d') : '-';
    $money = fn ($value) => 'AFN ' . number_format((float) $value, 2);
    $moneyCell = fn ($value) => 'AFN<br><span class="amount-value">' . number_format((float) $value, 2) . '</span>';
@endphp
<main class="page">
    <div class="top-line"></div>

    <header class="header">
        <div class="school">
            <img class="logo" src="{{ $logo }}" alt="School logo">
            <div>
                <h1>{{ $schoolName }}</h1>
                <p>School Fee Invoice</p>
            </div>
        </div>

        <div class="title">
            <div class="en">Parent Invoice</div>
            <div class="local">بل والدین / د والدینو بل</div>
        </div>

        <div class="status">
            <div class="label">Status</div>
            <div class="badge">{{ $invoice->status }}</div>
            <div class="header-note">Printed: {{ Jalalian::now()->format('Y/m/d') }}</div>
        </div>
    </header>

    <section class="meta-grid">
        <div class="box">
            <div class="label">Invoice Number</div>
            <div class="value">{{ $invoice->invoice_number }}</div>
        </div>
        <div class="box">
            <div class="label">Family Code</div>
            <div class="value">{{ $invoice->family_code }}</div>
        </div>
        <div class="box">
            <div class="label">Billing Period</div>
            <div class="value">{{ $invoice->billing_month }} {{ $invoice->billing_year }}</div>
        </div>
        <div class="box">
            <div class="label">Due Date</div>
            <div class="value">{{ $dueDate }}</div>
        </div>
        <div class="box">
            <div class="label">Parent / Guardian</div>
            <div class="value">{{ $parentName ?: '-' }}</div>
        </div>
        <div class="box">
            <div class="label">Invoice Date</div>
            <div class="value">{{ $invoiceDate }}</div>
        </div>
        <div class="box">
            <div class="label">Paid Amount</div>
            <div class="value">{{ $money($invoice->paid_amount) }}</div>
        </div>
        <div class="box">
            <div class="label">Balance</div>
            <div class="value">{{ $money($invoice->balance) }}</div>
        </div>
    </section>

    <div class="section-title">Students And Fee Details</div>
    <table>
        <thead>
            <tr>
                <th style="width: 28px;">#</th>
                <th style="width: 130px;">Student</th>
                <th style="width: 85px;">Class</th>
                <th style="width: 105px;">Fee Type</th>
                <th>Description</th>
                <th style="width: 82px;" class="number">Gross</th>
                <th style="width: 82px;" class="number">Discount</th>
                <th style="width: 88px;" class="number">Net</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td data-label="#">{{ $loop->iteration }}</td>
                    <td data-label="Student">{{ $item->student?->name ?? 'Previous Balance' }}</td>
                    <td data-label="Class">{{ $item->schoolClass?->class_name ?? '-' }}</td>
                    <td data-label="Fee Type">{{ $item->feeType?->name ?? 'Previous Balance' }}</td>
                    <td data-label="Description" class="description">{{ $item->description }}</td>
                    <td data-label="Gross" class="number money-cell">{!! $moneyCell($item->gross_amount ?: $item->amount) !!}</td>
                    <td data-label="Discount" class="number money-cell">{!! $moneyCell($item->discount_amount) !!}</td>
                    <td data-label="Net" class="number money-cell">{!! $moneyCell($item->amount) !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <section class="summary">
        <div>
            <div class="section-title">Notes</div>
            <div class="notes">{{ $invoice->notes ?: 'No notes.' }}</div>
        </div>

        <div class="totals">
            <div class="total-row">
                <strong>Previous Balance</strong>
                <span>{{ $money($invoice->previous_balance) }}</span>
            </div>
            <div class="total-row">
                <strong>Current Month Fees</strong>
                <span>{{ $money($invoice->subtotal) }}</span>
            </div>
            <div class="total-row">
                <strong>Total Invoice</strong>
                <span>{{ $money($invoice->total_amount) }}</span>
            </div>
            <div class="total-row">
                <strong>Paid</strong>
                <span>{{ $money($invoice->paid_amount) }}</span>
            </div>
            <div class="total-row grand">
                <span>Balance Due</span>
                <span>{{ $money($invoice->balance) }}</span>
            </div>
        </div>
    </section>

    @if ($invoice->payments->isNotEmpty())
        <div class="section-title">Payment History</div>
        <table>
            <thead>
                <tr>
                    <th>Receipt #</th>
                    <th class="number">Amount</th>
                    <th>Payment Date</th>
                    <th>Method</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->payments->sortByDesc('payment_date') as $payment)
                    <tr>
                        <td>{{ $payment->receipt_number }}</td>
                        <td class="number money-cell">{!! $moneyCell($payment->amount) !!}</td>
                        <td>{{ $payment->payment_date ? Jalalian::fromDateTime($payment->payment_date)->format('Y/m/d') : '-' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>{{ $payment->reference_number ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <section class="signatures">
        <div class="signature">Parent Signature</div>
        <div class="signature">Cashier</div>
        <div class="signature">Authorized Signature</div>
    </section>

    <footer class="footer">
        <div class="footer-copy">
            <span class="footer-brand">{{ $schoolName }}</span>
            <span>{{ $schoolAddress }}</span>
            <span>{{ $schoolContactLine }}</span>
        </div>
        <div class="footer-contact">
            <span>This invoice is computer generated and valid with school stamp/signature.</span>
            <span>{{ $invoice->invoice_number }}</span>
        </div>
    </footer>
</main>
