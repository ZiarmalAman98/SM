<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>اطلاعنـامه - چاپ خودکار</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Amiri:wght@400;600;700&family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Vazirmatn:wght@400;500;600;700&display=swap');

        :root {
            --page-w: 210mm;
            --page-h: 297mm;

            --subjects-top: 80mm;
            --subjects-right: 4mm;
            --subjects-width: 98mm;
            --row-height: 1.5mm;

            --col-subject: 45%;
            --col-mid: 18%;
            --col-final: 18%;
            --col-total: 19%;

            --font-size: 3.5mm;
            --font-weight: 700;
            --text: #0b1847;
            --first-col-bg: #50588f;
            --first-col-text: #ffffff;
            --cell-vpad: 0.5mm;
            --border-color: #868484;
        }

        @page {
            size: A4;
            margin: 0;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            background: #fff;
            font-family: 'Noto Naskh Arabic', 'Amiri', 'Vazirmatn', Tahoma, sans-serif;
        }

        .sheet {
            position: relative;
            width: var(--page-w);
            height: var(--page-h);
            margin: 0 auto;
            background: #fff no-repeat center/contain;
            background-image: url("{{ asset('report_bg.png') }}");
        }

        /* Logo */
        .logo-left {
            position: absolute;
            top: 8mm;
            left: 32mm;
            width: 25mm;
            height: auto;
        }

        .subjects-overlay {
            position: absolute;
            top: var(--subjects-top);
            right: var(--subjects-right);
            width: var(--subjects-width);
            font-family: 'Noto Naskh Arabic', 'Amiri', 'Vazirmatn', Tahoma, sans-serif;
            font-size: var(--font-size);
            line-height: 1.3;
            color: var(--text);
        }

        .subjects-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .subjects-table col.col-subject {
            width: var(--col-subject);
        }

        .subjects-table col.col-mid {
            width: var(--col-mid);
        }

        .subjects-table col.col-final {
            width: var(--col-final);
        }

        .subjects-table col.col-total {
            width: var(--col-total);
        }

        .subjects-table td {
            height: var(--row-height);
            padding: var(--cell-vpad) 2mm;
            vertical-align: middle;
            font-weight: var(--font-weight);
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            border: 0.52mm solid var(--border-color);
        }

        .subjects-table td.first {
            background: var(--first-col-bg);
            color: var(--first-col-text);
            text-align: center;
            padding-right: 2.2mm;
        }

        .subjects-table td.center {
            text-align: center;
        }

        /* استایل جدید برای فیلدهای اطلاعاتی */
        .identity-field {
            position: absolute;
            width: 40mm;
            height: 10mm;
            right: 114mm;
            display: flex;
            align-items: center;
            font-size: 3.8mm;
            font-weight: 700;
            color: #ffffff;
            padding-right: 3mm;
            box-sizing: border-box;
        }

        .identity-field-1 {
            position: absolute;
            width: 40mm;
            height: 10mm;
            right: 178mm;
            display: flex;
            align-items: center;
            font-size: 3.8mm;
            font-weight: 700;
            color: #ffffff;
            padding-right: 3mm;
            box-sizing: border-box;
        }



        .identity-field-2 {
            position: absolute;
            width: 40mm;
            height: 10mm;
            right: 121mm;
            display: flex;
            align-items: center;
            font-size: 3.8mm;
            font-weight: 700;
            color: #ffffff;
            padding-right: 3mm;
            box-sizing: border-box;
        }

        .identity-field-3 {
            position: absolute;
            width: 40mm;
            height: 10mm;
            right: 167mm;
            display: flex;
            align-items: center;
            font-size: 3.8mm;
            font-weight: 700;
            color: #ffffff;
            padding-right: 3mm;
            box-sizing: border-box;
        }

        .identity-field-4 {
            position: absolute;
            width: 40mm;
            height: 10mm;
            right: 127mm;
            display: flex;
            align-items: center;
            font-size: 3.8mm;
            font-weight: 700;
            color: #ffffff;
            padding-right: 3mm;
            box-sizing: border-box;
        }

        .identity-field-5 {
            position: absolute;
            width: 40mm;
            height: 10mm;
            right: 180mm;
            display: flex;
            align-items: center;
            font-size: 3.8mm;
            font-weight: 700;
            color: #ffffff;
            padding-right: 3mm;
            box-sizing: border-box;
        }

        .identity-field-6 {
            position: absolute;
            width: 40mm;
            height: 10mm;
            right: 126mm;
            display: flex;
            align-items: center;
            font-size: 3.8mm;
            font-weight: 700;
            color: #ffffff;
            padding-right: 3mm;
            box-sizing: border-box;
        }

        .identity-field-7 {
            position: absolute;
            width: 40mm;
            height: 10mm;
            right: 178mm;
            display: flex;
            align-items: center;
            font-size: 3.8mm;
            font-weight: 700;
            color: #ffffff;
            padding-right: 3mm;
            box-sizing: border-box;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>
    <div class="sheet">
        <img src="@if (isset($settings['app_logo'])) {{ asset('storage/' . $settings['app_logo']) }} @else {{ asset('schools/cosmos.png') }} @endif"
            alt="Logo" class="logo-left">

        <!-- ===== فیلدهای اطلاعاتی ===== -->
        <!-- نام -->
        <div contenteditable="true" class="identity-field" style="top: 53mm;">
            {{ $user->name }}
        </div>

        <!-- نمبر اساس -->
        <div contenteditable="true" class="identity-field-1" style="top: 53mm;">
            {{ $user->student->admission_no }}
        </div>

        <!-- نام پدر -->
        <div contenteditable="true" class="identity-field-2" style="top: 64mm;">
            {{ $user->father_name }}
        </div>

        <!-- صنف -->
        <div contenteditable="true" class="identity-field-3" style="top: 64mm;">
            {{ \App\Models\SchoolClass::find($_GET['class_id'])->class_name ?? '---' }}
        </div>

        <!-- نام پدر کلان -->
        <div contenteditable="true" class="identity-field-4" style="top: 75mm;">
            {{ $user->student->grand_father_name }}
        </div>


        <div contenteditable="true" class="identity-field-5" style="top: 75mm;">
            {{ \App\Models\SchoolClass::find($_GET['class_id'])?->teacher?->name ?? '---' }}
        </div>

        <!-- نمبر تذکره -->
        <div contenteditable="true" class="identity-field-6" style="top: 87mm;">
            {{ $user->student->tazkira_number }}
        </div>

        <!-- سال تعلیمی -->
        <div contenteditable="true" class="identity-field-7" style="top: 87mm;">
            {{ now()->format('Y') - 621 }}
        </div>
        <!-- ===== پایان فیلدهای اطلاعاتی ===== -->

        <div class="subjects-overlay">
            <table class="subjects-table">
                <colgroup>
                    <col class="col-subject" />
                    <col class="col-mid" />
                    <col class="col-final" />
                    <col class="col-total" />
                </colgroup>
                <tbody>
                    @foreach ($subjects as $subject)
                        <tr>
                            <td class="first">{{ $subject['name'] }}</td>
                            <td class="center">{{ $subject['mid_term'] }}</td>
                            <td class="center">{{ $subject['final'] }}</td>
                            <td class="center">{{ $subject['total'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>

                    <tr>
                        <td class="first">مجموعه نمرات</td>
                        <td class="center">{{ $result['mid_term'] }}</td>
                        <td class="center">{{ $result['final'] }}</td>
                        <td class="center">{{ $result['total'] }}</td>
                    </tr>
                    {{-- <tr>
                        <td class="first">اوسط نمرات</td>
                        <td class="center"></td>
                        <td class="center"></td>
                        <td class="center"></td>
                    </tr> --}}
                    <tr>
                        <td class="first">نتجه</td>
                        <td class="center"> {{ $result['status'] }}</td>
                        <td class="center"> {{ $result['status'] }}</td>
                        <td class="center"> {{ $result['status'] }}</td>
                    </tr>
                    <tr>
                        <td class="first">درجه</td>
                        <td class="center">{{ $result['grade'] }}</td>
                        <td class="center">{{ $result['grade'] }}</td>
                        <td class="center">{{ $result['grade'] }}</td>
                    </tr>

                    <tr class="spacer-row">
                        <td colspan="4"></td>
                    </tr>
                    {{-- <tr>
                        <td class="first">ایام سال تعلیمی</td>
                        <td class="center"></td>
                        <td class="center"></td>
                        <td class="center"></td>
                    </tr> --}}
                    <tr>
                        <td class="first">حاضر</td>
                        <td class="center">{{ $attendance['mid_term']['present'] }}</td>
                        <td class="center">{{ $attendance['final']['present'] }}</td>
                        <td class="center">{{ $attendance['final']['present'] + $attendance['mid_term']['present'] }}
                        </td>
                    </tr>
                    <tr>
                        <td class="first">غیر حاضر</td>
                        <td class="center">{{ $attendance['mid_term']['absent'] }}</td>
                        <td class="center">{{ $attendance['final']['absent'] }}</td>
                        <td class="center">{{ $attendance['final']['absent'] + $attendance['final']['absent'] }}</td>
                    </tr>
                    <tr>
                        <td class="first">مریض</td>
                        <td class="center">{{ $attendance['mid_term']['sick'] ?? '-' }}</td>
                        <td class="center">{{ $attendance['final']['sick'] ?? '-' }}</td>
                        <td class="center">{{ $attendance['final']['sick'] + $attendance['mid_term']['sick'] ?? '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="first">رحصت</td>
                        <td class="center">{{ $attendance['mid_term']['leave'] ?? '-' }}</td>
                        <td class="center">{{ $attendance['final']['leave'] ?? '-' }}</td>
                        <td class="center">{{ $attendance['mid_term']['leave'] + $attendance['final']['leave'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
