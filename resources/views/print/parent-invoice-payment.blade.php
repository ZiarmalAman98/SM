@php
    use Morilog\Jalali\Jalalian;

    $invoice = $payment->invoice;
    $parent = $invoice?->parentGuardian?->user;
    $parentName = trim(($parent?->name ?? '') . ' ' . ($parent?->last_name ?? ''));
    $schoolName = $settings['app_name'] ?? config('app.name', 'School Management');
    $logo = isset($settings['app_logo']) && filled($settings['app_logo'])
        ? asset('storage/' . $settings['app_logo'])
        : asset('schools/cosmos.png');
    $money = fn ($value) => 'AFN ' . number_format((float) $value, 2);
    $paymentDate = $payment->payment_date ? Jalalian::fromDateTime($payment->payment_date)->format('Y/m/d') : '-';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $payment->receipt_number }} - Invoice Payment Receipt</title>
    <style>
        @page {
            size: A4;
            margin: 14mm;
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

        .receipt {
            width: 190mm;
            min-height: 250mm;
            margin: 0 auto 24px;
            background: #fff;
            padding: 14mm;
            border: 1px solid #d8dee8;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        }

        .top-line {
            height: 6px;
            margin: -14mm -14mm 18px;
            background: #155e75;
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
            font-size: 23px;
            font-weight: 800;
        }

        .muted {
            color: #64748b;
            font-size: 12px;
        }

        .title {
            text-align: center;
        }

        .title .en {
            color: #155e75;
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .title .local {
            margin-top: 3px;
            color: #334155;
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
            border: 1px solid #155e75;
            border-radius: 4px;
            background: #ecfeff;
            color: #155e75;
            font-weight: 800;
        }

        .amount-box {
            margin: 22px 0;
            padding: 18px;
            border: 2px solid #155e75;
            border-radius: 8px;
            background: #f0fdfa;
            text-align: center;
        }

        .amount-box .label {
            color: #0f766e;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .amount-box .amount {
            margin-top: 4px;
            color: #0f172a;
            font-size: 34px;
            font-weight: 900;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 18px;
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
            width: 34%;
            background: #f1f5f9;
            color: #334155;
            font-size: 12px;
            text-align: left;
            padding: 9px;
            border: 1px solid #cbd5e1;
        }

        td {
            padding: 9px;
            border: 1px solid #cbd5e1;
        }

        .summary {
            margin-top: 16px;
            margin-left: auto;
            width: 86mm;
            border: 1px solid #155e75;
            border-radius: 6px;
            overflow: hidden;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 12px;
            border-bottom: 1px solid #dbe4ee;
        }

        .summary-row:last-child {
            border-bottom: 0;
        }

        .balance {
            background: #155e75;
            color: #fff;
            font-size: 15px;
            font-weight: 800;
        }

        .notes {
            min-height: 70px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px;
            color: #334155;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 42px;
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

            .receipt {
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
        <table>
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
            <span>This receipt confirms payment received for the invoice shown above.</span>
            <span>{{ $payment->receipt_number }}</span>
        </footer>
    </main>
</body>

</html>
