<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>کارت سوانح متعلمين مكاتب - {{ $biography->name ?? '—' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Vazirmatn font -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css">

    <style>
        @page {
            size: A4;
            margin: 15mm;
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
            line-height: 1.4;
            background-color: #f8f9fa;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 10mm;
            background: #fff;
            box-shadow: 0 0 15px rgba(0, 0, 0, .1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            position: relative;
        }

        .ministry-logo {
            display: flex;
            align-items: center;
            flex: 0 0 auto;
        }

        .logo-image {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }

        .ministry-info {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            white-space: nowrap;
        }

        .student-photo-header {
            width: 100px;
            height: 120px;
            border: 2px solid #2c3e50;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            flex: 0 0 auto;
        }

        .photo-placeholder-header {
            text-align: center;
            color: #7f8c8d;
            font-size: 10px;
        }

        .photo-stamp-header {
            position: absolute;
            bottom: -5px;
            right: -5px;
            width: 35px;
            height: 35px;
            border: 2px solid #2c3e50;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
            line-height: 1.1;
        }

        .ministry-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: #2c3e50;
        }

        .ministry-subtitle {
            font-size: 14px;
            margin: 5px 0;
            color: #7f8c8d;
        }

        .document-title {
            font-size: 20px;
            font-weight: 700;
            margin: 15px 0;
            color: #2c3e50;
            text-align: center;
        }

         .student-info {
             margin-bottom: 20px;
         }

         .student-details {
             display: grid;
             grid-template-columns: 1fr 1fr;
             gap: 15px;
         }

         .info-row {
             display: flex;
             align-items: center;
             margin-bottom: 10px;
         }

         .info-label {
             font-weight: 600;
             color: #2c3e50;
             font-size: 12px;
             margin-right: 5px;
         }

         .info-value {
             flex: 1;
             padding: 5px 0;
             border-bottom: 1px solid #bdc3c7;
             min-height: 20px;
             font-size: 14px;
         }

        .tables-section {
            margin-top: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border: 2px solid #2c3e50;
        }

        .table th,
        .table td {
            border: 1px solid #7f8c8d;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }

        .table th {
            background-color: #2c3e50;
            color: #fff;
            font-weight: 700;
        }

        .table td {
            background: #fff;
            min-height: 30px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin: 15px 0 10px;
            color: #2c3e50;
            text-align: center;
        }

        .enrollment-section th {
            background-color: #27ae60;
        }

        .separation-section th {
            background-color: #e74c3c;
        }

        .relatives-section th {
            background-color: #3498db;
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
            html,
            body,
            .table th,
            .table td,
            .photo-stamp {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: A4;
                margin: 15mm;
            }

            .container {
                width: 100%;
                padding: 0 !important;
                box-shadow: none !important;
                background: #fff !important;
            }

            .table {
                page-break-inside: avoid;
                box-shadow: none;
            }

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
        <!-- Header Section -->
        <div class="header">
            <div class="header-top">
                <div class="ministry-logo">
                    <img src="{{ $settings['app_logo_url'] ?? asset('schools/cosmos.png') }}" alt="وزارت معارف" class="logo-image">
                    <div class="ministry-info">
                        <h1 class="ministry-title">وزارت معارف</h1>
                        <p class="ministry-subtitle">ریاست معارف شهر کابل</p>
                        <p class="ministry-subtitle">آمریت حوزه پانزهم تعلیمی</p>
                        <p class="ministry-subtitle">{{ $settings['app_name'] ?? $schoolName ?? 'مكتب' }}</p>
                    </div>
                </div>
                <div class="student-photo-header">
                    <div class="photo-placeholder-header">
                        عکس متعلم<br>
                        Student Photo
                    </div>
                    <div class="photo-stamp-header">
                        وزارت<br>معارف<br>ریاست معارف<br>شهر کابل
                    </div>
                </div>
            </div>
            <h2 class="document-title">کارت سوانح متعلمين مكاتب</h2>
        </div>

         <!-- Student Information Section -->
         <div class="student-info">
             <div class="student-details">
                 <div class="info-row">
                     <span class="info-label">نام متعلم :</span>
                     <span class="info-value">{{ $biography->name ?? '' }}</span>
                 </div>
                 <div class="info-row">
                     <span class="info-label">نام پدر متعلم :</span>
                     <span class="info-value">{{ $biography->father_name ?? '' }}</span>
                 </div>
                 <div class="info-row">
                     <span class="info-label">نام پدر کلان متعلم :</span>
                     <span class="info-value">{{ $biography->grand_father_name ?? '' }}</span>
                 </div>
                 <div class="info-row">
                     <span class="info-label">سال تولد متعلم :</span>
                     <span class="info-value">{{ $biography->birth_year ?? $biography->age ?? '' }}</span>
                 </div>
                 <div class="info-row">
                     <span class="info-label">سکونت اصلی متعلم :</span>
                     <span class="info-value">{{ $biography->permanent_village ?? '' }}</span>
                 </div>
                 <div class="info-row">
                     <span class="info-label">سکونت فعلی متعلم :</span>
                     <span class="info-value">{{ $biography->current_village ?? '' }}</span>
                 </div>
                 <div class="info-row">
                     <span class="info-label">وظیفه پدر متعلم :</span>
                     <span class="info-value">{{ $biography->father_occupation ?? '' }}</span>
                 </div>
                 <div class="info-row">
                     <span class="info-label">محل بودباش پدر متعلم :</span>
                     <span class="info-value">{{ $biography->permanent_district ?? '' }}</span>
                 </div>
                 <div class="info-row">
                     <span class="info-label">ولی متعلم :</span>
                     <span class="info-value">{{ $biography->guardian_name ?? $biography->father_name ?? '' }}</span>
                 </div>
                 <div class="info-row">
                     <span class="info-label">نمبر تذکره :</span>
                     <span class="info-value">{{ $biography->citizenship_tazkira ?? '' }}</span>
                 </div>
                 <div class="info-row">
                     <span class="info-label">زبان مادری :</span>
                     <span class="info-value">{{ $biography->mother_language ?? '' }}</span>
                 </div>
             </div>
         </div>

        <!-- Details Tables Section -->
        <div class="tables-section">
            <div class="section-title">تفصیلات</div>
            <table class="table">
                <thead>
                    <tr>
                        <th colspan="3" class="enrollment-section">شمولیت</th>
                        <th colspan="3" class="separation-section">انفکاک</th>
                    </tr>
                    <tr>
                        <th>تاریخ شمولیت</th>
                        <th>نمبر مکتوب</th>
                        <th>در صنف</th>
                        <th>تاریخ انفکاک</th>
                        <th>نمبر مکتوب</th>
                        <th>از صنف</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $yearJalali }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <table class="table">
                <thead>
                    <tr>
                        <th colspan="2" class="relatives-section">اقارب نزدیک متعلم</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>برادر</td>
                        <td>{{ $biography->brother_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>برادرزاده</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>کاکا</td>
                        <td>{{ $biography->uncle_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>ماما</td>
                        <td>{{ $biography->maternal_uncle_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>پسرکاکا</td>
                        <td>{{ $biography->paternal_uncle_son_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>پسر ماما</td>
                        <td>{{ $biography->maternal_uncle_son_name ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <button class="print-button" onclick="window.print()">چاپ کارت</button>
</body>

</html>
