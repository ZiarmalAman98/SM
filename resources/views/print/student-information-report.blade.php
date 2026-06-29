<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Student Information Report - {{ $class->class_name }}</title>
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

        .logo-image {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-left: 15px;
        }

        .ministry-info {
            text-align: center;
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

        .student-info {
            margin-bottom: 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .student-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #3b82f6, #1e40af);
        }

        .student-name {
            font-weight: 800;
            font-size: 18px;
            color: #1e40af;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .student-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            font-size: 13px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.3);
            direction: ltr;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 700;
            min-width: 140px;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #2c3e50;
        }

        .detail-value {
            color: #34495e;
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
        <h2 class="document-title">Student Information Report</h2>

        <!-- Report Information -->
        <div class="report-info">
            <div class="info-row">
                <span class="info-label">Branch:</span>
                <span class="info-value">{{ $branch->branch_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Class:</span>
                <span class="info-value">{{ $class->class_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">{{ $status === 'all' ? 'All' : ucfirst($status) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Gender:</span>
                <span class="info-value">{{ $gender ? ucfirst($gender) : 'All' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Report Type:</span>
                <span class="info-value">{{ ucfirst($reportType) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Students:</span>
                <span class="info-value">{{ $students->count() }} students</span>
            </div>
            <div class="info-row">
                <span class="info-label">Generated On:</span>
                <span class="info-value">{{ $generatedDateJalali->format('F d, Y \a\t H:i') }}</span>
            </div>
        </div>

        <!-- Students Information -->
        @if($reportType === 'detailed')
            @foreach($students as $student)
                <div class="student-info">
                    <div class="student-name">{{ $student->name }}</div>
                    <div class="student-details">
                        @if($student->father_name)
                        <div class="detail-item">
                            <span class="detail-label">Father Name:</span>
                            <span class="detail-value">{{ $student->father_name }}</span>
                        </div>
                        @endif

                        @if($student->student?->grand_father_name)
                        <div class="detail-item">
                            <span class="detail-label">Grandfather Name:</span>
                            <span class="detail-value">{{ $student->student->grand_father_name }}</span>
                        </div>
                        @endif

                        @if($student->student?->dob)
                        <div class="detail-item">
                            <span class="detail-label">Age:</span>
                            <span class="detail-value">{{ \Carbon\Carbon::parse($student->student->dob)->age }} years</span>
                        </div>
                        @endif

                        @if($student->student?->gender)
                        <div class="detail-item">
                            <span class="detail-label">Gender:</span>
                            <span class="detail-value">{{ $student->student->gender }}</span>
                        </div>
                        @endif

                        @if($student->student?->address)
                        <div class="detail-item">
                            <span class="detail-label">Address:</span>
                            <span class="detail-value">{{ $student->student->address }}</span>
                        </div>
                        @endif

                        @if($student->student?->phone)
                        <div class="detail-item">
                            <span class="detail-label">Phone:</span>
                            <span class="detail-value">{{ $student->student->phone }}</span>
                        </div>
                        @endif

                        @if($student->student?->tazkira_number)
                        <div class="detail-item">
                            <span class="detail-label">Tazkira Number:</span>
                            <span class="detail-value">{{ $student->student->tazkira_number }}</span>
                        </div>
                        @endif

                        @if($student->student?->ton_number)
                        <div class="detail-item">
                            <span class="detail-label">TON Number:</span>
                            <span class="detail-value">{{ $student->student->ton_number }}</span>
                        </div>
                        @endif

                        @if($student->student?->blood_group)
                        <div class="detail-item">
                            <span class="detail-label">Blood Group:</span>
                            <span class="detail-value">{{ $student->student->blood_group }}</span>
                        </div>
                        @endif

                        @if($student->student?->admission_no)
                        <div class="detail-item">
                            <span class="detail-label">Admission No:</span>
                            <span class="detail-value">{{ $student->student->admission_no }}</span>
                        </div>
                        @endif

                        @if($student->student?->roll_no)
                        <div class="detail-item">
                            <span class="detail-label">Roll No:</span>
                            <span class="detail-value">{{ $student->student->roll_no }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @elseif($reportType === 'summary')
            <table class="table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Father Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->father_name ?? '—' }}</td>
                            <td>{{ $student->student?->dob ? \Carbon\Carbon::parse($student->student->dob)->age : '—' }}</td>
                            <td>{{ $student->student?->gender ?? '—' }}</td>
                            <td>{{ $student->studentClasses->first()?->status === 'active' ? 'Active' : 'Inactive' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Father Name</th>
                        <th>Tazkira Number</th>
                        <th>Current Address</th>
                        <th>Contact Number</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->father_name ?? '—' }}</td>
                            <td>{{ $student->student?->tazkira_number ?? '—' }}</td>
                            <td>{{ $student->student?->address ?? '—' }}</td>
                            <td>{{ $student->student?->phone ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <!-- Footer -->
        <div class="footer">

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $schoolName }}. All rights reserved.</p>
                @php
                    $studentInfoContact = implode(' | ', array_filter([
                        $settings['support_email'] ?? '',
                        $settings['support_phone_display'] ?? '',
                    ]));
                @endphp
                @if($studentInfoContact !== '')
                    <p>
                        {{ $studentInfoContact }}
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
