<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>جدول شهرت - {{ $biography->name ?? '—' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Vazirmatn font -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css">

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Vazirmatn, Tahoma, Arial, sans-serif;
            color: #2c3e50;
            line-height: 1.5;
            background-color: #f8f9fa;
        }

        .container {
            width: 100%;
            max-width: 297mm;
            margin: 0 auto;
            padding: 10mm;
            background: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, .1);
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2c3e50;
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            color: #2c3e50;
        }

        .subtitle {
            font-size: 16px;
            margin: 5px 0 0;
            color: #7f8c8d;
        }

        /* Grid */
        table.grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border: 2px solid #2c3e50;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .1);
        }

        .grid th,
        .grid td {
            border: 1px solid #7f8c8d;
            padding: 0;
            vertical-align: middle;
            font-size: 12px;
            text-align: center;
        }

        .w-notes {
            width: 120px;
        }

        .w-narrow {
            width: 50px;
        }

        .w-mid {
            width: 60px;
        }

        .grouprow th {
            text-align: center;
            font-weight: 700;
            padding: 10px 0;
            background-color: #2c3e50;
            color: #fff;
            border-bottom: 2px solid #2c3e50;
        }

        .subrow th {
            text-align: center;
            padding: 10px 0;
            font-weight: 700;
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
            line-height: 1.2;
            white-space: nowrap;
            background: #ecf0f1;
            color: #2c3e50;
        }

        .data td {
            height: 35mm;
            padding: 0;
            /* match header padding style */
            text-align: center;
            vertical-align: middle;
            background: #fff;
        }

        .data td:not(.w-notes) {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
            white-space: nowrap;
            /* no mid-word breaks */
            word-break: keep-all;
            overflow-wrap: normal;
            padding: 10px 0;
            /* same vertical padding as headers */
        }

        .notes-head {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            transform: rotate(180deg);
            text-align: center;
            font-weight: 700;
            font-size: 14px;
            padding: 10px 0;
            color: #fff;
        }

        .data .w-notes {
            vertical-align: top;
            padding: 10px;
            background: #f8f9fa;
            white-space: normal;
            word-break: normal;
            overflow-wrap: break-word;
        }

        .footer {
            margin-top: 15px;
            font-size: 13px;
            line-height: 1.7;
            text-align: justify;
            padding: 10px;
            border-top: 1px solid #bdc3c7;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .footer-endorse {
            margin-top: 10px;
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            padding: 8px;
            background: #ecf0f1;
            border-radius: 4px;
        }

        .print-button {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background: #2c3e50;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-family: Vazirmatn, Tahoma, Arial, sans-serif;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .2);
        }

        .print-button:hover {
            background: #1a252f;
        }

        @media print {

            /* Keep exact colors & backgrounds */
            html,
            body,
            .grouprow th,
            .subrow th,
            .data .w-notes,
            .footer,
            .footer-endorse,
            .grid th,
            .grid td {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                /* modern alias */
            }

            /* Preserve page size & margins */
            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            /* Keep container spacing & remove shadows only */
            .container {
                width: 100%;
                padding: 10mm !important;
                /* don't drop padding in print */
                box-shadow: none !important;
                background: #fff !important;
            }

            /* Prevent table from breaking oddly */
            table.grid {
                page-break-inside: avoid;
                box-shadow: none;
            }

            /* Keep same column widths/heights */
            .w-notes {
                width: 120px !important;
            }

            .w-narrow {
                width: 50px !important;
            }

            .w-mid {
                width: 60px !important;
            }

            .data td {
                height: 35mm !important;
            }

            /* Hide the button on paper */
            .print-button {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    @php
        $yearJalali = class_exists(\Morilog\Jalali\Jalalian::class)
            ? \Morilog\Jalali\Jalalian::fromCarbon(\Carbon\Carbon::now())->format('Y')
            : now()->format('Y');
    @endphp

    <div class="container">
        <div class="header">
            <h1 class="title">جدول شهرت یک نفر شاگرد</h1>
            <p class="subtitle">که در سال ({{ $yearJalali }}) شامل صنف اول گردیده</p>
        </div>

        <table class="grid">
            {{-- Row 1: group headers --}}
            <tr class="grouprow">
                {{-- Personal --}}
                <th colspan="8">شهرت مکمل</th>
                {{-- Permanent address --}}
                <th colspan="3">سکونت اصلی</th>
                {{-- Current address --}}
                <th colspan="3">سکونت فعلی</th>
                {{-- Relatives --}}
                <th colspan="5">شهرت اقارب نزدیک</th>

                {{-- Notes (at end, spanning both header rows) --}}
                <th class="w-notes" rowspan="2">
                    <div class="notes-head">ملاحظات</div>
                </th>
            </tr>

            {{-- Row 2: vertical sub-headers --}}
            <tr class="subrow">
                {{-- Personal (8) --}}
                <th class="w-narrow">اسم شاگرد</th>
                <th class="w-narrow">اسم پدر</th>
                <th class="w-narrow">ولدیت</th>
                <th class="w-narrow">سن موجوده</th>
                <th class="w-narrow">تابعیت تذکره</th>
                <th class="w-narrow">ملیت</th>
                <th class="w-narrow">وظیفه پدر</th>
                <th class="w-narrow">زبان مادری</th>
                {{-- Permanent (3) --}}
                <th class="w-narrow">ولایت</th>
                <th class="w-narrow">ولسوالی</th>
                <th class="w-narrow">قریه</th>
                {{-- Current (3) --}}
                <th class="w-narrow">ولایت</th>
                <th class="w-narrow">ولسوالی</th>
                <th class="w-narrow">قریه</th>
                {{-- Relatives (5) --}}
                <th class="w-narrow">برادر</th>
                <th class="w-narrow">کاکا</th>
                <th class="w-narrow">ماما</th>
                <th class="w-narrow">پسر کاکا</th>
                <th class="w-narrow">پسر ماما</th>
            </tr>

            {{-- Row 3: data --}}
            <tr class="data">
                {{-- Personal (8) --}}
                <td>{{ $biography->name ?? '' }}</td>
                <td>{{ $biography->father_name ?? '' }}</td>
                <td>{{ $biography->grand_father_name ?? '' }}</td>
                <td>{{ $biography->age ?? '' }}</td>
                <td>{{ $biography->citizenship_tazkira ?? '' }}</td>
                <td>{{ $biography->nationality ?? '' }}</td>
                <td>{{ $biography->father_occupation ?? '' }}</td>
                <td>{{ $biography->mother_language ?? '' }}</td>

                {{-- Permanent (3) --}}
                <td>{{ $biography->permanent_province ?? '' }}</td>
                <td>{{ $biography->permanent_district ?? '' }}</td>
                <td>{{ $biography->permanent_village ?? '' }}</td>

                {{-- Current (3) --}}
                <td>{{ $biography->current_province ?? '' }}</td>
                <td>{{ $biography->current_district ?? '' }}</td>
                <td>{{ $biography->current_village ?? '' }}</td>

                {{-- Relatives (5) --}}
                <td>{{ $biography->brother_name ?? '' }}</td>
                <td>{{ $biography->uncle_name ?? '' }}</td>
                <td>{{ $biography->maternal_uncle_name ?? '' }}</td>
                <td>{{ $biography->paternal_uncle_son_name ?? '' }}</td>
                <td>{{ $biography->maternal_uncle_son_name ?? '' }}</td>

                {{-- Notes --}}
                <td class="w-notes">{{ $biography->notes ?? '' }}</td>
            </tr>
        </table>

        <div class="footer">
            فورم هذا برای یک شاگرد جدیدالشمول توسط پدر و یا برادر شاگرد طور دقیق خانه پری می‌گردد، در صورتيکه
            فورمهٔ فوق توسط اولیاء محترم درست خانه پری نگردد، مسوولیت به دوش فامیل‌های محترم ایشان می‌باشد.
        </div>
        <div class="footer-endorse">مدیریت عمومی نظارت تعلیمی</div>
    </div>

    <button class="print-button" onclick="window.print()">چاپ جدول</button>
</body>

</html>
