<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Watan Oxford High School FeesBill</title>
    <style>
        /* Reset CSS */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 20px;
        }

        /* Fee Bill Styling */
        .fee-bill {
            border: 1px solid #333;
            margin-bottom: 8mm;
            padding: 3mm;
        }

        .fee-bill:last-child {
            margin-bottom: 0;
        }

        /* Header Styling */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }

        .logo {
            width: 15mm;
            height: 15mm;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .emblem {
            font-size: 6pt;
            text-align: center;
        }

        .title {
            text-align: center;
        }

        .title p:first-child {
            font-size: 10pt;
            margin-bottom: 1mm;
        }

        .title p:last-child {
            font-size: 10pt;
            font-weight: bold;
        }

        .stamp {
            width: 15mm;
            height: 15mm;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Student Info Styling */
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            border-bottom: 1px solid #333;
            margin-bottom: 2mm;
            font-size: 9pt;
        }

        .info-item {
            padding: 1mm;
        }

        .info-item:nth-child(1),
        .info-item:nth-child(2) {
            border-right: 1px solid #333;
        }

        .info-label {
            display: inline-block;
        }

        .info-value {
            float: right;
        }

        /* Fees Table Styling */
        .fees-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2mm;
            font-size: 9pt;
        }

        .fees-table th,
        .fees-table td {
            border-bottom: 1px solid #333;
            padding: 1mm 2mm;
        }

        .fees-table th:not(:last-child),
        .fees-table td:not(:last-child) {
            border-left: 1px solid #333;
        }

        .fees-table th:nth-child(1),
        .fees-table td:nth-child(1) {
            text-align: center;
            width: 15%;
        }

        .fees-table th:nth-child(2),
        .fees-table td:nth-child(2) {
            text-align: center;
            width: 15%;
        }

        .fees-table th:nth-child(3),
        .fees-table td:nth-child(3) {
            text-align: center;
            width: 15%;
        }

        .fees-table th:nth-child(4),
        .fees-table td:nth-child(4) {
            text-align: right;
            width: 55%;
        }

        .fees-table tr:last-child {
            font-weight: bold;
        }

        /* Footer Styling */
        .footer {
            font-size: 7pt;
            text-align: right;
            padding: 1mm;
        }

        /* Controls Area */
        .controls {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .print-btn {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
            }

            .a4-paper {
                width: 210mm;
                height: 297mm;
                padding: 10mm;
                box-shadow: none;
                margin: 0;
            }

            .controls {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="controls">
        <button class="print-btn" onclick="window.print()">Print</button>
    </div>

    <div class="a4-paper">
        @foreach ($feePayments as $payment)
            <div class="fee-bill">
                <div class="header">
                    <div class="logo">
                        <img src="{{ asset('afghanistan_school.png') }}" alt="{{ env('SCHOOL_LOGO') }}"
                            style="width: 90%; height: 90%;" />
                    </div>
                    <div class="title">
                        <p> شفاء خصوصي عالي لېسې د فیس بل</p>
                        <p>Shifa Private High School FeesBill</p>
                    </div>
                    <div class="stamp">
                        <img src="/{{ env('SCHOOL_LOGO') }}" alt="{{ env('SCHOOL_LOGO') }}"
                            style="width: 90%; height: 90%;" />
                    </div>
                </div>

                <div class="student-info">
                    <div class="info-item">
                        <span class="info-label">Name/اسم</span>
                        <span class="info-value">{{ $payment->student->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">FatherName/اسم پدر</span>
                        <span class="info-value">{{ $payment->student->father_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Class/صنف</span>
                        <span class="info-value">{{ $payment->class->class_name }}</span>
                    </div>
                </div>

                <table class="fees-table">
                    <thead>
                        <tr>
                            <th>ملاحظه شد</th>
                            <th>مبلغ قابل تادیه</th>
                            <th>ماه</th>
                            <th>موضوع فیس</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>{{ $payment->amount_paid }}</td>
                            <td>{{ $payment->month }}</td>
                            <td>{{ $payment->feeType->name }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="footer">
                    <p>
                        یادداشت: وقتادیدی گری در تحویل فیس فرزندان تان کوشش نماید در صورت
                        معین فیس همراه با نرخ نموده همکاری با اداره لیسه در تربیه معلمات
                    </p>
                    <p>استفاده نیمایید. شماره تماس 0780691000/0749514044</p>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // Print function
        function printPage() {
            window.print();
        }

        // Add event listener to print button
        document.querySelector(".print-btn").addEventListener("click", printPage);
    </script>
</body>




</html>
