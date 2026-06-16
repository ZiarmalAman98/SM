@php
    use Morilog\Jalali\Jalalian;

    $schoolName = $settings['app_name'] ?? config('app.name', 'School Management');
    $logo = isset($settings['app_logo']) && filled($settings['app_logo'])
        ? asset('storage/' . $settings['app_logo'])
        : asset('schools/cosmos.png');
    $parent = $invoice->parentGuardian?->user;
    $parentName = trim(($parent?->name ?? '') . ' ' . ($parent?->last_name ?? ''));
    $invoiceDate = $invoice->invoice_date ? Jalalian::fromDateTime($invoice->invoice_date)->format('Y/m/d') : '-';
    $dueDate = $invoice->due_date ? Jalalian::fromDateTime($invoice->due_date)->format('Y/m/d') : '-';
    $money = fn ($value) => 'AFN ' . number_format((float) $value, 2);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->invoice_number }} - Parent Invoice</title>
    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #eef2f6;
            color: #172033;
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            line-height: 1.45;
        }

        .toolbar {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 18px;
        }

        .toolbar button {
            border: 0;
            border-radius: 6px;
            padding: 10px 18px;
            background: #155e75;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
        }

        .toolbar button.secondary {
            background: #475569;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 24px;
            background: #fff;
            padding: 14mm;
            border: 1px solid #d8dee8;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        }

        .top-line {
            height: 6px;
            background: #155e75;
            margin: -14mm -14mm 18px;
        }

        .header {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 18px;
            border-bottom: 2px solid #155e75;
            padding-bottom: 14px;
        }

        .school {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 68px;
            height: 68px;
            object-fit: contain;
        }

        .school h1 {
            margin: 0;
            color: #0f172a;
            font-size: 24px;
            font-weight: 800;
        }

        .school p,
        .header-note {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .title {
            text-align: center;
        }

        .title .en {
            color: #155e75;
            font-size: 23px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .title .local {
            margin-top: 3px;
            color: #334155;
            font-size: 16px;
            font-weight: 700;
        }

        .status {
            justify-self: end;
            min-width: 145px;
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border: 1px solid #f59e0b;
            border-radius: 4px;
            background: #fffbeb;
            color: #92400e;
            font-weight: 700;
            text-transform: uppercase;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 18px 0;
        }

        .box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
            background: #f8fafc;
        }

        .label {
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .value {
            margin-top: 4px;
            color: #0f172a;
            font-size: 14px;
            font-weight: 700;
        }

        .section-title {
            margin: 20px 0 8px;
            color: #155e75;
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #155e75;
            color: #fff;
            font-size: 11px;
            padding: 8px 7px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            border: 1px solid #cbd5e1;
            padding: 8px 7px;
            vertical-align: top;
        }

        tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .number {
            text-align: right;
            white-space: nowrap;
        }

        .description {
            color: #334155;
        }

        .summary {
            display: grid;
            grid-template-columns: 1fr 84mm;
            gap: 16px;
            margin-top: 16px;
            align-items: start;
        }

        .notes {
            min-height: 96px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px;
        }

        .totals {
            border: 1px solid #155e75;
            border-radius: 6px;
            overflow: hidden;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 12px;
            border-bottom: 1px solid #dbe4ee;
        }

        .total-row:last-child {
            border-bottom: 0;
        }

        .grand {
            background: #155e75;
            color: #fff;
            font-size: 16px;
            font-weight: 800;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 34px;
        }

        .signature {
            padding-top: 36px;
            border-top: 1px solid #0f172a;
            text-align: center;
            color: #475569;
            font-weight: 700;
        }

        .footer {
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px solid #cbd5e1;
            color: #64748b;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            font-size: 11px;
        }

        @media print {
            body {
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .toolbar {
                display: none !important;
            }

            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                border: 0;
                box-shadow: none;
            }

            .top-line {
                margin: 0 0 18px;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()">Print Invoice</button>
        <button class="secondary" onclick="window.close()">Close</button>
    </div>

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
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->student?->name ?? 'Previous Balance' }}</td>
                        <td>{{ $item->schoolClass?->class_name ?? '-' }}</td>
                        <td>{{ $item->feeType?->name ?? 'Previous Balance' }}</td>
                        <td class="description">{{ $item->description }}</td>
                        <td class="number">{{ $money($item->gross_amount ?: $item->amount) }}</td>
                        <td class="number">{{ $money($item->discount_amount) }}</td>
                        <td class="number">{{ $money($item->amount) }}</td>
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
                            <td class="number">{{ $money($payment->amount) }}</td>
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
            <span>This invoice is computer generated and valid with school stamp/signature.</span>
            <span>{{ $invoice->invoice_number }}</span>
        </footer>
    </main>
</body>

</html>
