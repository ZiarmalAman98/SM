<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $class->class_name }} - Timetable</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f9f9f9;
            padding: 20px;
        }

        /* Report Container */
        .report-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        /* Header Section */
        .report-header {
            padding: 30px;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .report-title h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .report-meta {
            text-align: right;
        }

        .report-meta p {
            margin-bottom: 5px;
        }

        /* Timetable Table */
        .timetable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .timetable th,
        .timetable td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .timetable th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
            font-size: 16px;
        }

        .timetable td {
            background-color: #f9f9f9;
            font-size: 14px;
        }

        .timetable tr:nth-child(even) {
            background-color: #f4f6f9;
        }

        .timetable tr:hover {
            background-color: #f1f1f1;
        }

        .time-slot {
            font-weight: bold;
            color: #2c3e50;
        }

        .teacher-col {
            font-size: 13px;
            color: #2c3e50;
            white-space: nowrap;
        }

        /* Footer Section */
        .report-footer {
            padding: 20px 30px;
            background: #2c3e50;
            color: white;
            text-align: center;
            font-size: 14px;
        }

        /* Print Styles */
        @media print {
            @page {
                size: A4 portrait;
                margin: 20mm;
            }

            body {
                background: white;
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .report-container {
                box-shadow: none;
                max-width: 100%;
            }

            .print-button {
                display: none;
            }

            .chart-section {
                break-inside: avoid;
            }

            .data-section {
                break-inside: avoid-page;
            }

            .timetable th,
            .timetable td {
                font-size: 10px;
            }

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>

<body>
    <div class="report-container">
        <!-- Header Section -->
        <div class="report-header">
            <div class="report-title">
                <h1>{{ $class->class_name }} - Timetable</h1>
                <p>Schedule Overview for the Week</p>
                <p style="margin-top: 8px;">
                    <strong>Class Teacher:</strong>
                    {{ $class->teacher?->name ?? '—' }}
                </p>
            </div>
            <div class="report-meta">
                @php
                    use Morilog\Jalali\Jalalian;

                    $jalaliDate = Jalalian::now()->format('%d %B %Y');
                    $dariDate = dariMonthName($jalaliDate);
                @endphp

                <p><strong>Generated:</strong> {{ $dariDate }}</p>
                <p><strong>Academic Year:</strong> {{ Jalalian::now()->getYear() }}</p>
            </div>
        </div>

        <!-- Timetable Table -->
        <table class="timetable">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Teacher</th>
                    @foreach (['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                        <th>{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($subjects as $subject)
                    @foreach ($subject->schedules as $schedule)
                        <tr>
                            <td class="time-slot">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                            <td class="teacher-col">{{ $subject->teacher?->name ?? '—' }}</td>
                            @foreach (['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as $day)
                                <td>
                                    @if ($schedule->day_of_week === $day)
                                        <strong>{{ $subject->name }}</strong>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Footer Section -->
    <div class="report-footer">
        <p>&copy; {{ date('Y') }} {{ $settings['app_name'] ?? config('app.name') }} . All rights reserved.</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</body>

</html>
