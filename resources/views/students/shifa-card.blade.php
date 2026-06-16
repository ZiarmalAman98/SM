<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID Card</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Lateef:wght@400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600;700&display=swap');

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
                font-family: 'Lateef', 'Noto Sans Arabic', 'Noto Nastaliq Urdu', sans-serif !important;
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
                size: 54mm 86mm;
                margin: 0;
            }
        }

        body {
            font-family: 'Lateef', 'Noto Sans Arabic', 'Noto Nastaliq Urdu', sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .id-card-container {
            width: 54mm;
            height: 86mm;
            perspective: 1000px;
        }

        .id-card {
            width: 100%;
            height: 100%;
            position: relative;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #3b82f6 100%);
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            color: white;
        }

        .id-card::before {
            content: "";
            position: absolute;
            top: 30%;
            right: -60px;
            width: 140px;
            height: 140px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            z-index: 1;
        }

        .id-card::after {
            content: "";
            position: absolute;
            bottom: 20%;
            left: -70px;
            width: 160px;
            height: 160px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            z-index: 1;
        }

        .curved-accent-1 {
            position: absolute;
            top: -30px;
            left: -40px;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            z-index: 1;
        }

        .curved-accent-2 {
            position: absolute;
            bottom: -20px;
            right: -50px;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 1;
        }

        .card-header {
            background: transparent;
            padding: 15px 15px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .school-logo {
            width: 50px;
            height: 50px;
            background-color: white;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }

        .school-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }

        .school-name {
            text-align: center;
        }

        .school-name h2 {
            font-family: 'Lateef', 'Noto Sans Arabic', sans-serif;
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            direction: rtl;
            border-bottom: 2px solid rgba(255, 255, 255, 0.8);
            padding-bottom: 3px;
            display: inline-block;
        }

        .card-body {
            padding: 8px 15px 35px;
            /* Added bottom padding to avoid footer overlap */
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .photo-section {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
        }

        .student-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.25);
            margin-bottom: 4px;
        }

        .info-section {
            width: 100%;
            text-align: center;
        }

        .student-info h3 {
            font-family: 'Lateef', 'Noto Sans Arabic', sans-serif;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 0px;
            direction: rtl;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .student-info .father-name {
            font-family: 'Noto Nastaliq Urdu', 'Lateef', sans-serif;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 12px;
            direction: rtl;
            color: rgba(255, 255, 255, 0.95);
        }

        .student-info p {
            margin: 2px 0;
            font-size: 10px;
            color: rgba(255, 255, 255, 0.9);
        }

        .student-info p strong {
            color: white;
            font-weight: 600;
        }

        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 15px;
            background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
            text-align: center;
            font-size: 11px;
            font-weight: 600;
            z-index: 2;
            font-family: 'Noto Nastaliq Urdu', 'Lateef', sans-serif;
            direction: rtl;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 28px;
            line-height: 1.2;
        }

        .signature,
        .validity,
        .blood-group,
        .qr-code {
            display: none;
        }

        .arabic-text {
            font-family: 'Lateef', 'Noto Sans Arabic', 'Noto Nastaliq Urdu', sans-serif;
            direction: rtl;
            text-align: center;
        }
    </style>
</head>

<body onload="window.print();">
    <div class="id-card-container">
        <div class="id-card">
            <div class="curved-accent-1"></div>
            <div class="curved-accent-2"></div>

            <div class="card-header">
                <div class="school-logo">
                    <img src="@if (isset($settings['app_logo'])) {{ asset('storage/' . $settings['app_logo']) }}
                             @else
                                 https://hebbkx1anhila5yf.public.blob.vercel-storage.com/WhatsApp%20Image%202025-08-17%20at%2010.16.59%20AM-JkfiVCExJHzhmZJwDOEl1mxmBXQmv0.jpeg @endif"
                        alt="School Logo">
                </div>
                <div class="school-name">
                    <h2 class="arabic-text">{{ $settings['app_name'] ?? 'شفاء عالي خصوصي لیسه' }}</h2>
                </div>
            </div>

            <div class="card-body">
                <div class="photo-section">
                    @if ($user->student->photo_path)
                        <img src="{{ asset('storage/' . $user->student->photo_path) }}" alt="Student Photo"
                            class="student-photo">
                    @else
                        <img src="{{ asset('images/user.png') }}" alt="Default Photo" class="student-photo">
                    @endif
                </div>

                <div class="info-section">
                    <div class="student-info">
                        <h3 class="arabic-text">{{ $user->name }} {{ $user->last_name }}</h3>
                        <div class="father-name arabic-text">
                            د {{ $user->father_name ?? 'N/A' }}
                            @if ($user->student->gender == 'Male')
                                زوی
                            @else
                                لور
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="arabic-text">د زده کوونکي پیژندکارت</div>
            </div>
        </div>
    </div>
</body>

<script>
    window.onload = function() {
        window.print();
    };

    window.onafterprint = function() {
        window.close();
    };
</script>

</html>
