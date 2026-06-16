@php
    use Morilog\Jalali\Jalalian;

    $schoolName = $settings['app_name'] ?? config('app.name', 'School Management');
    $logo = isset($settings['app_logo']) && filled($settings['app_logo'])
        ? asset('storage/' . $settings['app_logo'])
        : asset('schools/cosmos.png');
    $money = fn ($value) => 'AFN ' . number_format((float) $value, 2);
    $total = $payments->sum('amount');
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Payment Report</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef2f6; color: #172033; font-family: "Segoe UI", Arial, sans-serif; font-size: 12px; }
        .toolbar { display: flex; justify-content: center; gap: 10px; padding: 16px; }
        .toolbar button { border: 0; border-radius: 6px; padding: 9px 16px; background: #155e75; color: #fff; cursor: pointer; font-weight: 700; }
        .page { width: 277mm; min-height: 190mm; margin: 0 auto 20px; background: #fff; padding: 12mm; border: 1px solid #d8dee8; }
        .header { display: flex; justify-content: space-between; align-items: center; gap: 18px; border-bottom: 2px solid #155e75; padding-bottom: 12px; }
        .school { display: flex; align-items: center; gap: 12px; }
        .logo { width: 58px; height: 58px; object-fit: contain; }
        h1, h2 { margin: 0; }
        h1 { font-size: 22px; }
        h2 { color: #155e75; font-size: 20px; text-transform: uppercase; }
        .muted { color: #64748b; font-size: 11px; }
        .summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin: 16px 0; }
        .box { border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; background: #f8fafc; }
        .label { color: #64748b; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .value { margin-top: 4px; font-size: 15px; font-weight: 800; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #155e75; color: #fff; padding: 8px; text-align: left; border: 1px solid #0f4f63; }
        td { padding: 7px 8px; border: 1px solid #cbd5e1; vertical-align: top; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .right { text-align: right; white-space: nowrap; }
        .empty { padding: 30px; text-align: center; color: #64748b; border: 1px solid #cbd5e1; }
        @media print {
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .toolbar { display: none !important; }
            .page { width: auto; min-height: auto; margin: 0; padding: 0; border: 0; }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()">Print Report</button>
        <button onclick="window.close()">Close</button>
    </div>

    <main class="page">
        <header class="header">
            <div class="school">
                <img class="logo" src="{{ $logo }}" alt="School logo">
                <div>
                    <h1>{{ $schoolName }}</h1>
                    <div class="muted">Finance Department</div>
                </div>
            </div>
            <div>
                <h2>Invoice Payment Report</h2>
                <div class="muted">
                    {{ Jalalian::fromDateTime($fromDate)->format('Y/m/d') }}
                    -
                    {{ Jalalian::fromDateTime($toDate)->format('Y/m/d') }}
                </div>
            </div>
        </header>

        <section class="summary">
            <div class="box">
                <div class="label">Payments</div>
                <div class="value">{{ $payments->count() }}</div>
            </div>
            <div class="box">
                <div class="label">Total Received</div>
                <div class="value">{{ $money($total) }}</div>
            </div>
            <div class="box">
                <div class="label">Generated</div>
                <div class="value">{{ Jalalian::now()->format('Y/m/d') }}</div>
            </div>
        </section>

        @if($payments->isEmpty())
            <div class="empty">No invoice payments found for the selected filters.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Receipt</th>
                        <th>Invoice</th>
                        <th>Family</th>
                        <th>Parent</th>
                        <th>Billing Period</th>
                        <th>Payment Date</th>
                        <th>Method</th>
                        <th class="right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        @php
                            $invoice = $payment->invoice;
                            $parent = $invoice?->parentGuardian?->user;
                            $parentName = trim(($parent?->name ?? '') . ' ' . ($parent?->last_name ?? ''));
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{ $invoice?->invoice_number ?? '-' }}</td>
                            <td>{{ $invoice?->family_code ?? '-' }}</td>
                            <td>{{ $parentName ?: '-' }}</td>
                            <td>{{ $invoice?->billing_month ?? '-' }} {{ $invoice?->billing_year ?? '' }}</td>
                            <td>{{ $payment->payment_date ? Jalalian::fromDateTime($payment->payment_date)->format('Y/m/d') : '-' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', (string) $payment->payment_method)) }}</td>
                            <td class="right">{{ $money($payment->amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8" class="right">Total</th>
                        <th class="right">{{ $money($total) }}</th>
                    </tr>
                </tfoot>
            </table>
        @endif
    </main>
</body>

</html>
