@php
    use Morilog\Jalali\Jalalian;

    $schoolName = $settings['app_name'] ?? config('app.name', 'School Management');
    $logo = isset($settings['app_logo']) && filled($settings['app_logo'])
        ? asset('storage/' . $settings['app_logo'])
        : asset('schools/cosmos.png');
    $schoolAddress = $settings['school_address_line'] ?? ($settings['address'] ?? '');
    $schoolContactLine = implode(' | ', array_filter([
        $settings['support_phone_display'] ?? '',
        $settings['support_email'] ?? '',
    ]));

    $money = fn ($value) => number_format((float) $value, 2);
    $openingBalance = (float) $supplier->opening_balance;
    $openingEffect = $supplier->opening_balance_type === 'receivable'
        ? -$openingBalance
        : $openingBalance;

    $running = $openingEffect;
    $rows = [];

    if ($openingBalance > 0) {
        $rows[] = [
            'date' => null,
            'bill_no' => '-',
            'type' => __('Opening Balance'),
            'debit' => $openingEffect > 0 ? $openingBalance : 0,
            'credit' => $openingEffect < 0 ? $openingBalance : 0,
            'balance' => $running,
            'status' => '-',
            'notes' => $supplier->opening_balance_type === 'receivable'
                ? __('Receivable')
                : __('Payable'),
        ];
    }

    foreach ($purchases as $purchase) {
        if ($purchase->status === 'cancelled') {
            continue;
        }

        $debit = (float) $purchase->total_amount;
        $credit = (float) $purchase->paid_amount;
        $running += $debit - $credit;
        $isPaymentOnly = $debit <= 0 && $credit > 0;

        $rows[] = [
            'date' => $purchase->purchase_date,
            'bill_no' => $purchase->purchase_no,
            'type' => $isPaymentOnly ? __('Payment') : __('Purchase'),
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $running,
            'status' => __($purchase->status),
            'notes' => $purchase->notes,
            'purchase' => $purchase,
        ];
    }

    $closingBalance = (float) $supplier->net_balance;
    $isCompleted = abs($closingBalance) < 0.01;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Supplier Ledger') }} - {{ $supplier->name }}</title>
    <style>
        @page { size: A4; margin: 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef2f6; color: #172033; font-family: "Segoe UI", Arial, sans-serif; font-size: 13px; line-height: 1.45; }
        .toolbar { display: flex; justify-content: center; gap: 10px; padding: 18px; }
        .toolbar button { border: 0; border-radius: 6px; padding: 10px 18px; background: #155e75; color: #fff; cursor: pointer; font-weight: 600; }
        .toolbar button.secondary { background: #475569; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto 24px; background: #fff; padding: 14mm; border: 1px solid #d8dee8; box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12); }
        .top-line { height: 6px; background: #155e75; margin: -14mm -14mm 18px; }
        .header { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 18px; border-bottom: 2px solid #155e75; padding-bottom: 14px; }
        .school { display: flex; align-items: center; gap: 12px; }
        .logo { width: 68px; height: 68px; object-fit: contain; }
        .school h1 { margin: 0; color: #0f172a; font-size: 22px; font-weight: 800; }
        .muted { color: #64748b; font-size: 12px; }
        .title { text-align: center; }
        .title .en { color: #155e75; font-size: 22px; font-weight: 800; text-transform: uppercase; }
        .title .local { margin-top: 3px; color: #334155; font-size: 15px; font-weight: 700; }
        .number-box { justify-self: end; text-align: right; }
        .badge { display: inline-block; padding: 5px 10px; border: 1px solid #155e75; border-radius: 4px; background: #ecfeff; color: #155e75; font-weight: 800; }
        .badge.ok { border-color: #15803d; background: #f0fdf4; color: #15803d; }
        .badge.due { border-color: #b91c1c; background: #fef2f2; color: #b91c1c; }
        .meta-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 18px 0; }
        .box { border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 12px; background: #f8fafc; }
        .label { color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .value { margin-top: 4px; color: #0f172a; font-size: 14px; font-weight: 700; }
        .section-title { margin: 20px 0 8px; color: #155e75; font-size: 15px; font-weight: 800; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #155e75; color: #fff; font-size: 11px; padding: 8px 7px; text-align: left; text-transform: uppercase; }
        td { border: 1px solid #cbd5e1; padding: 8px 7px; vertical-align: top; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        .number { text-align: right; white-space: nowrap; }
        .summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 16px; }
        .summary .box .value { font-size: 15px; }
        .footer { margin-top: 28px; padding-top: 14px; border-top: 1px solid #cbd5e1; color: #475569; text-align: center; font-size: 12px; line-height: 1.6; }
        .footer .footer-title { color: #155e75; font-size: 13px; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; }
        .footer .footer-address { color: #334155; font-weight: 600; }
        .footer .footer-contact { color: #64748b; margin-top: 2px; }
        @media print {
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .toolbar { display: none !important; }
            .page { width: auto; min-height: auto; margin: 0; padding: 0; border: 0; box-shadow: none; }
            .top-line { margin: 0 0 18px; }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()">{{ __('Print Ledger') }}</button>
        <button class="secondary" onclick="window.close()">{{ __('Close') }}</button>
    </div>

    <main class="page">
        <div class="top-line"></div>

        <header class="header">
            <div class="school">
                <img class="logo" src="{{ $logo }}" alt="{{ $schoolName }}">
                <div>
                    <h1>{{ $schoolName }}</h1>
                </div>
            </div>
            <div class="title">
                <div class="en">{{ __('Supplier Ledger') }}</div>
                <div class="local">{{ __('Full Account Statement') }}</div>
            </div>
            <div class="number-box">
                <div class="muted">{{ __('Code') }}</div>
                <div class="badge">{{ $supplier->supplier_code }}</div>
            </div>
        </header>

        <section class="meta-grid">
            <div class="box">
                <div class="label">{{ __('Supplier') }}</div>
                <div class="value">{{ $supplier->name }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Phone') }}</div>
                <div class="value">{{ $supplier->phone ?: '-' }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Email') }}</div>
                <div class="value">{{ $supplier->email ?: '-' }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Ledger Status') }}</div>
                <div class="value">
                    @if($isCompleted)
                        <span class="badge ok">{{ __('Completed / Settled') }}</span>
                    @else
                        <span class="badge due">{{ $supplier->balance_status }}</span>
                    @endif
                </div>
            </div>
        </section>

        <div class="section-title">{{ __('Full Ledger') }}</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Bill No') }}</th>
                    <th>{{ __('Type') }}</th>
                    <th class="number">{{ __('Debit') }}</th>
                    <th class="number">{{ __('Credit') }}</th>
                    <th class="number">{{ __('Balance') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            @if($row['date'])
                                {{ Jalalian::fromDateTime($row['date'])->format('Y/m/d') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $row['bill_no'] }}</td>
                        <td>{{ $row['type'] }}</td>
                        <td class="number">{{ $row['debit'] > 0 ? $money($row['debit']) : '-' }}</td>
                        <td class="number">{{ $row['credit'] > 0 ? $money($row['credit']) : '-' }}</td>
                        <td class="number">{{ $money($row['balance']) }}</td>
                        <td>{{ $row['status'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;">{{ __('No ledger entries found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <section class="summary">
            <div class="box">
                <div class="label">{{ __('Purchases') }}</div>
                <div class="value">AFN {{ $money($supplier->purchases_total) }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Paid') }}</div>
                <div class="value">AFN {{ $money($supplier->purchases_paid) }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Closing Balance') }}</div>
                <div class="value">AFN {{ $money(abs($closingBalance)) }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Payment Status') }}</div>
                <div class="value">{{ $isCompleted ? __('Completed') : $supplier->balance_status }}</div>
            </div>
        </section>

        <footer class="footer">
            <div class="footer-title">{{ __('Supplier Ledger') }}</div>
            @if($schoolAddress !== '')
                <div class="footer-address">{{ $schoolAddress }}</div>
            @endif
            @if($schoolContactLine !== '')
                <div class="footer-contact">{{ $schoolContactLine }}</div>
            @endif
        </footer>
    </main>
</body>

</html>
