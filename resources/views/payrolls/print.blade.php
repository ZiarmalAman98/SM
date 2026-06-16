<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Report - {{ $payrollParent->title }}</title>
    <!-- Add these meta tags to help suppress headers/footers -->
    <meta name="pdfkit-orientation" content="portrait" />
    <meta name="pdfkit-page-size" content="A4" />
    <meta name="pdfkit-margin-top" content="0mm" />
    <meta name="pdfkit-margin-right" content="0mm" />
    <meta name="pdfkit-margin-bottom" content="0mm" />
    <meta name="pdfkit-margin-left" content="0mm" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        :root {
            --primary: {{ env('Color', '#6C5CE7') }};
            --primary-light: #eef2ff;
            --secondary: #64748b;
            --dark: #1e293b;
            --light: #f8fafc;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #fff;
            color: var(--dark);
            font-size: 13px; /* Reduced from 14px */
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .logo {
            width: 120px;
            height: 120px;
        }

        .company-info h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .company-info p {
            color: var(--secondary);
            font-size: 0.875rem;
        }

        .payroll-info {
            background-color: var(--primary-light);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .payroll-info h2 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .info-item {
            margin-bottom: 0.5rem;
        }

        .info-item .label {
            font-weight: 600;
            color: var(--secondary);
            font-size: 0.875rem;
        }

        .info-item .value {
            font-weight: 500;
            color: var(--dark);
        }

        .table-container {
            margin-top: 2rem;
            overflow-x: hidden; /* Changed from auto to hidden */
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
            table-layout: fixed; /* Fixed table layout for better control */
        }

        thead {
            background-color: var(--primary);
            color: white;
        }

        th {
            padding: 0.75rem 0.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.75rem; /* Reduced from 0.875rem */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        td {
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.8rem; /* Reduced from 0.875rem */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .amount {
            text-align: left;
            font-family: 'Courier New', monospace;
            font-weight: 600;
        }

        /* Column width classes - Updated to accommodate father name */
        .col-teacher { width: 10%; }
        .col-father { width: 10%; }
        .col-base { width: 8%; }
        .col-bonus { width: 7%; }
        .col-deductions { width: 8%; }
        .col-advance { width: 7%; }
        .col-tax { width: 7%; }
        .col-net { width: 8%; }

        .summary {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .summary-box {
            width: 300px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1.5rem;
            background-color: var(--light);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .summary-item.total {
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 2px solid #e2e8f0;
            font-weight: 700;
            font-size: 1.125rem;
        }

        .footer {
            margin-top: 3rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: var(--secondary);
            font-size: 0.75rem;
        }

        .signature-section {
            margin-top: 4rem;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-bottom: 0.5rem;
            padding-top: 0.5rem;
        }

        .signature-name {
            font-weight: 600;
        }

        .signature-title {
            font-size: 0.75rem;
            color: var(--secondary);
        }

        .print-button {
            display: block;
            margin: 2rem auto;
            padding: 0.75rem 2rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .print-button:hover {
            background-color: #5b4bd4;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .container {
                padding: 0.5cm;
                max-width: 100%;
            }

            table {
                font-size: 8px; /* Reduced from 9px for print */
                width: 100%;
                table-layout: fixed;
            }

            th, td {
                padding: 4px 3px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .print-button {
                display: none;
            }

            @page {
                margin: 0.5cm;
                size: A4 portrait;
                margin-bottom: 0;
            }

            /* Hide browser headers and footers */
            @page :first {
                margin-top: 0;
            }

            @page :left {
                margin-left: 0;
            }

            @page :right {
                margin-right: 0;
            }

            head, header, footer {
                display: none;
            }

            /* Force hiding of browser-generated content */
            html {
                height: 100%;
                overflow: hidden;
            }

            body::after {
                content: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img
                    src="@if(isset($settings['app_logo']))
                             {{ asset('storage/' . $settings['app_logo']) }}
                         @else
                             {{ asset(env('LOGO', 'logo.png')) }}
                         @endif"
                    alt="{{ $settings['app_name'] ?? env('APP_NAME', 'Company') }} Logo"
                    class="logo"
                    width="180" height="60">
            </div>
            <div class="company-info">
                <h1>{{ $settings['app_name'] ?? env('APP_NAME', 'School Management') }}</h1>
                <p>{{ $settings['address'] ?? env('Address', 'Kabul, afghanistan') }}</p>
                <p>
                    Phone: {{ $settings['support_phone_1'] ?? env('Phone', '0787938293') }} |
                    Email: {{ $settings['support_email'] ?? env('Email', 'info@exmale.com') }}
                </p>
            </div>
        </div>

        <div class="payroll-info">
            <h2>Payroll Summary: {{ $payrollParent->title }}</h2>
            @if($payrollParent->description)
                <div class="description-text" style="margin-bottom: 1rem;">
                    {!! $payrollParent->description !!}
                </div>
            @endif
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Period</div>
                    <div class="value">
                        {{ date('F', mktime(0, 0, 0, $payrollParent->month, 1)) }} {{ $payrollParent->year }}
                    </div>
                </div>
                <div class="info-item">
                    <div class="label">Generated On</div>
                    <div class="value">{{ now()->format('d M Y, h:i A') }}</div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="col-teacher">Staff</th>
                        <th class="col-father">Father Name</th>
                        <th class="col-base">Base Salary</th>
                        <th class="col-bonus">Bonus</th>
                        <th class="col-deductions">Deductions</th>
                        <th class="col-advance">Advance</th>
                        <th class="col-tax">Tax</th>
                        <th class="col-net">Net Salary</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalNetSalary = 0; @endphp
                    @foreach($payrollParent->payrolls as $payroll)
                    @php $totalNetSalary += $payroll->net_salary; @endphp
                    <tr>
                        <td class="col-teacher">{{ $payroll->teacher->name }}</td>
                        <td class="col-father">{{ $payroll->teacher->name ?? 'N/A' }}</td>
                        <td class="col-base amount">{{ number_format($payroll->base_salary) }}</td>
                        <td class="col-bonus amount">{{ $payroll->bonus ? number_format($payroll->bonus) : '-' }}</td>
                        <td class="col-deductions amount">{{ $payroll->deductions ? number_format($payroll->deductions) : '-' }}</td>
                        <td class="col-advance amount">{{ $payroll->advance ? number_format($payroll->advance) : '-' }}</td>
                        <td class="col-tax amount">{{ $payroll->tax ? number_format($payroll->tax) : '-' }}</td>
                        <td class="col-net amount">{{ number_format($payroll->net_salary) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="summary">
            <div class="summary-box">
                <div class="summary-item">
                    <span>Total Payrolls</span>
                    <span>{{ $payrollParent->payrolls->count() }}</span>
                </div>
                <div class="summary-item total">
                    <span>Total Net Salary</span>
                    <span>{{ number_format($totalNetSalary) }}</span>
                </div>
            </div>
        </div>

        <button class="print-button" onclick="window.print()">Print Report</button>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Prepared By</div>
                <div class="signature-title">Finance Officer</div>
            </div>

            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Approved By</div>
                <div class="signature-title">Finance Director</div>
            </div>

            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-name">Authorized By</div>
                <div class="signature-title">CEO</div>
            </div>
        </div>

        <div class="footer">
            <p>This is an official payroll document. Please keep for your records.</p>
            <p>&copy; {{ date('Y') }} {{ env('APP_NAME', 'School Management') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
