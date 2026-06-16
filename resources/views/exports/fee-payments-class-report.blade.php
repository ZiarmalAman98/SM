<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Monthly Fee Report - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
            --border-radius: 8px;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: var(--gray-800);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            transform: rotate(30deg);
        }

        .school-name {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
            position: relative;
            z-index: 2;
        }

        .report-title {
            font-size: 1.5rem;
            font-weight: 400;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .report-info {
            padding: 2rem;
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .info-item {
            background: white;
            padding: 1.25rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border-left: 4px solid var(--primary-color);
        }

        .info-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .table-container {
            padding: 2rem;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        th {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            color: white;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th:first-child {
            border-top-left-radius: var(--border-radius);
        }

        th:last-child {
            border-top-right-radius: var(--border-radius);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            font-size: 0.9375rem;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: var(--gray-50);
            transition: background-color 0.2s ease;
        }

        .text-right {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }

        .status-badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-uninvoiced {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        .status-unpaid {
            background: #fee2e2;
            color: var(--danger-color);
        }

        .status-partial {
            background: #fef3c7;
            color: var(--warning-color);
        }

        .status-paid {
            background: #d1fae5;
            color: var(--success-color);
        }

        tfoot tr {
            background: var(--gray-50);
            font-weight: 600;
        }

        tfoot td {
            border-top: 2px solid var(--gray-300);
            font-size: 1rem;
        }

        .print-section {
            padding: 2rem;
            text-align: center;
            background: var(--gray-50);
            border-top: 1px solid var(--gray-200);
        }

        .print-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary-color);
            color: white;
            padding: 0.875rem 1.75rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .print-button:hover {
            background: #1e40af;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px -1px rgba(0, 0, 0, 0.15);
        }

        .print-button:active {
            transform: translateY(0);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                border-radius: 0;
            }

            .print-section {
                display: none;
            }

            .header {
                background: var(--primary-color) !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            th {
                background: var(--primary-color) !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .status-badge {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
        }

        @media (max-width: 768px) {
            .school-name {
                font-size: 2rem;
            }

            .report-title {
                font-size: 1.25rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            th,
            td {
                padding: 0.75rem;
            }

            .table-container {
                padding: 1rem;
            }
        }

        .currency {
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        .negative-balance {
            color: var(--danger-color);
            font-weight: 600;
        }

        .positive-balance {
            color: var(--success-color);
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 class="school-name">{{ config('app.name') }}</h1>
            <div class="report-title">Class Monthly Fee Report</div>
        </div>

        <div class="report-info">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Class</div>
                    <div class="info-value">{{ $class->class_name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Month</div>
                    <div class="info-value">{{ $month }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fee Type</div>
                    <div class="info-value">
                        {{ $feeType ? $feeType->name : 'All Types' }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Report Date</div>
                    <div class="info-value">{{ $reportDate }}</div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Admission #</th>
                        <th>Student Name</th>
                        <th>Father Name</th>
                        <th class="text-right">Total Fees (AFN)</th>
                        <th class="text-right">Amount Paid (AFN)</th>
                        <th class="text-right">Balance (AFN)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reportData as $data)
                        <tr>
                            <td>{{ $data['admission_no'] }}</td>
                            <td>{{ $data['student_name'] }}</td>
                            <td>{{ $data['father_name'] ?? '-' }}</td>
                            <td class="text-right currency">{{ number_format($data['total_fees'], 2) }}</td>
                            <td class="text-right currency">{{ number_format($data['amount_paid'], 2) }}</td>
                            <td
                                class="text-right currency {{ $data['balance'] > 0 ? 'negative-balance' : 'positive-balance' }}">
                                {{ number_format($data['balance'], 2) }}
                            </td>
                            <td>
                                @php
                                    $statusClass = match ($data['status']) {
                                        'Uninvoiced' => 'status-uninvoiced',
                                        'Unpaid' => 'status-unpaid',
                                        'Partial' => 'status-partial',
                                        'Paid' => 'status-paid',
                                        default => 'status-uninvoiced',
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $data['status'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Totals:</strong></td>
                        <td class="text-right currency"><strong>{{ number_format($totalFees, 2) }}</strong></td>
                        <td class="text-right currency"><strong>{{ number_format($totalPaid, 2) }}</strong></td>
                        <td
                            class="text-right currency {{ $totalBalance > 0 ? 'negative-balance' : 'positive-balance' }}">
                            <strong>{{ number_format($totalBalance, 2) }}</strong>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="print-section">
            <button onclick="window.print()" class="print-button">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z"
                        clip-rule="evenodd" />
                </svg>
                Print Report
            </button>
        </div>
    </div>

    <script>
        // Add subtle animations
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                row.style.transition = 'all 0.5s ease';

                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
</body>

</html>
