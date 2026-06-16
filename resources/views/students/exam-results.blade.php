<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>کارنامه تعلیمی</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;600;700&family=Noto+Sans+Arabic:wght@400;500;600;700&display=swap');

        :root {
            --primary-color: #2563eb;
            --primary-light: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary-color: #059669;
            --secondary-light: #10b981;
            --accent-color: #f59e0b;
            --neutral-dark: #374151;
            --neutral-light: #f8fafc;
            --border-color: #e5e7eb;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        body {
            font-family: 'Noto Sans Arabic', 'Amiri', serif;
            background: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 0;
            box-sizing: border-box;
        }

        .report-container {
            background: white;
            width: 100%;
            height: 100%;
            padding: 5mm;
            box-sizing: border-box;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 3mm;
            position: relative;
            overflow: hidden;
            border-radius: 2mm;
            margin-bottom: 3mm;
        }

        .logo-container {
            width: 20mm;
            height: 20mm;
            background: white;
            border-radius: 3mm;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .logo {
            width: 16mm;
            height: 16mm;
            object-fit: contain;
        }

        .header-text {
            position: relative;
            z-index: 1;
        }

        .header-text h1 {
            font-size: 4.5mm;
            margin: 0;
            line-height: 1.2;
        }

        .header-text h2 {
            font-size: 4mm;
            margin: 0;
            line-height: 1.2;
        }

        .header-text h3 {
            font-size: 3.5mm;
            margin: 0;
            line-height: 1.2;
        }

        .student-info {
            background: var(--neutral-light);
            border-radius: 2mm;
            padding: 3mm;
            margin: 0 0 3mm 0;
            position: relative;
            border: 0.5mm solid var(--border-color);
            text-align: center;
        }

        .student-info p {
            font-size: 3.5mm;
            margin: 0;
            line-height: 1.4;
        }

        .modern-table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 2mm;
            overflow: hidden;
            margin-bottom: 3mm;
            font-size: 3mm;
            page-break-inside: avoid;
        }

        .modern-table th {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 2mm;
            text-align: center;
            font-weight: 600;
            border: 0.25mm solid var(--border-color);
        }

        .modern-table td {
            padding: 2mm;
            text-align: center;
            border: 0.25mm solid var(--border-color);
            font-weight: 500;
        }

        .total-row {
            background: #fef3c7;
            font-weight: 700;
        }

        .result-row {
            background: #d1fae5;
            font-weight: 700;
        }

        .result-row .status-text {
            color: var(--secondary-color);
            font-weight: 700;
        }

        .grade-row {
            background: #e0e7ff;
            font-weight: 600;
        }

        .attendance-table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 2mm;
            overflow: hidden;
            margin-bottom: 3mm;
            font-size: 3mm;
            page-break-inside: avoid;
        }

        .attendance-table th {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--secondary-light) 100%);
            color: white;
            padding: 2mm;
            text-align: center;
            font-weight: 600;
            border: 0.25mm solid var(--border-color);
        }

        .attendance-table td {
            padding: 2mm;
            text-align: center;
            border: 0.25mm solid var(--border-color);
            font-weight: 500;
        }

        .student-code {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            padding: 2mm;
            border-radius: 2mm;
            text-align: center;
            font-weight: 600;
            margin-bottom: 3mm;
            font-size: 3.5mm;
        }

        .signature-table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 2mm;
            overflow: hidden;
            font-size: 3mm;
            page-break-inside: avoid;
        }

        .signature-table th {
            background: linear-gradient(135deg, var(--neutral-dark) 0%, #4b5563 100%);
            color: white;
            padding: 2mm;
            text-align: center;
            font-weight: 600;
            border: 0.25mm solid var(--border-color);
        }

        .signature-table td {
            padding: 6mm 2mm;
            text-align: center;
            border: 0.25mm solid var(--border-color);
            font-weight: 500;
        }

        .content-wrapper {
            padding: 0;
        }

        .section-title {
            position: relative;
            padding-right: 4mm;
            margin: 2mm 0;
            font-weight: 700;
            color: var(--neutral-dark);
            font-size: 4mm;
        }

        .section-title::before {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 2mm;
            height: 6mm;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border-radius: 1mm;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 1mm 2mm;
            border-radius: 3mm;
            font-weight: 600;
            font-size: 2.5mm;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .flex-container {
            display: flex;
            gap: 3mm;
            margin-bottom: 3mm;
        }

        .main-content {
            width: 67%;
        }

        .side-content {
            width: 33%;
        }

        /* Print-specific styles */
        @media print {
            body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                background: white;
            }

            .report-container {
                box-shadow: none;
                padding: 5mm;
                height: 287mm;
            }

            .header-section, .student-info, .modern-table, .attendance-table, .student-code, .signature-table {
                page-break-inside: avoid;
            }

            /* Hide interactive elements for print */
            .logo-container:hover,
            .modern-table:hover {
                transform: none;
                box-shadow: none;
            }

            /* Ensure backgrounds and colors print correctly */
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-message {
                display: none;
            }
        }

        /* Screen-only styles */
        @media screen {
            body {
                background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
                padding: 5mm;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .report-container {
                box-shadow: 0 5mm 10mm -3mm rgba(0, 0, 0, 0.15);
                border-radius: 3mm;
            }

            .print-message {
                text-align: center;
                margin-top: 3mm;
                color: var(--neutral-dark);
                font-size: 3.5mm;
            }
        }
    </style>
</head>

<body>
    <div class="report-container">
        <!-- Header section -->
        <div class="header-section">
            <div class="flex justify-between items-center">
                <div class="logo-container">
                    <img src="@if (isset($settings['app_logo'])) {{ asset('storage/' . $settings['app_logo']) }}
                     @else
                             {{ asset('schools/cosmos.png') }} @endif"
                        alt="Company Logo" class="logo">
                </div>
                <div class="text-center header-text">
                    <h1>د افغانستان اسلامي امارت</h1>
                    <h2>د پوهنې وزارت</h2>
                    <h3>د لغمان ولایت د پوهنې ریاست</h3>
                    <h3>{{ env('SCHOOL_NAME_FA') }}</h3>
                </div>
                <div class="logo-container">
                    <img src="{{ asset('afghanistan_school.png') }}" alt="Company Logo" class="logo">
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            <!-- Student information -->
            <div class="student-info">
                <p>
                    <i class="fas fa-user-graduate ml-2 text-blue-600"></i>
                    اسم: <span class="text-blue-600">{{ $user->name }}</span> &nbsp; | &nbsp;
                    <i class="fas fa-chalkboard-teacher ml-2 text-green-600"></i>
                    صنف: <span class="text-green-600">{{ \App\Models\SchoolClass::find($_GET['class_id'])->class_name ?? '---' }}</span> &nbsp; | &nbsp;
                    <i class="fas fa-calendar-alt ml-2 text-purple-600"></i>
                    سال تعلیمی: <span class="text-purple-600">{{ now()->format('Y') - 621 }}</span>
                </p>
            </div>

            <div class="flex-container">
                <!-- Main content - Grades -->
                <div class="main-content">
                    <h3 class="section-title">نمرات</h3>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th rowspan="2">مضامین</th>
                                <th colspan="2">امتحان</th>
                                <th rowspan="2">مجموعه</th>
                                <th rowspan="2">درجه</th>
                            </tr>
                            <tr>
                                <th>4/5 ماهه</th>
                                <th>سالانه</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subjects as $subject)
                                <tr>
                                    <td class="font-semibold text-gray-700 text-right">{{ $subject['name'] }}</td>
                                    <td>{{ $subject['mid_term'] }}</td>
                                    <td>{{ $subject['final'] }}</td>
                                    <td class="font-semibold">{{ $subject['total'] }}</td>
                                    <td>
                                        @if($subject['total'] >= 90)
                                            <span class="badge badge-success">A</span>
                                        @elseif($subject['total'] >= 80)
                                            <span class="badge badge-success">B</span>
                                        @elseif($subject['total'] >= 70)
                                            <span class="badge badge-warning">C</span>
                                        @elseif($subject['total'] >= 60)
                                            <span class="badge badge-warning">D</span>
                                        @else
                                            <span class="badge badge-danger">F</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="total-row">
                                <td>مجموعه</td>
                                <td>{{ $result['mid_term'] }}</td>
                                <td>{{ $result['final'] }}</td>
                                <td>{{ $result['total'] }}</td>
                                <td></td>
                            </tr>
                            <tr class="result-row">
                                <td>نتیجه</td>
                                <td class="status-text" colspan="3">
                                    <i class="fas fa-check-circle ml-2"></i>
                                    {{ $result['status'] }}
                                </td>
                                <td></td>
                            </tr>
                            <tr class="grade-row">
                                <td>درجه</td>
                                <td colspan="3" class="text-blue-600 font-bold">
                                    <i class="fas fa-award ml-2"></i>
                                    {{ $result['grade'] }}
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Side content - Attendance and signatures -->
                <div class="side-content">
                    <h3 class="section-title">حضوری</h3>
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th colspan="2">امتحان</th>
                                <th colspan="3">کیفیت حضوری</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-gray-50">
                                <td class="font-semibold"></td>
                                <td class="font-semibold text-green-600">حاضر</td>
                                <td class="font-semibold text-red-600">غیر حاضر</td>
                                <td class="font-semibold text-yellow-600">رخصت</td>
                                <td class="font-semibold text-orange-600">مریض</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">4/5 ماهه</td>
                                <td class="text-green-600">{{ $attendance['mid_term']['present'] }}</td>
                                <td class="text-red-600">{{ $attendance['mid_term']['absent'] }}</td>
                                <td class="text-yellow-600">{{ $attendance['mid_term']['leave'] ?? '-' }}</td>
                                <td class="text-orange-600">{{ $attendance['mid_term']['sick'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">سالانه</td>
                                <td class="text-green-600">{{ $attendance['final']['present'] }}</td>
                                <td class="text-red-600">{{ $attendance['final']['absent'] }}</td>
                                <td class="text-yellow-600">{{ $attendance['final']['leave'] ?? '-' }}</td>
                                <td class="text-orange-600">{{ $attendance['final']['sick'] ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="student-code">
                        <p>
                            <i class="fas fa-id-card ml-2"></i>
                            نمبر اساس : {{ $user->student->student_code ?? '---' }}
                        </p>
                    </div>

                    <h3 class="section-title">امضا</h3>
                    <table class="signature-table">
                        <thead>
                            <tr>
                                <th>امتحان</th>
                                <th>امضاء نگران</th>
                                <th>امضاء مدیر</th>
                                <th>امضاء ولی</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-semibold">چهارماهه</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="font-semibold">سالانه</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="print-message">
        <p>این کارنامه برای چاپ در اندازه A4 طراحی شده است. برای چاپ از گزینه Print در مرورگر خود استفاده کنید.</p>
    </div>

<script>
    // Auto-print when opened
    window.onload = function () {
        window.print();
    };
</script>
</body>

</html>
