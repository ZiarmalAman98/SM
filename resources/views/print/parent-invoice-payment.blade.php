@php
    use Morilog\Jalali\Jalalian;

    $invoice = $payment->invoice;
    $parent = $invoice?->parentGuardian?->user;
    $parentName = trim(($parent?->name ?? '') . ' ' . ($parent?->last_name ?? ''));
    $schoolName = trim($settings['app_name'] ?? config('app.name', 'School Management'));
    $logo = $settings['app_logo_url'] ?? asset('schools/cosmos.png');
    $schoolAddress = $settings['school_address_line'] ?? ($settings['address'] ?? 'Kabul, Afghanistan');
    $schoolContactLine = $settings['support_phone_display'] ?? '';
    $money = fn ($value) => 'AFN ' . number_format((float) $value, 2);
    $moneyCell = fn ($value) => 'AFN<br><span class="amount-value">' . number_format((float) $value, 2) . '</span>';
    $paymentDate = $payment->payment_date ? Jalalian::fromDateTime($payment->payment_date)->format('Y/m/d') : '-';
    $items = $invoice?->items ?? collect();
    $paidBeforeThisReceipt = $invoice
        ? (float) $invoice->payments
            ->where('id', '!=', $payment->id)
            ->filter(fn ($otherPayment) => $otherPayment->payment_date < $payment->payment_date || ($otherPayment->payment_date == $payment->payment_date && $otherPayment->id < $payment->id))
            ->sum('amount')
        : 0;
    $receiptRemaining = (float) $payment->amount;
    $priorPaidRemaining = $paidBeforeThisReceipt;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $payment->receipt_number }} - Invoice Payment Receipt</title>
    <style>
        :root {
            --brand: #c8642c;
            --brand-dark: #9f4d20;
            --brand-soft: #fff4ec;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #e5d4c7;
            --panel: #fffaf6;
        }

        @page {
            size: A4;
            margin: 14mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: linear-gradient(180deg, #f7ede5 0%, #f4f5f7 100%);
            color: var(--ink);
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
            background: var(--brand);
            color: #fff;
            cursor: pointer;
            font-weight: 700;
            box-shadow: 0 10px 20px rgba(200, 100, 44, 0.18);
        }

        .toolbar button.secondary {
            background: #6b7280;
        }

        .receipt {
            width: min(190mm, calc(100vw - 24px));
            min-height: 250mm;
            margin: 0 auto 24px;
            background: #fff;
            padding: 14mm;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 24px 50px rgba(77, 37, 12, 0.12);
        }

        .top-line {
            height: 8px;
            margin: -14mm -14mm 18px;
            background: linear-gradient(90deg, var(--brand-dark), var(--brand));
            border-radius: 18px 18px 0 0;
        }

        .header {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 18px;
            border-bottom: 2px solid rgba(200, 100, 44, 0.2);
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
            color: #101828;
            font-size: 23px;
            font-weight: 800;
        }

        .muted {
            color: var(--muted);
            font-size: 12px;
            overflow-wrap: anywhere;
        }

        .title {
            text-align: center;
        }

        .title .en {
            color: var(--brand-dark);
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .title .local {
            margin-top: 3px;
            color: #7c2d12;
            font-size: 15px;
            font-weight: 700;
        }

        .receipt-number {
            justify-self: end;
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border: 1px solid #f0b486;
            border-radius: 4px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .amount-box {
            margin: 22px 0;
            padding: 18px;
            border: 2px solid rgba(200, 100, 44, 0.25);
            border-radius: 14px;
            background: linear-gradient(180deg, var(--brand-soft), #fff);
            text-align: center;
        }

        .amount-box .label {
            color: var(--brand-dark);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .amount-box .amount {
            margin-top: 4px;
            color: #0f172a;
            font-size: clamp(26px, 4vw, 34px);
            font-weight: 900;
            line-height: 1.15;
            overflow-wrap: anywhere;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }

        .box {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px 12px;
            background: var(--panel);
            min-width: 0;
        }

        .label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .value {
            margin-top: 4px;
            color: #111827;
            font-size: 14px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .section-title {
            margin: 20px 0 8px;
            color: var(--brand-dark);
            font-size: 15px;
            font-weight: 800;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th {
            width: 34%;
            background: #fff4ec;
            color: #7c2d12;
            font-size: 12px;
            text-align: left;
            padding: 9px;
            border: 1px solid var(--line);
            overflow-wrap: anywhere;
        }

        .line-table {
            table-layout: fixed;
        }

        .line-table th {
            width: auto;
            background: var(--brand);
            color: #fff;
            font-size: 10px;
            padding: 6px;
            text-transform: uppercase;
        }

        .line-table th:nth-child(1),
        .line-table td:nth-child(1) {
            width: 5%;
        }

        .line-table th:nth-child(2),
        .line-table td:nth-child(2) {
            width: 13%;
        }

        .line-table th:nth-child(3),
        .line-table td:nth-child(3) {
            width: 13%;
        }

        .line-table th:nth-child(4),
        .line-table td:nth-child(4) {
            width: 21%;
        }

        .line-table th:nth-child(5),
        .line-table td:nth-child(5),
        .line-table th:nth-child(6),
        .line-table td:nth-child(6),
        .line-table th:nth-child(7),
        .line-table td:nth-child(7),
        .line-table th:nth-child(8),
        .line-table td:nth-child(8),
        .line-table th:nth-child(9),
        .line-table td:nth-child(9) {
            width: 9.6%;
        }

        .line-table td {
            padding: 5px;
            font-size: 10px;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        .line-table .number {
            text-align: right;
            white-space: nowrap;
            font-weight: 700;
        }

        .line-table .money-cell {
            white-space: normal;
            line-height: 1.15;
            font-size: 9px;
            font-weight: 800;
        }

        .line-table .money-cell .amount-value {
            display: block;
            margin-top: 2px;
            font-size: 10px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
        }

        .line-table .paid-now {
            background: var(--brand-soft);
            color: var(--brand-dark);
        }

        td {
            padding: 9px;
            border: 1px solid var(--line);
        }

        .summary {
            margin-top: 16px;
            margin-left: auto;
            width: 86mm;
            border: 1px solid rgba(200, 100, 44, 0.35);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 12px;
            border-bottom: 1px solid var(--line);
        }

        .summary-row:last-child {
            border-bottom: 0;
        }

        .balance {
            background: linear-gradient(90deg, var(--brand-dark), var(--brand));
            color: #fff;
            font-size: 15px;
            font-weight: 800;
        }

        .notes {
            min-height: 70px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px;
            color: #334155;
            background: var(--panel);
            overflow-wrap: anywhere;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 42px;
        }

        .signature {
            padding-top: 36px;
            border-top: 1px solid #bca28e;
            text-align: center;
            color: #5b4b3f;
            font-weight: 700;
        }

        .footer {
            margin-top: 24px;
            padding-top: 10px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            font-size: 11px;
            overflow-wrap: anywhere;
        }

        .footer-copy,
        .footer-contact {
            min-width: 0;
        }

        .footer-copy {
            display: grid;
            gap: 4px;
        }

        .footer-brand {
            color: var(--brand-dark);
            font-size: 12px;
            font-weight: 800;
        }

        .footer-contact {
            text-align: right;
            display: grid;
            gap: 4px;
        }

        @media (max-width: 1100px) {
            body {
                font-size: 12px;
            }

            .receipt {
                width: calc(100vw - 12px);
                min-height: auto;
                padding: 16px;
                margin-bottom: 12px;
                border-radius: 14px;
            }

            .top-line {
                margin: -16px -16px 14px;
                border-radius: 14px 14px 0 0;
            }

            .header,
            .grid,
            .signatures,
            .footer {
                grid-template-columns: 1fr;
                display: grid;
            }

            .summary {
                width: 100%;
            }

            .header {
                text-align: left;
            }

            .receipt-number {
                justify-self: start;
                text-align: left;
            }

            .footer {
                text-align: left;
            }

            .title {
                text-align: left;
            }

            .school {
                align-items: flex-start;
            }

            .logo {
                width: 56px;
                height: 56px;
            }

            .amount-box {
                padding: 14px 12px;
            }

            .amount-box .amount {
                font-size: clamp(24px, 7vw, 30px);
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            .details-table th {
                width: auto;
                border-bottom: 0;
                border-radius: 12px 12px 0 0;
            }

            .details-table td {
                border-top: 0;
                margin-bottom: 10px;
                border-radius: 0 0 12px 12px;
                background: #fff;
            }

            .line-table thead {
                display: none;
            }

            .line-table tbody tr {
                margin-bottom: 10px;
                border: 1px solid var(--line);
                border-radius: 12px;
                overflow: hidden;
                background: #fff;
            }

            .line-table tbody td {
                border: 0;
                border-bottom: 1px solid var(--line);
                padding: 8px 10px;
            }

            .line-table tbody td:last-child {
                border-bottom: 0;
            }

            .line-table tbody td::before {
                content: attr(data-label);
                display: block;
                font-size: 10px;
                font-weight: 800;
                color: var(--brand-dark);
                text-transform: uppercase;
                margin-bottom: 4px;
            }

            .line-table .number {
                text-align: left;
                white-space: normal;
            }

            .footer-contact {
                text-align: left;
            }
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

            .receipt {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                border: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .top-line {
                margin: 0 0 18px;
                border-radius: 0;
            }

            .amount-box {
                break-inside: avoid;
            }

            .line-table th {
                font-size: 9px;
                padding: 5px 4px;
            }

            .line-table td {
                font-size: 9px;
                padding: 4px;
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: revert;
            }

            .line-table tbody td::before {
                content: none;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()">Print Receipt</button>
        <button class="secondary" onclick="window.close()">Close</button>
    </div>

    <main class="receipt">
        <div class="top-line"></div>

        <header class="header">
            <div class="school">
                <img class="logo" src="{{ $logo }}" alt="School logo">
                <div>
                    <h1>{{ $schoolName }}</h1>
                    <div class="muted">Finance Department</div>
                </div>
            </div>

            <div class="title">
                <div class="en">Payment Receipt</div>
                <div class="local">رسید پرداخت / د تادیې رسید</div>
            </div>

            <div class="receipt-number">
                <div class="label">Receipt Number</div>
                <div class="badge">{{ $payment->receipt_number }}</div>
                <div class="muted">Printed: {{ Jalalian::now()->format('Y/m/d') }}</div>
            </div>
        </header>

        <section class="amount-box">
            <div class="label">Amount Received</div>
            <div class="amount">{{ $money($payment->amount) }}</div>
        </section>

        <section class="grid">
            <div class="box">
                <div class="label">Payment Date</div>
                <div class="value">{{ $paymentDate }}</div>
            </div>
            <div class="box">
                <div class="label">Payment Method</div>
                <div class="value">{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</div>
            </div>
            <div class="box">
                <div class="label">Reference Number</div>
                <div class="value">{{ $payment->reference_number ?? '-' }}</div>
            </div>
        </section>

        <div class="section-title">Invoice And Family Details</div>
        <table class="details-table">
            <tbody>
                <tr>
                    <th>Invoice Number</th>
                    <td>{{ $invoice?->invoice_number ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Family Code</th>
                    <td>{{ $invoice?->family_code ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Parent / Guardian</th>
                    <td>{{ $parentName ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Billing Period</th>
                    <td>{{ $invoice?->billing_month ?? '-' }} {{ $invoice?->billing_year ?? '' }}</td>
                </tr>
                <tr>
                    <th>Invoice Status</th>
                    <td>{{ $invoice?->status ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Payment Breakdown</div>
        <table class="line-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Item / Fee</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Discount</th>
                    <th>Invoice Amount</th>
                    <th>Paid This Receipt</th>
                    <th>Line Balance</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    @php
                        $lineAmount = (float) $item->amount;
                        $coveredBefore = min($priorPaidRemaining, $lineAmount);
                        $priorPaidRemaining -= $coveredBefore;
                        $lineDueBeforeReceipt = max(0, $lineAmount - $coveredBefore);
                        $paidThisLine = min($receiptRemaining, $lineDueBeforeReceipt);
                        $receiptRemaining -= $paidThisLine;
                        $lineBalance = max(0, $lineDueBeforeReceipt - $paidThisLine);
                        $student = trim(($item->student?->name ?? '') . ' ' . ($item->student?->last_name ?? ''));
                        $itemName = $item->feeType?->name
                            ?? ($item->inventory_sale_id ? 'Inventory Sale' : ($item->is_previous_balance ? 'Previous Balance' : 'Invoice Item'));
                    @endphp
                    <tr>
                        <td data-label="#">{{ $loop->iteration }}</td>
                        <td data-label="Student">{{ $student !== '' ? $student : '-' }}</td>
                        <td data-label="Item / Fee">{{ $itemName }}</td>
                        <td data-label="Description">{{ $item->description }}</td>
                        <td data-label="Price" class="number money-cell">{!! $moneyCell($item->gross_amount ?: $item->amount) !!}</td>
                        <td data-label="Discount" class="number money-cell">{!! $moneyCell($item->discount_amount) !!}</td>
                        <td data-label="Invoice Amount" class="number money-cell">{!! $moneyCell($item->amount) !!}</td>
                        <td data-label="Paid This Receipt" class="number money-cell paid-now">{!! $moneyCell($paidThisLine) !!}</td>
                        <td data-label="Line Balance" class="number money-cell">{!! $moneyCell($lineBalance) !!}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">No invoice lines found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <strong>Invoice Total</strong>
                <span>{{ $money($invoice?->total_amount ?? 0) }}</span>
            </div>
            <div class="summary-row">
                <strong>Total Paid</strong>
                <span>{{ $money($invoice?->paid_amount ?? 0) }}</span>
            </div>
            <div class="summary-row balance">
                <span>Remaining Balance</span>
                <span>{{ $money($invoice?->balance ?? 0) }}</span>
            </div>
        </div>

        <div class="section-title">Notes</div>
        <div class="notes">{{ $payment->notes ?: 'No notes.' }}</div>

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
                <span>This receipt confirms payment received for the invoice shown above.</span>
                <span>{{ $payment->receipt_number }}</span>
            </div>
        </footer>
    </main>
</body>

</html>
