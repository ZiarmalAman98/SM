@php
    use Morilog\Jalali\Jalalian;

    $schoolName = $settings['app_name'] ?? config('app.name', 'School Management');
    $logo = isset($settings['app_logo']) && filled($settings['app_logo'])
        ? asset('storage/' . $settings['app_logo'])
        : asset('schools/cosmos.png');
    $purchaseDate = $purchase->purchase_date ? Jalalian::fromDateTime($purchase->purchase_date)->format('Y/m/d') : '-';
    $money = fn ($value) => 'AFN ' . number_format((float) $value, 2);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $purchase->purchase_no }} - Purchase Invoice</title>
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
        .school h1 { margin: 0; color: #0f172a; font-size: 24px; font-weight: 800; }
        .muted { color: #64748b; font-size: 12px; }
        .title { text-align: center; }
        .title .en { color: #155e75; font-size: 23px; font-weight: 800; text-transform: uppercase; }
        .title .local { margin-top: 3px; color: #334155; font-size: 16px; font-weight: 700; }
        .number-box { justify-self: end; text-align: right; }
        .badge { display: inline-block; padding: 5px 10px; border: 1px solid #155e75; border-radius: 4px; background: #ecfeff; color: #155e75; font-weight: 800; }
        .meta-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin: 18px 0; }
        .box { border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 12px; background: #f8fafc; }
        .label { color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .value { margin-top: 4px; color: #0f172a; font-size: 14px; font-weight: 700; }
        .section-title { margin: 20px 0 8px; color: #155e75; font-size: 15px; font-weight: 800; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #155e75; color: #fff; font-size: 11px; padding: 8px 7px; text-align: left; text-transform: uppercase; }
        td { border: 1px solid #cbd5e1; padding: 8px 7px; vertical-align: top; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        .number { text-align: right; white-space: nowrap; }
        .summary { display: grid; grid-template-columns: 1fr 84mm; gap: 16px; margin-top: 16px; align-items: start; }
        .notes { min-height: 96px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; }
        .totals { border: 1px solid #155e75; border-radius: 6px; overflow: hidden; }
        .total-row { display: flex; justify-content: space-between; gap: 12px; padding: 9px 12px; border-bottom: 1px solid #dbe4ee; }
        .total-row:last-child { border-bottom: 0; }
        .grand { background: #155e75; color: #fff; font-size: 16px; font-weight: 800; }
        .signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-top: 42px; }
        .signature { padding-top: 36px; border-top: 1px solid #0f172a; text-align: center; color: #475569; font-weight: 700; }
        .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #cbd5e1; color: #64748b; display: flex; justify-content: space-between; gap: 16px; font-size: 11px; }
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
        <button onclick="window.print()">Print Purchase</button>
        <button class="secondary" onclick="window.close()">Close</button>
    </div>

    <main class="page">
        <div class="top-line"></div>

        <header class="header">
            <div class="school">
                <img class="logo" src="{{ $logo }}" alt="{{ $schoolName }}">
                <div>
                    <h1>{{ $schoolName }}</h1>
                    <div class="muted">Inventory Purchase Invoice</div>
                </div>
            </div>
            <div class="title">
                <div class="en">Purchase Invoice</div>
                <div class="local">د خرید بیل</div>
            </div>
            <div class="number-box">
                <div class="muted">Purchase #</div>
                <div class="badge">{{ $purchase->purchase_no }}</div>
            </div>
        </header>

        <section class="meta-grid">
            <div class="box"><div class="label">Supplier</div><div class="value">{{ $purchase->supplier?->name ?? '-' }}</div></div>
            <div class="box"><div class="label">Company</div><div class="value">{{ $purchase->supplier?->company_name ?? '-' }}</div></div>
            <div class="box"><div class="label">Purchase Date</div><div class="value">{{ $purchaseDate }}</div></div>
            <div class="box"><div class="label">Status</div><div class="value">{{ ucfirst($purchase->status) }}</div></div>
        </section>

        <div class="section-title">Purchase Items</div>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="number">Qty</th>
                    <th class="number">Unit Price</th>
                    <th class="number">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchase->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->product?->name ?? '-' }}</td>
                        <td class="number">{{ number_format((float) $item->quantity, 2) }}</td>
                        <td class="number">{{ $money($item->unit_price) }}</td>
                        <td class="number">{{ $money($item->total_amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <section class="summary">
            <div>
                <div class="section-title">Notes</div>
                <div class="notes">{{ $purchase->notes ?: '-' }}</div>
            </div>
            <div class="totals">
                <div class="total-row"><span>Subtotal</span><strong>{{ $money($purchase->subtotal) }}</strong></div>
                <div class="total-row"><span>Discount</span><strong>{{ $money($purchase->discount_amount) }}</strong></div>
                <div class="total-row"><span>Total</span><strong>{{ $money($purchase->total_amount) }}</strong></div>
                <div class="total-row"><span>Paid</span><strong>{{ $money($purchase->paid_amount) }}</strong></div>
                <div class="total-row grand"><span>Balance</span><strong>{{ $money($purchase->balance) }}</strong></div>
            </div>
        </section>

        <section class="signatures">
            <div class="signature">Prepared By</div>
            <div class="signature">Supplier</div>
            <div class="signature">Approved By</div>
        </section>

        <footer class="footer">
            <span>Generated by {{ $schoolName }}</span>
            <span>{{ now()->format('Y-m-d H:i') }}</span>
        </footer>
    </main>
</body>

</html>
