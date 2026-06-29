<!DOCTYPE html>
<html lang="ps" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>د لیږد سند</title>
    <style>
        @page { 
            size: A4; 
            margin: 14mm 16mm 16mm 16mm; 
        }
        * { 
            box-sizing: border-box; 
            margin: 0;
            padding: 0;
        }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            color: #222;
            background-color: #f9f9f9;
            line-height: 1.4;
        }
        .sheet {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #1a4d8c;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .meta {
            font-size: 14px;
            color: #555;
        }
        .title {
            font-size: 22px;
            font-weight: 700;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            color: #1a4d8c;
            margin: 15px 0;
        }
        .logo {
            height: 40px;
        }
        .sub {
            font-size: 14px;
            margin-top: 5px;
            text-align: center;
            color: #666;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #444;
            padding: 8px 10px;
            vertical-align: middle;
            font-size: 14px;
        }
        .table th {
            font-weight: 600;
            background-color: #f0f5ff;
        }
        .photo-container {
            width: 35mm;
            height: 45mm;
            margin: 2mm auto;
            border: 1px solid #000;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .center {
            text-align: center;
        }
        .signature-section {
            margin-top: 30px;
        }
        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .signature-box {
            text-align: center;
            width: 30%;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin: 10px 0;
            padding-top: 5px;
        }
        .no-print {
            display: none;
        }
        .action-bar {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: flex-end;
            padding: 10px 0;
        }
        .action-btn {
            background-color: #1a4d8c;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }
        .action-btn.secondary { background-color: #666; }
        @media print {
            .action-bar { display: none !important; }
            body { background-color: #fff; }
        }
        .certificate-title {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 15px 0;
            color: #1a4d8c;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
            color: #666;
        }
    </style>
    <script> 
        window.onload = function() {
            // Auto-print functionality
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-YcsIPQ6xQ0Zq+Qv3QkQ7J1w8fEo8h3VQqQmYIVsQm3f8j2g1Q5c4oXzA5Xk9qD1p2k8A2QX0vI1zH5m1xZ9v6g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body>
    <div class="sheet">
        <div class="action-bar">
            <button id="btn-back" class="action-btn secondary" type="button">شاته</button>
            <button id="btn-close" class="action-btn secondary" type="button">تړل</button>
            <button id="btn-print" class="action-btn" type="button">چاپ</button>
            <button id="btn-pdf" class="action-btn" type="button">پی ډی اف ډاونلوډ</button>
        </div>
        <div class="header">
            <div class="header-top">
                <div class="meta">د سند نمبر: <strong>{{ $serialNo }}</strong></div>
                <!-- <div class="meta">د جاري کیدو نېټه: <strong>{{ now()->format('Y-m-d') }}</strong></div> -->
                @php
                    use Morilog\Jalali\Jalalian;
                @endphp

                <div class="meta">
                    د جاري کیدو نېټه: 
                    <strong>{{ Jalalian::now()->format('Y-m-d') }}</strong>
                </div>

            </div>
            <div class="title">
                @if(!empty($logoSrc))
                    <img src="{{ $logoSrc }}" class="logo" alt="{{ $appSettings['app_name'] ?? 'Company Logo' }}">
                @endif
                <div>
                    <h4 class="mb-0" style="margin:0;">{{ $appSettings['app_name'] ?? ($settings['school_name_en'] ?? ($settings['school_name'] ?? 'پاراپامیزاد عالي لیسې')) }}</h4>
                </div>
            </div>
            <div class="sub">{{ $settings['school_address_line'] ?? '' }}</div>
        </div>

        <div class="certificate-title">د لیږد سند</div>

        <table class="table">
            <tr>
                <td rowspan="4" style="width:42mm" class="center">
                    <div class="photo-container">
                        @php
                            $photo = $student->student?->photo_path ? \Illuminate\Support\Facades\Storage::url($student->student->photo_path) : null;
                        @endphp
                        @if($photo)
                            <img class="photo" src="{{ $photo }}" alt="د زده کونکي عکس" />
                        @else
                            <span style="color: #999;">عکس نشته</span>
                        @endif
                    </div>
                </td>
                <th class="center" style="width:12%">رول نمبر</th>
                <td class="center">{{ $student->student->roll_no ?? '' }}</td>
                <th class="center" style="width:15%">نوم</th>
                <td class="center" colspan="3">{{ $student->name }}</td>
            </tr>
            <tr>
                <th class="center">د پلار نوم</th>
                <td class="center">{{ $student->student->father_name ?? '' }}</td>
                <th class="center">د نیکه نوم</th>
                <td class="center" colspan="3">{{ $student->student->grand_father_name ?? '' }}</td>
            </tr>
            <tr>
                <th class="center">د تذکری شمېره</th>
                <td class="center">{{ $student->student->tazkira_number ?? '' }}</td>
                <th class="center">ټولګی</th>
                <td class="center">{{ $class->class_name }}</td>
                <th class="center">د زده کړی کال</th>
                <td class="center">{{ $academic_year ?? ($student->latestEnrollment?->academic_year ?? '') }}</td>
            </tr>
            <tr>
                <th class="center">څانګه</th>
                <td class="center">{{ $branch->branch_name }}</td>
                <th class="center">ښوونکی</th>
                <td class="center">{{ $class->teacher?->name ?? '' }}</td>
                <th class="center">تلیفون</th>
                <td class="center">{{ $student->student->phone ?? '' }}</td>
            </tr>
        </table>

        <table class="table">
            <tr>
                <th class="center" style="width:15%">د زیږیدلو ځای</th>
                <td class="center" style="width:20%">{{ $student->student->birth_place ?? '' }}</td>
                <th class="center" style="width:15%">د زیږیدلو نېټه</th>
                <td class="center" style="width:20%">{{ $student->student->dob ?? '' }}</td>
                <th class="center" style="width:15%">ولایت</th>
                <td class="center" style="width:15%">{{ $student->student->province ?? '' }}</td>
            </tr>
            <tr>
                <th class="center">ولسوالی</th>
                <td class="center">{{ $student->student->district ?? '' }}</td>
                <th class="center">کلی</th>
                <td class="center" colspan="3">{{ $student->student->village ?? '' }}</td>
            </tr>
        </table>

        <div class="signature-section">
            <div class="signature-row">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>د لیسې رئيس / سر ښوونکی</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>د مرکز مسوول</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <div>د لیسې مهر</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>دا تایید کیږي چې پورتنۍ معلومات سم دي او زده کونکی خپلې زده کړې په دې مؤسسه کې بشپړې کړې دي.</p>
        </div>
    </div>
    <script>
        (function(){
            var sheet = document.querySelector('.sheet');
            var btnPdf = document.getElementById('btn-pdf');
            var btnPrint = document.getElementById('btn-print');
            var btnClose = document.getElementById('btn-close');
            var btnBack = document.getElementById('btn-back');

            if (btnPdf) {
                btnPdf.addEventListener('click', function(){
                    var opt = {
                        margin:       [10, 10, 10, 10],
                        filename:     ('Transfer-Certificate-' + ({{ json_encode($serialNo ?? '' ) }} || '').toString().replace(/\s+/g,'_')) + '.pdf',
                        image:        { type: 'jpeg', quality: 0.98 },
                        html2canvas:  { scale: 2, useCORS: true, logging: false },
                        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    };
                    // Clone to temporarily hide controls during export
                    var clone = sheet.cloneNode(true);
                    var bar = clone.querySelector('.action-bar');
                    if (bar) bar.parentNode.removeChild(bar);
                    html2pdf().from(clone).set(opt).save();
                });
            }
            if (btnPrint) {
                btnPrint.addEventListener('click', function(){ window.print(); });
            }
            if (btnClose) {
                btnClose.addEventListener('click', function(){ window.close(); });
            }
            if (btnBack) {
                btnBack.addEventListener('click', function(){
                    if (history.length > 1) { history.back(); }
                    else { window.close(); }
                });
            }
        })();
    </script>
</body>
</html>
