<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Assignment Submissions Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-right: 20px;
        }

        .report-title h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .report-title p {
            font-size: 16px;
            opacity: 0.9;
        }

        .report-meta {
            text-align: right;
        }

        .report-meta p {
            margin-bottom: 5px;
        }

        /* Summary Section */
        .summary-section {
            padding: 30px;
            background: #f5f9ff;
            border-bottom: 1px solid #e0e0e0;
        }

        .summary-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            display: inline-block;
        }

        .summary-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .summary-card {
            flex: 1;
            min-width: 200px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #3498db;
        }

        .summary-card h3 {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .summary-card p {
            font-size: 24px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Data Section */
        .data-section {
            padding: 30px;
        }

        .data-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
            display: inline-block;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .data-table th {
            background: #f2f6fc;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #ddd;
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .data-table tr:hover {
            background: #f9f9f9;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .amount {
            font-weight: 600;
        }

        /* Chart Section */
        .chart-section {
            padding: 0 30px 30px;
            display: flex;
            gap: 30px;
        }

        .chart-container {
            flex: 1;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            min-height: 300px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #2c3e50;
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

            .page-break {
                page-break-after: always;
            }
        }

        /* Action Buttons */
        .action-buttons {
            padding: 20px 30px;
            text-align: right;
        }

        .print-button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }

        .print-button:hover {
            background: #2980b9;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .report-header {
                flex-direction: column;
                text-align: center;
            }

            .logo-section {
                margin-bottom: 20px;
                justify-content: center;
            }

            .report-meta {
                text-align: center;
                margin-top: 20px;
            }

            .summary-cards {
                flex-direction: column;
            }

            .chart-section {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div class="logo-section">
                <img src="@if(isset($settings['app_logo'])) {{ asset('storage/' . $settings['app_logo']) }} @else {{ asset('schools/cosmos.png') }} @endif"
                    class="logo" alt="Logo">
                <div class="report-title">
                    <h1>Assignment Submissions Report</h1>
                    <p>Submission Tracking Overview</p>
                </div>
            </div>
            <div class="report-meta">
                <p><strong>Generated:</strong> {{ now()->format('F d, Y') }}</p>
                <p><strong>Period:</strong> {{ request('from', now()->startOfMonth()->format('M d, Y')) }} -
                    {{ request('to', now()->format('M d, Y')) }}
                </p>
                <p><strong>Report ID:</strong> #{{ rand(10000, 99999) }}</p>
            </div>
        </div>

        <!-- Data Section -->
        <div class="data-section">
            <h2 class="data-title">Submitted Assignments</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Assignment</th>
                        <th>Subject</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $index => $submission)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $submission->student->name ?? 'N/A' }}</td>
                            <td>{{ $submission->assignment->title ?? 'Untitled' }}</td>
                            <td>{{ $submission->assignment->subject->name ?? 'N/A' }}</td>
                            <td style="min-width: 50px">{{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y H:i') : 'N/A' }}
                            </td>
                            <td>
                                <span class="status-badge status-{{ strtolower($submission->status) }}">
                                    {{ ucfirst($submission->status ?? 'Pending') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center;">No assignment submissions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="report-footer">
            <p>&copy; {{ date('Y') }} {{ $settings['app_name'] ?? config('app.name') }}. All rights reserved.</p>
            <p>This report is generated automatically and is valid without a signature.</p>
        </div>
    </div>

    <script>
        window.onload = function () {
            window.print();
            window.onafterprint = function () {
                window.close();
            };
            setTimeout(() => {
                window.close();
            }, 500);
        };
    </script>
</body>

</html>