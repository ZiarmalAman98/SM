<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>راپور نتایج امتحان - {{ $examData['examTypeName'] }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Vazirmatn font -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css">

    <style>
        @page {
            size: A4 landscape;
            margin: 5mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Vazirmatn, Tahoma, Arial, sans-serif;
            color: #000;
            line-height: 1.4;
            background: #fff;
            direction: rtl;
            text-align: right;
        }

        .container {
            width: 100%;
            max-width: 297mm;
            margin: 0 auto;
            padding: 5mm;
            background: #fff;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 8px 0;
            border-bottom: 1px solid #000;
        }

        .logo-left,
        .logo-right {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1px solid #ccc;
            padding: 3px;
        }

        .logo-left img,
        .logo-right img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .ministry-info {
            flex: 1;
            text-align: center;
            padding: 0 10px;
            font-size: 11px;
            font-weight: 600;
            line-height: 1.3;
        }

        .ministry-title {
            font-size: 12px;
            font-weight: 700;
            margin: 0 0 2px 0;
            color: #000;
        }

        .ministry-subtitle {
            font-size: 10px;
            font-weight: 600;
            margin: 0 0 1px 0;
            color: #000;
        }

        .ministry-name {
            font-size: 10px;
            font-weight: 600;
            margin: 0;
            color: #000;
        }

        .document-title {
            font-size: 14px;
            font-weight: 700;
            margin: 8px 0 10px 0;
            text-align: center;
            color: #000;
        }

        .report-info-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            direction: rtl;
        }

        .report-info-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #1e40af, #3b82f6);
        }

        .info-row {
            display: flex;
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
            direction: rtl;
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-label {
            font-weight: 700;
            min-width: 140px;
            color: #1e40af;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: #374151;
            font-weight: 500;
            font-size: 14px;
        }

        .exam-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #000;
            font-size: 10px;
        }

        .exam-table th,
        .exam-table td {
            border: 1px solid #000;
            padding: 3px 2px;
            text-align: center;
            font-size: 8px;
            font-family: Vazirmatn, Tahoma, Arial, sans-serif;
        }

        .exam-table th {
            background: #f0f0f0;
            color: #000;
            font-weight: 700;
            font-size: 8px;
            padding: 4px 2px;
        }

        .exam-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .exam-table tr:nth-child(odd) {
            background: #fff;
        }

        .exam-table tr:hover {
            background: #e8e8e8;
        }

        .total-row {
            background: #e0e0e0 !important;
            color: #000 !important;
            font-weight: 700 !important;
        }

        .grand-total-row {
            background: #d0d0d0 !important;
            color: #000 !important;
            font-weight: 800 !important;
            font-size: 10px !important;
        }

        .footer {
            margin-top: 10px;
            padding: 8px 0;
            border-top: 1px solid #ccc;
            background: #fff;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #d1d5db;
        }

        .footer-bottom p {
            margin: 0;
            font-size: 12px;
            color: #6b7280;
            font-style: italic;
        }

        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #ffffff;
            border: none;
            padding: 15px 30px;
            border-radius: 50px;
            cursor: pointer;
            font-family: Vazirmatn, Tahoma, Arial, sans-serif;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 25px rgba(30, 64, 175, 0.3);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .print-button:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(30, 64, 175, 0.4);
        }

        .print-button:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        }

        @media print {
            html,
            body,
            .exam-table th,
            .exam-table td {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: A4 landscape;
                margin: 5mm;
            }

            body {
                background: #fff !important;
                -webkit-print-color-adjust: exact;
            }

            .container {
                width: 100%;
                max-width: 297mm;
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 5mm !important;
            }

            .header {
                padding: 8px 0 !important;
                border-radius: 0 !important;
                background: #fff !important;
                border-bottom: 1px solid #000 !important;
                margin-bottom: 8px !important;
            }

            .header::before {
                display: none !important;
            }

            .logo-left,
            .logo-right {
                width: 50px !important;
                height: 50px !important;
                padding: 2px !important;
                background: #fff !important;
                border: 1px solid #ccc !important;
            }

            .school-name {
                font-size: 22px !important;
                text-shadow: none !important;
            }

            .school-address {
                font-size: 13px !important;
                text-shadow: none !important;
            }

            .document-title {
                font-size: 18px !important;
                margin: 15px 0 !important;
                padding: 10px 0 !important;
            }

            .document-title::after {
                width: 60px !important;
                height: 2px !important;
            }

            .report-info-section {
                padding: 15px !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
                background: #f8fafc !important;
            }

            .report-info-section::before {
                width: 2px !important;
            }

            .info-row {
                margin-bottom: 8px !important;
                padding: 5px 0 !important;
            }

            .info-label {
                min-width: 100px !important;
                font-size: 12px !important;
            }

            .info-value {
                font-size: 12px !important;
            }

            .exam-table {
                box-shadow: none !important;
                border-radius: 0 !important;
                border: 1px solid #e2e8f0 !important;
            }

            .exam-table th,
            .exam-table td {
                padding: 2px 1px !important;
                font-size: 7px !important;
            }

            .exam-table th {
                background: #f0f0f0 !important;
            }

            .exam-table tr:nth-child(even) {
                background: #f1f5f9 !important;
            }

            .exam-table tr:nth-child(odd) {
                background: #ffffff !important;
            }

            .total-row {
                background: #1e40af !important;
            }

            .grand-total-row {
                background: #059669 !important;
            }

            .footer {
                margin-top: 20px !important;
                padding: 15px 0 !important;
                border-top: 1px solid #e2e8f0 !important;
                background: #f8fafc !important;
                border-radius: 0 !important;
            }

            .footer-bottom {
                padding-top: 10px !important;
                border-top: 1px solid #d1d5db !important;
            }

            .footer-bottom p {
                font-size: 10px !important;
            }

            .print-button {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <!-- Left Logo -->
            <div class="logo-left">
                <img src="{{ asset('afghanistan_school.png') }}" alt="Ministry of Education">
            </div>

            <!-- Ministry Information -->
            <div class="ministry-info">
                <div class="ministry-title">امارت اسلامی افغانستان</div>
                <div class="ministry-subtitle">وزارت معارف ریاست معارف شهر کابل</div>
                <div class="ministry-subtitle">آمریت حوزه پانزدهم تعلیمی</div>
                <div class="ministry-name">{{ $schoolName }}</div>
            </div>

            <!-- Right Logo -->
            <div class="logo-right">
                <img src="{{ $appLogoUrl ?? asset('schools/cosmos.png') }}" alt="School Logo">
            </div>
        </div>

        <!-- Document Title -->
        <h2 class="document-title">{{ $academicYear }} خلص نتایج امتحانات {{ $examData['examTypeName'] }} سال</h2>

        <!-- Report Information -->
        {{-- <div class="report-info-section">
            <div class="info-row">
                <span class="info-label">شاخه:</span>
                <span class="info-value">{{ $branch->branch_name }}</span>
            </div>
            @if($class)
            <div class="info-row">
                <span class="info-label">صنف:</span>
                <span class="info-value">{{ $class->class_name }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">نوع امتحان:</span>
                <span class="info-value">{{ $examData['examTypeName'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">سال تحصیلی:</span>
                <span class="info-value">{{ $academicYear }}</span>
            </div>
        </div> --}}

        <!-- Exam Results Table -->
        @if(count($examData['classResults']) > 0)
        <table class="exam-table">
            <thead>
                <tr>
                    <th rowspan="2">شماره</th>
                    <th rowspan="2">صنوف</th>
                    <th colspan="3">تعداد داخله</th>
                    <th colspan="3">شامل امتحان</th>
                    <th colspan="3">موفق</th>
                    <th colspan="3">تلاش بیشتر</th>
                    <th colspan="3">معذرت</th>
                    <th colspan="3">غایب</th>
                </tr>
                <tr>
                    <th>ذكور</th>
                    <th>اناث</th>
                    <th>مجموع</th>
                    <th>ذكور</th>
                    <th>اناث</th>
                    <th>مجموع</th>
                    <th>ذكور</th>
                    <th>اناث</th>
                    <th>مجموع</th>
                    <th>ذكور</th>
                    <th>اناث</th>
                    <th>مجموع</th>
                    <th>ذكور</th>
                    <th>اناث</th>
                    <th>مجموع</th>
                    <th>ذكور</th>
                    <th>اناث</th>
                    <th>مجموع</th>
                </tr>
            </thead>
            <tbody>
                @foreach($examData['classResults'] as $index => $classResult)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $classResult['class_name'] }}</strong></td>
                    <!-- تعداد داخله -->
                    <td>{{ $classResult['male_total'] }}</td>
                    <td>{{ $classResult['female_total'] }}</td>
                    <td>{{ $classResult['total_students'] }}</td>
                    <!-- شامل امتحان -->
                    <td>{{ $classResult['male_present'] }}</td>
                    <td>{{ $classResult['female_present'] }}</td>
                    <td>{{ $classResult['present_students'] }}</td>
                    <!-- موفق -->
                    <td>{{ $classResult['male_pass'] }}</td>
                    <td>{{ $classResult['female_pass'] }}</td>
                    <td>{{ $classResult['pass_students'] }}</td>
                    <!-- تلاش بیشتر -->
                    <td>{{ $classResult['male_more_effort'] ?? 0 }}</td>
                    <td>{{ $classResult['female_more_effort'] ?? 0 }}</td>
                    <td>{{ $classResult['more_effort'] }}</td>
                    <!-- معذرت -->
                    <td>{{ $classResult['male_excused'] ?? 0 }}</td>
                    <td>{{ $classResult['female_excused'] ?? 0 }}</td>
                    <td>{{ $classResult['excused_absent'] }}</td>
                    <!-- غایب -->
                    <td>{{ $classResult['male_absent'] ?? 0 }}</td>
                    <td>{{ $classResult['female_absent'] ?? 0 }}</td>
                    <td>{{ $classResult['absent_students'] }}</td>
                </tr>
                @endforeach

                <!-- Total Row -->
                <tr class="total-row">
                    <td></td>
                    <td><strong>مجموع</strong></td>
                    <!-- تعداد داخله -->
                    <td><strong>{{ $examData['totalStats']['male_total'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_total'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['total_students'] }}</strong></td>
                    <!-- شامل امتحان -->
                    <td><strong>{{ $examData['totalStats']['male_present'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_present'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['present_students'] }}</strong></td>
                    <!-- موفق -->
                    <td><strong>{{ $examData['totalStats']['male_pass'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_pass'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['pass_students'] }}</strong></td>
                    <!-- تلاش بیشتر -->
                    <td><strong>{{ $examData['totalStats']['male_more_effort'] ?? 0 }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_more_effort'] ?? 0 }}</strong></td>
                    <td><strong>0</strong></td>
                    <!-- معذرت -->
                    <td><strong>{{ $examData['totalStats']['male_excused'] ?? 0 }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_excused'] ?? 0 }}</strong></td>
                    <td><strong>0</strong></td>
                    <!-- غایب -->
                    <td><strong>{{ $examData['totalStats']['male_absent'] ?? 0 }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_absent'] ?? 0 }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['absent_students'] }}</strong></td>
                </tr>

                <!-- Grand Total Row -->
                <tr class="grand-total-row">
                    <td></td>
                    <td><strong>مجموعه عمومی</strong></td>
                    <!-- تعداد داخله -->
                    <td><strong>{{ $examData['totalStats']['male_total'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_total'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['total_students'] }}</strong></td>
                    <!-- شامل امتحان -->
                    <td><strong>{{ $examData['totalStats']['male_present'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_present'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['present_students'] }}</strong></td>
                    <!-- موفق -->
                    <td><strong>{{ $examData['totalStats']['male_pass'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_pass'] }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['pass_students'] }}</strong></td>
                    <!-- تلاش بیشتر -->
                    <td><strong>{{ $examData['totalStats']['male_more_effort'] ?? 0 }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_more_effort'] ?? 0 }}</strong></td>
                    <td><strong>0</strong></td>
                    <!-- معذرت -->
                    <td><strong>{{ $examData['totalStats']['male_excused'] ?? 0 }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_excused'] ?? 0 }}</strong></td>
                    <td><strong>0</strong></td>
                    <!-- غایب -->
                    <td><strong>{{ $examData['totalStats']['male_absent'] ?? 0 }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['female_absent'] ?? 0 }}</strong></td>
                    <td><strong>{{ $examData['totalStats']['absent_students'] }}</strong></td>
                </tr>
            </tbody>
        </table>
        @else
        <div style="text-align: center; padding: 40px; font-size: 16px; color: #666;">
            <p>هیچ داده‌ای برای این شاخه و نوع امتحان یافت نشد.</p>
            <p>لطفاً مطمئن شوید که:</p>
            <ul style="text-align: right; margin: 20px 0;">
                <li>دانش‌آموزانی در این شاخه ثبت شده باشند</li>
                <li>امتحانات مربوط به این نوع امتحان ایجاد شده باشند</li>
                <li>نتایج امتحانات وارد شده باشند</li>
            </ul>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p style="text-align: justify; font-size: 9px; line-height: 1.4; margin: 5px 0;">
                قرار شرح فوق احصاییه خلص جدول نتایج امتحان {{ $examData['examTypeName'] }} شاگردان مکتب خصوصی  از بابت سال {{ $academicYear }} ترتیب و تقدیم است.
            </p>
            @php
                $examReportContact = implode(' | ', array_filter([
                    $settings['school_address_line'] ?? '',
                    $settings['support_phone_display'] ?? '',
                ]));
            @endphp
            @if($examReportContact !== '')
                <p style="text-align: center; font-size: 9px; margin: 6px 0 0;">
                    {{ $examReportContact }}
                </p>
            @endif
        </div>
    </div>

        <button class="print-button" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
                <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
            </svg>
            چاپ راپور
        </button>
</body>

</html>
