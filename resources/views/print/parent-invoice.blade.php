@php
    $invoices = isset($invoices) ? collect($invoices) : collect(isset($invoice) ? [$invoice] : []);
    $isPrintAll = $invoices->count() > 1 || ! empty($printAll);
    $pageTitle = $isPrintAll
        ? 'Parent Invoices - ' . ($billingMonth ?? $invoices->first()?->billing_month) . ' ' . ($billingYear ?? $invoices->first()?->billing_year)
        : (($invoices->first()?->invoice_number ?? 'Parent Invoice') . ' - Parent Invoice');
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
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
            margin: 12mm;
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

        .page {
            width: min(210mm, calc(100vw - 24px));
            min-height: 297mm;
            margin: 0 auto 24px;
            background: #fff;
            padding: 14mm;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 24px 50px rgba(77, 37, 12, 0.12);
        }

        .top-line {
            height: 8px;
            background: linear-gradient(90deg, var(--brand-dark), var(--brand));
            margin: -14mm -14mm 18px;
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
            font-size: 24px;
            font-weight: 800;
        }

        .school p,
        .header-note {
            margin: 3px 0 0;
            color: var(--muted);
            font-size: 12px;
            overflow-wrap: anywhere;
        }

        .title {
            text-align: center;
        }

        .title .en {
            color: var(--brand-dark);
            font-size: 23px;
            font-weight: 800;
            letter-spacing: 0;
            text-transform: uppercase;
        }

        .title .local {
            margin-top: 3px;
            color: #7c2d12;
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
            border: 1px solid #f0b486;
            border-radius: 4px;
            background: var(--brand-soft);
            color: var(--brand-dark);
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
            overflow: hidden;
            border-radius: 12px;
        }

        th {
            background: var(--brand);
            color: #fff;
            font-size: 11px;
            padding: 8px 7px;
            text-align: left;
            text-transform: uppercase;
            overflow-wrap: anywhere;
        }

        td {
            border: 1px solid var(--line);
            padding: 8px 7px;
            vertical-align: top;
            overflow-wrap: anywhere;
        }

        tbody tr:nth-child(even) td {
            background: #fff9f5;
        }

        .number {
            text-align: right;
            white-space: nowrap;
        }

        .money-cell {
            white-space: normal;
            line-height: 1.15;
            font-size: 10px;
            font-weight: 800;
        }

        .money-cell .amount-value {
            display: block;
            margin-top: 2px;
            font-size: 11px;
            font-weight: 800;
            font-variant-numeric: tabular-nums;
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
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px;
            background: var(--panel);
            overflow-wrap: anywhere;
        }

        .totals {
            border: 1px solid rgba(200, 100, 44, 0.35);
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 9px 12px;
            border-bottom: 1px solid var(--line);
        }

        .total-row:last-child {
            border-bottom: 0;
        }

        .grand {
            background: linear-gradient(90deg, var(--brand-dark), var(--brand));
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

        @media (max-width: 900px) {
            body {
                font-size: 12px;
            }

            .page {
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
            .meta-grid,
            .summary,
            .signatures,
            .footer {
                grid-template-columns: 1fr;
                display: grid;
            }

            .header {
                text-align: left;
            }

            .status {
                justify-self: start;
                text-align: left;
                min-width: 0;
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

            .meta-grid {
                gap: 8px;
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            thead {
                display: none;
            }

            tbody tr {
                margin-bottom: 10px;
                border: 1px solid var(--line);
                border-radius: 12px;
                overflow: hidden;
                background: #fff;
            }

            tbody td {
                border: 0;
                border-bottom: 1px solid var(--line);
                padding: 8px 10px;
            }

            tbody td:last-child {
                border-bottom: 0;
            }

            tbody td::before {
                content: attr(data-label);
                display: block;
                font-size: 10px;
                font-weight: 800;
                color: var(--brand-dark);
                text-transform: uppercase;
                margin-bottom: 4px;
            }

            .number {
                text-align: left;
                white-space: normal;
            }

            .footer {
                text-align: left;
                gap: 8px;
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

            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                border: 0;
                box-shadow: none;
                border-radius: 0;
            }

            .page + .page {
                break-before: page;
                page-break-before: always;
            }

            .top-line {
                margin: 0 0 18px;
                border-radius: 0;
            }

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: revert;
            }

            tbody td::before {
                content: none;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()">{{ $isPrintAll ? 'Print All Invoices' : 'Print Invoice' }}</button>
        <button class="secondary" onclick="window.close()">Close</button>
    </div>

    @forelse ($invoices as $invoice)
        @include('print.partials.parent-invoice-document', [
            'invoice' => $invoice,
            'settings' => $settings,
        ])
    @empty
        <main class="page">
            <div class="top-line"></div>
            <p style="padding: 24px; text-align: center;">No invoices found for the selected month and year.</p>
        </main>
    @endforelse
</body>

</html>
