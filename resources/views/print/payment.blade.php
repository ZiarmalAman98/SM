<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .receipt-container {
            background: white;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .receipt-header {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
        }

        .receipt-table th {
            background-color: #f1f5f9;
            color: #64748b;
            font-weight: 600;
            text-align: left;
        }

        .receipt-table td,
        .receipt-table th {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .total-amount {
            color: #10b981;
            font-weight: 700;
        }

        .logo img {
            max-height: 70px;
            width: auto;
        }

        .school-name h2 {
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .school-name p {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        @media print {
            body {
                background-color: white;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
        <!-- Receipt Container -->
        <div class="receipt-container">
            <!-- Header -->
            <div class="receipt-header p-6 rounded-t-lg">
                <div class="flex justify-between items-start">
                    <div class="flex items-center space-x-4">
                        <!-- Logo -->
                        <div class="logo">
                            <img src="@if (isset($settings['app_logo'])) {{ asset('storage/' . $settings['app_logo']) }} @else {{ asset('schools/cosmos.png') }} @endif"
                                alt="Logo">
                        </div>
                        <!-- School Name -->
                        <div class="school-name">
                            <h2>{{ $settings['app_name'] ?? env('APP_NAME', 'School Management') }}</h2>
                            @if(!empty($settings['school_address_line']))
                                <p>{{ $settings['school_address_line'] }}</p>
                            @endif
                            @php
                                $paymentContact = implode(' | ', array_filter([
                                    $settings['support_phone_display'] ?? '',
                                    $settings['support_email'] ?? '',
                                ]));
                            @endphp
                            @if($paymentContact !== '')
                                <p>{{ $paymentContact }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm opacity-90">Issued on</div>
                        <div class="font-medium">{{ \Morilog\Jalali\Jalalian::now()->format('Y/m/d') }}</div>
                    </div>
                </div>

                <div class="mt-6">
                    <h1 class="text-2xl font-bold">Payment Receipt</h1>
                    <p class="opacity-90 mt-1">#<span id="receipt-number">{{ $payment->id }}</span></p>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6">
                <!-- Payment Details -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase mb-3">Payment Details</h3>
                    <table class="receipt-table">
                        <tbody>
                            <tr>
                                <th class="w-1/3">Payroll Parent</th>
                                <td>{{ $payment->payroll->payrollParent->title ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Payroll Number</th>
                                <td>{{ $payment->payroll->payroll_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Teacher Name</th>
                                <td>{{ $payment->payroll->teacher->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Payment Date</th>
                                <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <th class="text-lg">Total Paid</th>
                                <td class="text-lg total-amount">AFN {{ number_format($payment->amount, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Notes -->
                @if ($payment->details)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-slate-500 uppercase mb-2">Notes</h3>
                        <div class="bg-slate-50 p-4 rounded-lg">
                            {!! $payment->details !!}
                        </div>
                    </div>
                @endif

                <!-- Signature -->
                <div class="mt-8 pt-6 border-t border-dashed border-gray-300">
                    <div class="flex justify-between">
                        <div>
                            <p class="mb-8">_________________________</p>
                            <p class="text-sm text-slate-500">Recipient Signature</p>
                        </div>
                        <div>
                            <p class="mb-8">_________________________</p>
                            <p class="text-sm text-slate-500">Authorized By</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="no-print flex justify-center gap-4 mt-6">
            <button onclick="window.print()"
                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow">
                Print Receipt
            </button>
            <button onclick="window.close()"
                class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg shadow">
                Close
            </button>
        </div>
    </div>
</body>

</html>
