@php
    $schoolName = trim($settings['app_name'] ?? config('app.name', 'School Management'));
    $schoolAddress = $settings['school_address_line'] ?? ($settings['address'] ?? 'Kabul, Afghanistan');
    $schoolContactLine = $settings['support_phone_display'] ?? '';
    $logo = $settings['app_logo_url'] ?? asset('schools/cosmos.png');
    $studentName = trim(($student->name ?? '') . ' ' . ($student->last_name ?? '')) ?: '-';
    $fatherName = $student->father_name ?? '-';
    $money = fn ($value) => number_format((float) $value, 2) . ' AFN';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Card - {{ $studentName }}</title>
    <style>
        :root {
            --brand: #c8642c;
            --brand-dark: #9f4d20;
            --brand-soft: #fff4ec;
            --line: #e5d4c7;
            --ink: #3c2415;
            --muted: #7a5a46;
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
            background: linear-gradient(180deg, #f7ede5 0%, #f6f1eb 100%);
            color: var(--ink);
            font-family: "Segoe UI", Arial, sans-serif;
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

        .sheet {
            width: min(210mm, calc(100vw - 24px));
            min-height: 297mm;
            margin: 0 auto 24px;
            background: #fff;
            padding: 14mm;
            border-radius: 18px;
            box-shadow: 0 24px 50px rgba(77, 37, 12, 0.12);
        }

        .card {
            width: 100%;
            max-width: 184mm;
            margin: 0 auto;
            border: 2px solid var(--brand);
            padding: 12px 14px;
            background: linear-gradient(180deg, #ffffff 0%, #fffaf6 100%);
        }

        .header {
            display: grid;
            grid-template-columns: 64px 1fr 92px;
            gap: 12px;
            align-items: center;
            padding-bottom: 8px;
            border-bottom: 4px solid var(--brand-dark);
        }

        .logo {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }

        .titles {
            text-align: center;
        }

        .titles h1,
        .titles p {
            margin: 0;
        }

        .titles h1 {
            font-size: 18px;
            color: var(--brand-dark);
            font-weight: 800;
            text-transform: uppercase;
        }

        .titles .sub {
            font-size: 11px;
            color: var(--muted);
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .titles .card-title {
            margin-top: 4px;
            font-size: 24px;
            color: var(--brand-dark);
            font-weight: 800;
            text-transform: uppercase;
            text-decoration: underline;
            text-decoration-thickness: 2px;
        }

        .bubble {
            border: 2px solid var(--brand-dark);
            border-radius: 999px;
            padding: 10px 8px;
            text-align: center;
            font-size: 10px;
            font-weight: 800;
            color: var(--brand-dark);
            background: #fff7f1;
        }

        .meta-line,
        .info-grid,
        .totals-grid {
            display: grid;
            gap: 12px;
            margin-top: 10px;
        }

        .meta-line {
            grid-template-columns: 1.1fr 1fr 1fr;
            align-items: end;
        }

        .info-grid {
            grid-template-columns: 1.25fr 1fr 0.8fr;
        }

        .totals-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .field {
            min-width: 0;
        }

        .label {
            font-size: 11px;
            font-weight: 800;
            color: var(--brand-dark);
            text-transform: uppercase;
        }

        .line-value {
            margin-top: 3px;
            min-height: 24px;
            border-bottom: 1.5px solid var(--line);
            font-size: 15px;
            font-weight: 700;
            color: #10253d;
            padding-bottom: 2px;
            overflow-wrap: anywhere;
        }

        .line-value.small {
            font-size: 13px;
        }

        .periods {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .period {
            border: 1px solid var(--line);
            background: #fff;
            color: var(--brand-dark);
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .foot-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 18px;
            margin-top: 12px;
            align-items: end;
        }

        .signature {
            margin-top: 12px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .signature .line-value {
            min-height: 28px;
        }

        .footer {
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            gap: 12px;
            color: var(--muted);
            font-size: 11px;
        }

        @media (max-width: 900px) {
            .sheet {
                width: calc(100vw - 12px);
                min-height: auto;
                padding: 12px;
                margin-bottom: 12px;
                border-radius: 14px;
            }

            .card {
                padding: 12px;
            }

            .header,
            .meta-line,
            .info-grid,
            .totals-grid,
            .foot-grid,
            .signature,
            .footer {
                grid-template-columns: 1fr;
                display: grid;
            }

            .titles,
            .bubble {
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

            .sheet {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                border-radius: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Print Exam Card</button>
    </div>

    <main class="sheet">
        <section class="card">
            <header class="header">
                <img src="{{ $logo }}" alt="School logo" class="logo">

                <div class="titles">
                    <h1>{{ $schoolName }}</h1>
                    <p class="sub">{{ $examLabel }}</p>
                    <p class="card-title">Attending Card</p>
                </div>

                <div class="bubble">Fee Collection Account</div>
            </header>

            <div class="meta-line">
                <div class="field">
                    <div class="label">Expiry Date</div>
                    <div class="line-value small">{{ $examDate }}</div>
                </div>
                <div class="field">
                    <div class="label">Printed Date</div>
                    <div class="line-value small">{{ $printedDate }}</div>
                </div>
                <div class="field">
                    <div class="label">Exam</div>
                    <div class="line-value small">{{ $exam?->name ?? $examLabel }}</div>
                </div>
            </div>

            <div class="meta-line">
                <div class="field">
                    <div class="label">P - # Code</div>
                    <div class="line-value">{{ $cardCode }}</div>
                </div>
                <div class="field">
                    <div class="label">Receipt No</div>
                    <div class="line-value">{{ $receiptNo }}</div>
                </div>
                <div class="field">
                    <div class="label">Attending Card Fee</div>
                    <div class="line-value">{{ $money($receivedAmount) }}</div>
                </div>
            </div>

            <div class="info-grid">
                <div class="field">
                    <div class="label">Student Name</div>
                    <div class="line-value">{{ $studentName }}</div>
                </div>
                <div class="field">
                    <div class="label">Father Name</div>
                    <div class="line-value">{{ $fatherName }}</div>
                </div>
                <div class="field">
                    <div class="label">Class</div>
                    <div class="line-value">{{ $schoolClass->class_name }}</div>
                </div>
            </div>

            @if($periods->isNotEmpty())
                <div class="periods">
                    @foreach($periods as $period)
                        <span class="period">{{ $period }}</span>
                    @endforeach
                </div>
            @endif

            <div class="foot-grid">
                <div class="field">
                    <div class="label">Total Clearance</div>
                    <div class="line-value">{{ $money($studentTotal) }}</div>
                </div>
                <div class="field">
                    <div class="label">Received</div>
                    <div class="line-value">{{ $money($receivedAmount) }}</div>
                </div>
            </div>

            <div class="signature">
                <div class="field">
                    <div class="label">Financial Manager Sign</div>
                    <div class="line-value"></div>
                </div>
                <div class="field">
                    <div class="label">School Stamp</div>
                    <div class="line-value"></div>
                </div>
            </div>

            <footer class="footer">
                <span>{{ $schoolAddress }}</span>
                <span>{{ $schoolContactLine }}</span>
            </footer>
        </section>
    </main>
</body>
</html>
