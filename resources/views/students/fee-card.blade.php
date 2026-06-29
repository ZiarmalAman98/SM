<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>کارت فیس شاگرد</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Formal Dari/Arabic fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600;700&family=Vazirmatn:wght@400;600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --card-w: 86mm;
            --card-h: 124mm;
            --radius: 12px;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            font-family: "Noto Naskh Arabic", "Amiri", "Vazirmatn", Tahoma, Arial, sans-serif;
        }

        .print-controls {
            display: none;
            gap: .5rem;
            padding: 12px;
            position: sticky;
            top: 0;
        }

        @media screen {
            .print-controls {
                display: flex;
            }
        }

        .sheet {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(var(--card-w), 1fr));
            gap: 10mm;
            padding: 10mm;
            box-sizing: border-box;
        }

        .card {
            width: var(--card-w);
            height: var(--card-h);
            border-radius: var(--radius);
            position: relative;
            overflow: hidden;
            background: #ffffff url('{{ asset('fee_card_bg.png') }}') no-repeat center center;
            background-size: cover;

            /* ✅ Force background image in print */
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .15));
        }

        /* ✅ Logo in top-left corner */
        .logo {
            position: absolute;
            top: 14mm;
            left: 8mm;
            width: 33mm;
            height: auto;
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
        }

        .fields {
            position: absolute;
            right: 40mm;
            top: 64mm;
            width: calc(100% - 32mm);
            color: #13224a;
        }

        .label {
            font-family: "Noto Naskh Arabic", "Vazirmatn", sans-serif;
            font-weight: 600;
            font-size: 11pt;
            line-height: 1.7;
        }

        .value {
            font-family: "Amiri", "Noto Naskh Arabic", serif;
            font-weight: 700;
            font-size: 15pt;
        }

        /* ✅ School phone at bottom */
        .school-phone {
            position: absolute;
            bottom: 7mm;
            left: 0;
            right: 0;
            text-align: center;
            font-family: "Noto Naskh Arabic", "Amiri", serif;
            font-size: 11pt;
            font-weight: 600;
            color: #13224a;
        }

        @media print {
            .print-controls {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .card {
                box-shadow: none;
            }
        }
    </style>
</head>

<body>

    <div class="print-controls">
        <button onclick="window.print()">پرینت</button>
    </div>

    <div class="sheet">
        <div class="card">
            <div class="overlay"></div>

            {{-- ✅ Logo --}}
            <div class="logo">
                <img src="@if (isset($settings['app_logo'])) {{ asset('storage/' . $settings['app_logo']) }} @else {{ asset('schools/cosmos.png') }} @endif"
                    alt="Logo">
            </div>

            <div class="fields">
                <div class="label father-name">
                    <span class="value">{{ $father_name ?? ($student->father_name ?? '—') }}</span>
                </div>

                <div class="label student-name">
                    <span class="value">{{ $student_name ?? ($student->name ?? '—') }}</span>
                </div>

                <div class="label phone-number">
                    <span class="value">{{ $phone ?? ($student->phone ?? '—') }}</span>
                </div>
            </div>

            {{-- ✅ School phone number at bottom --}}
            <div class="school-phone" dir="ltr" style="unicode-bidi: embed; text-align: center;">
                {{ $settings['support_phone_display'] ?? '' }}
            </div>
        </div>
    </div>

</body>

</html>
