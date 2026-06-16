<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>تقدیرنامه</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* === A4 print setup === */
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        /* Import Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;1,400;1,600&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400;1,700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Scheherazade+New:wght@400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;500;600;700&display=swap');

        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            background: #fff;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-family: "Vazirmatn", "Noto Sans Arabic", "Scheherazade New", "Amiri", "Reem Kufi", "Markazi Text", "Lateef", "Almarai", sans-serif;
        }

        /* Page Container */
        .page {
            position: relative;
            width: 190mm;
            height: 277mm;
            margin: 0 auto;
            background: #fff;
            border: 3px solid #1e40af;
            box-sizing: border-box;
            padding: 12mm;
            page-break-inside: avoid;
            overflow: hidden;
        }

        .inner-border {
            position: absolute;
            top: 3mm;
            left: 3mm;
            right: 3mm;
            bottom: 3mm;
            border: 1.5px solid #d97706;
            pointer-events: none;
        }

        /* Header */
        .header {
            position: relative;
            margin-bottom: 12mm;
            height: 35mm;
        }

        .logo-left,
        .logo-right {
            position: absolute;
            top: 0;
            width: 32mm;
            height: 32mm;
        }

        .logo-left { left: 0; }
        .logo-right { right: 0; }

        .school-name {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font-size: 22pt;
            font-weight: 700;
            color: #000;
            font-family: "Playfair Display", "Crimson Text", "Libre Baskerville", "Times New Roman", serif;
            font-style: italic;
            letter-spacing: 0.5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            white-space: nowrap;
            z-index: 10;
        }

        /* Date */
        .date-section {
            display: flex;
            justify-content: space-between;
            font-size: 11pt;
            margin-bottom: 8mm;
        }

        /* Titles */
        .main-title {
            text-align: center;
            margin: 8mm 0 10mm;
        }

        .title-persian {
            font-size: 48pt;
            font-weight: 700;
            color: #1e40af;
            line-height: 1.1;
            font-family: "Amiri", "Scheherazade New", "Noto Nastaliq Urdu", "Noto Serif", "Vazirmatn", "Noto Sans Arabic", "Markazi Text", "Lateef", "Almarai", serif;
            font-style: normal;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.15);
            text-transform: uppercase;
        }

        .title-english {
            font-size: 22pt;
            color: #1e40af;
            font-style: italic;
            margin-top: 3mm;
            font-family: "Playfair Display", "Crimson Text", "Libre Baskerville", "Times New Roman", serif;
            letter-spacing: 0.5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        /* Letter Body */
        .letter-body {
            text-align: justify;
            font-size: 13pt;
            line-height: 1.7;
            color: #333;
            margin: 0 5mm 10mm;
        }

        .student-info {
            color: #1e40af;
            font-weight: bold;
        }

        /* Signature */
        .signature-section {
            text-align: center;
            margin-top: 12mm;
        }

        .signature-line {
            border-bottom: 2px solid #333;
            width: 55mm;
            margin: 10mm auto 4mm;
        }

        /* Print Button (hidden on print) */
        .print-actions {
            position: fixed;
            bottom: 18px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
        }

        .btn {
            border: 1px solid #1f2937;
            background: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover { background: #f3f4f6; }

        /* Print Rules */
        @media print {
            .print-actions { display: none !important; }

            html, body {
                width: 210mm;
                height: 297mm;
                background: #fff;
                overflow: hidden;
            }

            .page {
                width: 190mm;
                height: 277mm;
                margin: 0;
                padding: 12mm;
                box-shadow: none;
                border: 3px solid #1e40af;
            }

            .inner-border {
                border: 1.5px solid #d97706;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="inner-border"></div>

        <!-- Header -->
        <div class="header">
            <div class="logo-left">
                <img src="@if (isset($settings['app_logo'])) {{ asset('storage/' . $settings['app_logo']) }} @else {{ asset('schools/cosmos.png') }} @endif"
                    alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>

            <div class="logo-right">
                <img src="{{ asset('Emrtlogo.png') }}" alt="School Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>

            <div class="school-name">
                {{ $settings['app_name'] ?? 'مکتب خصوصی شفا' }}
            </div>
        </div>

        <!-- Date -->
        <div class="date-section">
            <div>Date: {{ now()->format('d/m/Y') }}</div>
            <div>تاریخ: {{ \Morilog\Jalali\Jalalian::fromCarbon(now())->format('Y/m/d') }}</div>
        </div>

        <!-- Title -->
        <div class="main-title">
            <div class="title-persian">تقدیرنامه</div>
            <div class="title-english">Appreciation Letter</div>
        </div>

        <!-- Body -->
        <div class="letter-body">

            <p>
                مدیریت مکتب خصوصی
                تلاش‌های صادقانه، زحمات مستمر و کوشش‌های هوشمندانه دانش‌آموز ارجمند
                <span class="student-info">{{ $studentName ?: '..........' }}</span>
                فرزند گرامی
                <span class="student-info">{{ $fatherName ?: '..........' }}</span>
                و نیز همکاری دلسوزانه و سازنده والدین محترم ایشان در نیمه نخست سال تعلیمی،
                صمیمانه قدردانی می‌گردد.
            </p>
            <p>
                ضمن تحسین دستاوردهای درخشان این عزیز، موفقیت‌های روزافزون ایشان را
                در عرصه‌های علمی و معنوی از درگاه ایزد منان استدعا داریم.
            </p>
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <p>با حرمت</p>
            <p>مدیر مکتب خصوصی</p>
            <div class="signature-line"></div>
        </div>
    </div>

    <div class="print-actions">
        <button class="btn" onclick="window.print()">پرینت / Print</button>
    </div>
</body>
</html>
