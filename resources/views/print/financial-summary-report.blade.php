<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Financial Summary Report - {{ $branch->branch_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2c3e50;
            line-height: 1.6;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            min-height: 100vh;
            direction: ltr;
            text-align: left;
        }

        .container {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 15mm;
            background: #fff;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 25px 30px;
            border-bottom: 4px solid #1e40af;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
            border-radius: 8px 8px 0 0;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            pointer-events: none;
        }

        .logo-left {
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 8px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            z-index: 2;
            position: relative;
        }

        .logo-right {
            width: 90px;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 8px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            z-index: 2;
            position: relative;
        }

        .logo-left img,
        .logo-right img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .school-info {
            flex: 1;
            text-align: center;
            padding: 0 30px;
            z-index: 2;
            position: relative;
        }

        .school-name {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .school-address {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .document-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e40af;
            margin: 25px 0 30px 0;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            position: relative;
            padding: 15px 0;
        }

        .document-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, #1e40af, #3b82f6);
            border-radius: 2px;
        }

        .report-info {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .report-info::before {
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
            direction: ltr;
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

        .financial-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #3b82f6, #1e40af);
        }

        .summary-card.revenue::before {
            background: linear-gradient(180deg, #10b981, #059669);
        }

        .summary-card.expense::before {
            background: linear-gradient(180deg, #ef4444, #dc2626);
        }

        .summary-card.profit::before {
            background: linear-gradient(180deg, #f59e0b, #d97706);
        }

        .card-title {
            font-size: 14px;
            font-weight: 700;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .card-amount {
            font-size: 24px;
            font-weight: 800;
            color: #374151;
            margin-bottom: 5px;
        }

        .card-subtitle {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table th,
        .table td {
            border: none;
            padding: 15px 12px;
            text-align: left;
            font-size: 14px;
        }

        .table th {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 12px;
            text-align: center;
        }

        .table tr:nth-child(even) {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .table tr:nth-child(odd) {
            background: #ffffff;
        }

        .table tr:hover {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            transform: scale(1.01);
            transition: all 0.2s ease;
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

            /* Enhanced Readability Styles */
            .breakdown-section {
                margin: 30px 0;
                padding: 25px;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                border-radius: 12px;
                border: 2px solid #e2e8f0;
            }

            .section-title {
                font-size: 20px;
                font-weight: 700;
                color: #1e40af;
                margin: 0 0 20px 0;
                text-align: center;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            .breakdown-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
            }

            .breakdown-item {
                background: #ffffff;
                padding: 20px;
                border-radius: 10px;
                border: 2px solid #e2e8f0;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
            }

            .breakdown-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
                border-color: #3b82f6;
            }

            .breakdown-label {
                font-size: 16px;
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .breakdown-amount {
                font-size: 24px;
                font-weight: 700;
                color: #1e40af;
                margin-bottom: 5px;
            }

            .breakdown-percentage {
                font-size: 14px;
                font-weight: 500;
                color: #6b7280;
                margin-bottom: 8px;
            }

            .breakdown-details {
                font-size: 12px;
                color: #9ca3af;
                font-style: italic;
            }

            .monthly-section {
                margin: 30px 0;
                padding: 25px;
                background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
                border-radius: 12px;
                border: 2px solid #bae6fd;
            }

            .monthly-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
            }

            .monthly-item {
                background: #ffffff;
                padding: 15px;
                border-radius: 8px;
                border: 1px solid #bae6fd;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            .month-name {
                font-size: 14px;
                font-weight: 600;
                color: #1e40af;
                margin-bottom: 10px;
                text-align: center;
                padding: 5px;
                background: #dbeafe;
                border-radius: 5px;
            }

            .monthly-details {
                space-y: 5px;
            }

            .monthly-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 5px;
                padding: 3px 0;
            }

            .monthly-label {
                font-size: 12px;
                font-weight: 500;
                color: #6b7280;
                min-width: 50px;
            }

            .monthly-value {
                font-size: 12px;
                font-weight: 600;
                color: #374151;
            }

            .net-row {
                border-top: 1px solid #e5e7eb;
                padding-top: 5px;
                margin-top: 5px;
            }

            .net-value {
                font-weight: 700;
                font-size: 13px;
            }

        .footer {
            margin-top: 40px;
            padding: 25px 0;
            border-top: 3px solid #e2e8f0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 0 0 8px 8px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            direction: ltr;
        }

        .footer-left,
        .footer-right {
            flex: 1;
        }

        .footer-left p,
        .footer-right p {
            margin: 5px 0;
            font-size: 13px;
            color: #374151;
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

        @media print {
            html,
            body,
            .table th,
            .table td {
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
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <!-- Left Logo -->
            <div class="logo-left">
                <img src="{{ asset('afghanistan_school.png') }}" alt="Ministry of Education">
            </div>

            <!-- School Information -->
            <div class="school-info">
                <h1 class="school-name">{{ $schoolName }}</h1>
                <p class="school-address">{{ $schoolAddress }}</p>
            </div>

            <!-- Right Logo -->
            <div class="logo-right">
                <img src="{{ $appLogoUrl ?? asset('schools/cosmos.png') }}" alt="School Logo">
            </div>
        </div>

        <!-- Document Title -->
        <h2 class="document-title">Financial Summary Report</h2>

        <!-- Report Information -->
        <div class="report-info">
            <div class="info-row">
                <span class="info-label">Branch:</span>
                <span class="info-value">{{ $branch->branch_name }}</span>
            </div>
            @if($class)
            <div class="info-row">
                <span class="info-label">Class:</span>
                <span class="info-value">{{ $class->class_name }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Report Type:</span>
                <span class="info-value">{{ ucfirst($reportType) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Period:</span>
                <span class="info-value">{{ $startDateJalali->format('F d, Y') }} - {{ $endDateJalali->format('F d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Generated On:</span>
                <span class="info-value">{{ $generatedDateJalali->format('F d, Y \a\t H:i A') }}</span>
            </div>
        </div>

        <!-- Financial Summary Cards -->
        <div class="financial-summary">
            <div class="summary-card revenue">
                <div class="card-title">Total Revenue</div>
                <div class="card-amount">{{ number_format($financialData['totalRevenue']) }} Afg</div>
                <div class="card-subtitle">Invoice Payments + Other Income</div>
            </div>

            <div class="summary-card expense">
                <div class="card-title">Total Expenses</div>
                <div class="card-amount">{{ number_format($financialData['totalExpenses']) }} Afg</div>
                <div class="card-subtitle">Payroll + Other Costs</div>
            </div>

            <div class="summary-card profit">
                <div class="card-title">Net {{ $financialData['netProfit'] >= 0 ? 'Profit' : 'Loss' }}</div>
                <div class="card-amount" style="color: {{ $financialData['netProfit'] >= 0 ? '#10b981' : '#ef4444' }};">
                    {{ number_format(abs($financialData['netProfit'])) }} Afg
                </div>
                <div class="card-subtitle">{{ $financialData['netProfit'] >= 0 ? 'Profit' : 'Loss' }} Margin</div>
            </div>
        </div>

        <!-- Detailed Breakdown -->
        <div class="breakdown-section">
            <h3 class="section-title">Financial Breakdown</h3>
            <div class="breakdown-grid">
                <div class="breakdown-item">
                    <div class="breakdown-label">Invoice Payment Collection</div>
                    <div class="breakdown-amount">{{ number_format($financialData['totalFeesCollected']) }} Afg</div>
                    <div class="breakdown-percentage">{{ $financialData['totalRevenue'] > 0 ? number_format(($financialData['totalFeesCollected'] / $financialData['totalRevenue']) * 100, 1) : 0 }}%</div>
                    <div class="breakdown-details">{{ $financialData['feePayments']->count() }} payments</div>
                </div>

                <div class="breakdown-item">
                    <div class="breakdown-label">Other Income</div>
                    <div class="breakdown-amount">{{ number_format($financialData['totalIncome']) }} Afg</div>
                    <div class="breakdown-percentage">{{ $financialData['totalRevenue'] > 0 ? number_format(($financialData['totalIncome'] / $financialData['totalRevenue']) * 100, 1) : 0 }}%</div>
                    <div class="breakdown-details">{{ $financialData['incomes']->count() }} entries</div>
                </div>

                <div class="breakdown-item">
                    <div class="breakdown-label">Payroll Expenses</div>
                    <div class="breakdown-amount">{{ number_format($financialData['totalPayrollExpenses']) }} Afg</div>
                    <div class="breakdown-percentage">{{ $financialData['totalExpenses'] > 0 ? number_format(($financialData['totalPayrollExpenses'] / $financialData['totalExpenses']) * 100, 1) : 0 }}%</div>
                    <div class="breakdown-details">{{ $financialData['payrolls']->count() }} employees</div>
                </div>

                <div class="breakdown-item">
                    <div class="breakdown-label">Outstanding Invoice Balance</div>
                    <div class="breakdown-amount">{{ number_format($financialData['outstandingFees']) }} Afg</div>
                    <div class="breakdown-percentage">-</div>
                    <div class="breakdown-details">Pending collection</div>
                </div>
            </div>
        </div>

        <!-- Monthly Breakdown -->
        @if(count($financialData['monthlyData']) > 1)
        <div class="monthly-section">
            <h3 class="section-title">Monthly Performance</h3>
            <div class="monthly-grid">
                @foreach($financialData['monthlyData'] as $month)
                <div class="monthly-item">
                    <div class="month-name">{{ $month['month'] }}</div>
                    <div class="monthly-details">
                        <div class="monthly-row">
                            <span class="monthly-label">Invoice Payments:</span>
                            <span class="monthly-value">{{ number_format($month['fees']) }} Afg</span>
                        </div>
                        <div class="monthly-row">
                            <span class="monthly-label">Income:</span>
                            <span class="monthly-value">{{ number_format($month['income']) }} Afg</span>
                        </div>
                        <div class="monthly-row">
                            <span class="monthly-label">Payroll:</span>
                            <span class="monthly-value">{{ number_format($month['payroll']) }} Afg</span>
                        </div>
                        <div class="monthly-row net-row">
                            <span class="monthly-label">Net:</span>
                            <span class="monthly-value net-value" style="color: {{ $month['net'] >= 0 ? '#10b981' : '#ef4444' }};">
                                {{ number_format($month['net']) }} Afg
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Invoice Payments Details -->
        <div class="breakdown-section">
            <h3 class="section-title">Invoice Payment Details</h3>
            @if($financialData['feePayments']->isEmpty())
                <p style="text-align: center; color: #6b7280;">No invoice payments found for this period.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Receipt</th>
                            <th>Invoice</th>
                            <th>Family</th>
                            <th>Parent</th>
                            <th>Payment Date</th>
                            <th>Method</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($financialData['feePayments'] as $payment)
                            @php
                                $invoice = $payment->invoice;
                                $parent = $invoice?->parentGuardian?->user;
                                $parentName = trim(($parent?->name ?? '') . ' ' . ($parent?->last_name ?? ''));
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $payment->receipt_number }}</td>
                                <td>{{ $invoice?->invoice_number ?? '-' }}</td>
                                <td>{{ $invoice?->family_code ?? '-' }}</td>
                                <td>{{ $parentName ?: '-' }}</td>
                                <td>{{ $payment->payment_date ? \Morilog\Jalali\Jalalian::fromDateTime($payment->payment_date)->format('Y/m/d') : '-' }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', (string) $payment->payment_method)) }}</td>
                                <td>{{ number_format((float) $payment->amount, 2) }} Afg</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Invoice Details -->
        <div class="breakdown-section">
            <h3 class="section-title">Invoice Details</h3>
            @if($financialData['invoices']->isEmpty())
                <p style="text-align: center; color: #6b7280;">No invoices found for this period.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice</th>
                            <th>Family</th>
                            <th>Parent</th>
                            <th>Billing Period</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($financialData['invoices'] as $invoice)
                            @php
                                $parent = $invoice->parentGuardian?->user;
                                $parentName = trim(($parent?->name ?? '') . ' ' . ($parent?->last_name ?? ''));
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->family_code }}</td>
                                <td>{{ $parentName ?: '-' }}</td>
                                <td>{{ $invoice->billing_month }} {{ $invoice->billing_year }}</td>
                                <td>{{ number_format((float) $invoice->total_amount, 2) }} Afg</td>
                                <td>{{ number_format((float) $invoice->paid_amount, 2) }} Afg</td>
                                <td>{{ number_format((float) $invoice->balance, 2) }} Afg</td>
                                <td>{{ ucfirst($invoice->status) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Other Income Details -->
        <div class="breakdown-section">
            <h3 class="section-title">Other Income Details</h3>
            @if($financialData['incomes']->isEmpty())
                <p style="text-align: center; color: #6b7280;">No other income found for this period.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Source</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($financialData['incomes'] as $income)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $income->date ? \Morilog\Jalali\Jalalian::fromDateTime($income->date)->format('Y/m/d') : '-' }}</td>
                                <td>{{ $income->source?->name ?? '-' }}</td>
                                <td>{{ $income->description ?? '-' }}</td>
                                <td>{{ ucfirst((string) $income->status) }}</td>
                                <td>{{ number_format((float) $income->amount, 2) }} Afg</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Payroll Details -->
        <div class="breakdown-section">
            <h3 class="section-title">Payroll Details</h3>
            @if($financialData['payrolls']->isEmpty())
                <p style="text-align: center; color: #6b7280;">No payroll records found for this period.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Net Salary</th>
                            <th>Bonus</th>
                            <th>Deductions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($financialData['payrolls'] as $payroll)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $payroll->created_at ? \Morilog\Jalali\Jalalian::fromDateTime($payroll->created_at)->format('Y/m/d') : '-' }}</td>
                                <td>{{ $payroll->employee?->name ?? $payroll->user?->name ?? '-' }}</td>
                                <td>{{ number_format((float) $payroll->net_salary, 2) }} Afg</td>
                                <td>{{ number_format((float) $payroll->bonus, 2) }} Afg</td>
                                <td>{{ number_format((float) $payroll->deductions, 2) }} Afg</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.</p>
                @if(!empty($settings['support_email']) || !empty($settings['support_phone_1']) || !empty($settings['support_phone_2']))
                    <p>
                        {{ $settings['support_email'] ?? '' }}
                        @if(!empty($settings['support_email']) && (!empty($settings['support_phone_1']) || !empty($settings['support_phone_2']))) | @endif
                        {{ $settings['support_phone_1'] ?? '' }}
                        @if(!empty($settings['support_phone_1']) && !empty($settings['support_phone_2'])) | @endif
                        {{ $settings['support_phone_2'] ?? '' }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <button class="print-button" onclick="window.print()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" style="margin-right: 8px;">
            <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
        </svg>
        Print Report
    </button>
</body>

</html>
