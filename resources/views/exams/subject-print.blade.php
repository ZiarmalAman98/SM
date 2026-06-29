<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>گزارش نمرات مضمون | {{ $subject->name }} | {{ $class->class_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Formal fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600;700&display=swap"
        rel="stylesheet">

    <style>
        @page {
            size: A4;
            margin: 10mm;
            /* tighter margin for more space */
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            font-family: "Noto Naskh Arabic", "Amiri", Tahoma, Arial, sans-serif;
            color: #111;
        }

        .toolbar {
            display: none;
            margin-bottom: 12px;
        }

        @media screen {
            .toolbar {
                display: flex;
                gap: 8px;
            }
        }

        h1 {
            font-family: "Amiri", serif;
            font-weight: 700;
            font-size: 16pt;
            /* smaller for fit */
            text-align: center;
            margin: 2px 0;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            margin: 6px 0 10px;
            font-size: 10pt;
        }

        .meta div {
            background: #f6f7fb;
            padding: 4px 6px;
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            /* shrink font for fit */
        }

        th,
        td {
            border: 1px solid #d7dbe6;
            padding: 4px 5px;
        }

        thead th {
            background: #2f5496;
            color: #fff;
            text-align: center;
        }

        .num {
            text-align: center;
        }

        .ltr {
            direction: ltr;
            unicode-bidi: embed;
        }

        .footer {
            margin-top: 8px;
            display: flex;
            justify-content: space-between;
            font-size: 9pt;
            color: #555;
        }

        .note {
            margin-top: 6px;
            font-size: 9pt;
            color: #333;
        }

        /* Logos */
        .logo-left {
            position: absolute;
            top: 10mm;
            left: 10mm;
            width: 25mm;
            /* smaller to save space */
            height: auto;
        }

        .logo-right {
            position: absolute;
            top: 10mm;
            right: 10mm;
            width: 25mm;
            height: auto;
        }

        @media print {
            .toolbar {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                zoom: 0.85;
                /* ✅ scale everything down to fit one page */
            }

            table tr {
                page-break-inside: avoid;
            }

            .footer,
            .note {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()">{{ __('Print') }}</button>
    </div>

    {{-- Logos --}}
    <img src="{{ asset('afghanistan_school.png') }}" alt="Logo" class="logo-left">
    <img src="{{ $settings['app_logo_url'] ?? asset('schools/cosmos.png') }}" alt="Logo" class="logo-right">

    <h1>وزات معارف</h1>
    <h1>{{ env('PRESIDENT') }}</h1>
    <h1>{{ env('DEPARTMENT') }}</h1>
    <h1>{{ $settings['app_name'] ?? env('SCHOOL_NAME_FA') }}</h1>

    <div class="meta">
        <div><strong>نگران:</strong> {{ $class->teacher->name ?? '—' }}</div>
        <div><strong>صنف:</strong> {{ $class->class_name }}</div>
        <div><strong>مضمون:</strong> {{ $subject->name }}</div>
        <div><strong>امتحان:</strong>
            @if ($examType === 'mid_term')
                چهارونیم ماهه
            @elseif ($examType === 'final')
                سالانه
            @else
                —
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:40px">#</th>
                <th>اسم</th>
                <th>ولد</th>
                <th>تحریری</th>
                <th>تقریری</th>
                <th>کارخانگی</th>
                <th>فعالیت صنفی</th>
                <th>مجموعه</th>
                <th>نمره به حروف</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @foreach ($students as $s)
                @php
                    $rows = $results->get($s->id) ?? collect();
                    $currentResult = $rows->first();

                    // Get detailed marks from the current exam result
                    $writtenMarks = $currentResult->written_marks ?? 0;
                    $recitalMarks = $currentResult->recital_marks ?? 0;
                    $homeworkMarks = $currentResult->homework_marks ?? 0;
                    $activityMarks = $currentResult->class_activity_marks ?? 0;
                    $markInWords = $currentResult->mark_in_words ?? '';
                    $totalMarks = $currentResult->marks ?? 0;
                @endphp
                <tr>
                    <td class="num">{{ $i++ }}</td>
                    <td>{{ trim(($s->name ?? '') . ' ' . ($s->last_name ?? '')) }}</td>
                    <td>{{ $s->father_name ?? '' }}</td>
                    <td class="num">{{ $writtenMarks > 0 ? number_format($writtenMarks, 1) : '' }}</td>
                    <td class="num">{{ $recitalMarks > 0 ? number_format($recitalMarks, 1) : '' }}</td>
                    <td class="num">{{ $homeworkMarks > 0 ? number_format($homeworkMarks, 1) : '' }}</td>
                    <td class="num">{{ $activityMarks > 0 ? number_format($activityMarks, 1) : '' }}</td>
                    <td class="num">{{ $totalMarks > 0 ? number_format($totalMarks, 1) : '' }}</td>
                    <td>{{ $markInWords }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="note">
        یادداشت: این گزارش صرفاً مربوط به نمرات مضمون <strong>{{ $subject->name }}</strong> در صنف
        <strong>{{ $class->class_name }}</strong> می‌باشد.
    </div>

    <div class="footer">
        <div>تهیه شده توسط: {{ auth()->user()->name ?? '—' }}</div>
        <div class="ltr">{{ $settings['support_phone_display'] ?? '' }}</div>
    </div>

    <script>
        // Auto-print on window load
        window.addEventListener('load', function() {
            // Small delay to ensure all content is loaded
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>

</html>
