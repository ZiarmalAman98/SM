<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shifa Private High School Fee Bills</title>
    <style>
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

        .a4-paper {
            width: 210mm;
            padding: 10mm;
            background-color: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .fee-bill {
            border: 1px solid #333;
            margin-bottom: 8mm;
            padding: 3mm;
            page-break-inside: avoid;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 2mm;
            margin-bottom: 2mm;
        }

        .logo,
        .stamp {
            width: 15mm;
            height: 15mm;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .title {
            text-align: center;
        }

        .title p:first-child {
            font-size: 14pt;
            margin-bottom: 1mm;
        }

        .title p:last-child {
            font-size: 14pt;
            font-weight: bold;
        }

        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            border-bottom: 1px solid #333;
            margin-bottom: 2mm;
            font-size: 9pt;
        }

        .info-item {
            padding: 1mm;
            border-right: 1px solid #333;
        }

        .info-item:last-child {
            border-right: none;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            font-size: 8pt;
            margin: 2mm 0;
            padding: 0 1mm;
        }

        .fees-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2mm;
            font-size: 9pt;
        }

        .fees-table th,
        .fees-table td {
            border: 1px solid #333;
            padding: 1mm 2mm;
            text-align: center;
        }

        .fees-table thead {
            background-color: #f9f9f9;
        }

        .footer {
            font-size: 12pt;
            text-align: right;
            padding: 1mm;
            border-top: 1px solid #333;
            margin-top: 3mm;
        }

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
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
            }

            .a4-paper {
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
        @forelse($payments as $payment)
            <div class="fee-bill">
                <div class="header">
                    <div class="stamp">
                        <img src="{{ asset('afghanistan_school.png') }}" alt="Stamp"
                            style="width: 90%; height: 90%;" />
                    </div>

                    @php
                        $schoolNameFa = env('SCHOOL_NAME_FA', 'لیسه خصوصی وطن آکسفورد');
                        $schoolNameEn = env('SCHOOL_NAME_EN', 'Watan Oxford High School');
                    @endphp

                    <div class="title">
                        <p>{{ $schoolNameFa }} فیس بل</p>
                        <p>{{ $schoolNameEn }} Fees Bill</p>
                    </div>
                    <div class="logo">


                        <img src="@if (isset($settings['app_logo'])) {{ asset('storage/' . $settings['app_logo']) }}
                 @else
                    {{ asset('afghanistan_school.png') }} @endif"
                            alt="School Logo" style="width: 90%; height: 90%;">
                    </div>

                </div>

                <div class="student-info">
                    <div class="info-item"><strong>Name:</strong> {{ $payment->student->name ?? '-' }}</div>
                    <div class="info-item"><strong>Father Name:</strong> {{ $payment->student->father_name ?? '-' }}
                    </div>
                    <div class="info-item"><strong>Class:</strong> {{ $payment->class->class_name ?? '-' }}</div>
                </div>

                <div class="receipt-info">
                    <div><strong>Receipt #:</strong> {{ $payment->receipt_number ?? '-' }}</div>
                    <div><strong>Total Fees:</strong> {{ number_format($payment->total_fees ?? 0, 2) }}</div>
                    <div><strong>Payment Date:</strong>
                        {{ optional($payment->payment_date)->format('Y-m-d') ?? '-' }}
                    </div>
                </div>

                <table class="fees-table">
                    <thead>
                        <tr>
                            <th>ملاحظه شد</th>
                            <th>Amount Paid</th>
                            <th>Month</th>
                            <th>Fee Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>{{ number_format($payment->amount_paid ?? 0, 2) }}</td>
                            <td>{{ $payment->month ?? '-' }}</td>
                            <td>{{ $payment->feeType->name ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="footer">
                    <p>
                        یادداشت: لطفاً در پرداخت به موقع فیس فرزندتان کوشا باشید. پرداخت به موقع فیس باعث همکاری
                        بهتر با
                        اداره لیسه و ارتقاء کیفیت تدریس می‌گردد.
                    </p>
                    {{-- <p>شماره تماس: 0780691000 / 0749514044</p> --}}
                </div>
            </div>
        @empty
            <p>No fee payments found for the given filters.</p>
        @endforelse
    </div>

    <script>
        document.querySelector(".print-btn").addEventListener("click", () => window.print());
    </script>
</body>

</html>
