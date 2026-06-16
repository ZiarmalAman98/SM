<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Card</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body {
                background: none !important;
                margin: 0 !important;
                padding: 0 !important;
                font-family: 'Poppins', sans-serif !important;
            }

            .id-card-container {
                box-shadow: none !important;
                margin: 0 !important;
            }

            .id-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            @page {
                size: 86mm 54mm;
                margin: 0;
            }
        }


        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* ID card styling */
        .id-card-container {
            width: 86mm;
            /* Standard ID card width */
            height: 54mm;
            /* Standard ID card height */
            perspective: 1000px;
        }

        .id-card {
            width: 100%;
            height: 100%;
            position: relative;
            background: linear-gradient(135deg, #ffffff 0%, #f0f8ff 100%);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            color: #333;
        }

        .card-header {
            background: linear-gradient(90deg, #1a5276 0%, #2980b9 100%);
            padding: 12px 15px 8px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #f39c12;
        }

        .school-logo {
            width: 40px;
            height: 40px;
            background-color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 10px;
            color: #1a5276;
        }

        .school-name {
            text-align: center;
            flex-grow: 1;
        }

        .school-name h2 {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .school-name p {
            font-size: 8px;
            margin-top: 2px;
            opacity: 0.9;
        }

        .card-body {
            padding: 10px 15px;
            display: flex;
            position: relative;
        }

        .card-body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%232980b9' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
            z-index: 0;
        }

        .photo-section {
            width: 30%;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 1;
        }

        .student-photo {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            object-fit: cover;
            border: 1px solid #2980b9;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 5px;
        }

        .qr-code {
            width: 50px;
            height: 50px;
            background-color: #fff;
            border: 1px solid #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 6px;
            margin-top: 5px;
        }

        .info-section {
            width: 70%;
            padding-left: 10px;
            z-index: 1;
        }

        .student-info h3 {
            color: #1a5276;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
            border-bottom: 1px dashed #2980b9;
            padding-bottom: 3px;
        }

        .student-info p {
            margin: 3px 0;
            font-size: 10px;
            display: flex;
        }

        .student-info p strong {
            width: 80px;
            color: #2c3e50;
        }

        .card-footer {
            padding: 5px 15px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 8px;
            border-top: 1px solid #e0e0e0;
            background-color: rgba(41, 128, 185, 0.05);
        }

        .signature {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .signature-line {
            width: 70px;
            height: 1px;
            background-color: #333;
            margin-bottom: 2px;
        }

        .validity {
            text-align: right;
        }

        .blood-group {
            position: absolute;
            top: 10px;
            right: 15px;
            background-color: #e74c3c;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 3px;
        }

        /* Print-specific styles */
        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }

            .id-card-container {
                box-shadow: none;
                margin: 0;
            }

            .id-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }

            @page {
                size: 86mm 54mm;
                margin: 0;
            }
        }
    </style>
</head>

<body onload="window.print();">

    <div class="id-card-container">
        <div class="id-card">
            <!-- Card Header -->
            <div class="card-header">
                <div class="school-logo">
                    {{-- <img src="{{ asset('schools/cosmos.png') }}" alt="Default Photo" class="school-logo"> --}}
                    <img src="@if (isset($settings['app_logo'])) {{ asset('storage/' . $settings['app_logo']) }}
         @else
             {{ asset('schools/cosmos.png') }} @endif"
                        alt="Default Photo" class="school-logo" width="180" height="60">
                </div>
                <div class="school-name">
                    <h2>{{ $settings['app_name'] ?? env('APP_NAME', 'School Management') }}</h2>
                    <p>Excellence in Education Since 1995</p>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Photo Section -->
                <div class="photo-section">
                    @if ($user->student->photo_path)
                        <img src="{{ asset('storage/' . $user->student->photo_path) }}" alt="Student Photo"
                            class="student-photo">
                    @else
                        <img src="{{ asset('images/user.png') }}" alt="Default Photo" class="student-photo">
                    @endif
                    <div class="qr-code"> <img
                            src="https://freeqr.com/api/v1/?size=300x300&color=24aae1&data=Roll+No:+{{ urlencode($user->student->roll_no) }}"
                            alt="Student Roll No QR Code" class="qr-code">
                    </div>
                </div>

                <!-- Info Section -->
                <div class="info-section">
                    <div class="student-info">
                        <h3>{{ $user->name }} {{ $user->last_name }}</h3>
                        <p><strong>Roll No:</strong> {{ $user->student->roll_no ?? 'N/A' }}</p>
                        <p><strong>Admission No:</strong> {{ $user->student->admission_no ?? 'N/A' }}</p>
                        <p><strong>DOB:</strong> {{ $user->student->dob ?? 'N/A' }}</p>
                        <p><strong>Section:</strong> {{ $user->student->section->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
<script>
    window.onload = function() {
        window.print();
    };

    window.onafterprint = function() {
        window.close();
    };
</script>
