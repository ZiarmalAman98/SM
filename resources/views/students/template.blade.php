<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>مکتوب معرفی</title>
  <!-- Fonts: Noto Naskh Arabic + Vazirmatn -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css">
  <style>
    @page {
      size: A4;
      margin: 15mm;
    }

    body {
      font-family: 'Noto Naskh Arabic', 'Vazirmatn', Tahoma, Arial, sans-serif;
      direction: rtl;
      font-size: 13pt;
      line-height: 1.8;
      margin: 0;
      padding: 0;
    }

    /* Top right buttons */
    .top-buttons {
      position: fixed;
      top: 10px;
      right: 15px;
      z-index: 1000;
    }

    .top-buttons button {
      margin-left: 8px;
      padding: 6px 14px;
      font-size: 12pt;
      cursor: pointer;
      background: #f5f5f5;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    .top-buttons button:hover {
      background: #e9e9e9;
    }

    /* Hide buttons in print view */
    @media print {
      .top-buttons {
        display: none;
      }
    }

    /* .container {
      display: block;
      padding: 10mm 10mm;
    } */
    .container {
      padding: 20mm 2mm;
    }

    .copy {
      width: 100%;
      box-sizing: border-box;
      margin-bottom: 30mm;
    }

    .header {
      text-align: center;
      margin-bottom: 20px;
      line-height: 1.4;
    }

    .header img {
      width: 60px;
      height: auto;
      margin-bottom: 5px;
    }

    p {
      text-align: justify;
      margin: 12px 0;
    }

    .footer {
      margin-top: 60px;
      text-align: center;
      line-height: 1.8;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .large-space {
      display: inline-block;
      min-width: 250px;
    }

    .md-space {
      display: inline-block;
      min-width: 120px;
    }

    .divider {
      border-top: 1px dashed #777;
      margin: 20mm 0;
    }
  </style>
</head>
<body>

  <!-- Simple Top Buttons -->
  <div class="top-buttons">
    <button onclick="window.print()">Print</button>
    <button onclick="window.history.back()">Back</button>
  </div>

  <div class="container">

    <!-- FIRST COPY -->
    <div class="copy">
      <div class="header">
        <img src="{{ $settings['app_logo_url'] ?? asset('schools/cosmos.png') }}" alt="لوگو">
        <div>امارت اسلامی افغانستان</div>
        <div>وزارت معارف</div>
        <div>ریاست معارف شهر</div>
        <div>آمریت حوزه یازدهم تعلیمی</div>
        <div>مدیریت لیسه ({{ $settings['app_name'] ?? config('app.name') }})</div>
      </div>

      <p><strong>به مدیریت محترم</strong> ( <span class="large-space"></span> )</p>

      <p>
        اینکه طعم مکتوب هذا سوابق تعلیمی ارجمند
        ( <span class="md-space">{{ $student->user->name ?? '' }}</span> )
        ولد
        ( <span class="md-space">{{ $student->user->father_name ?? '' }}</span> )
        صنف
        ( <span class="md-space">{{ $class->class_name ?? '' }}</span> )
        سال تعلیمی
        ( <span class="md-space">{{ ($request->year ?? '') - 621 }}</span> )
        نظر به موافقه قبلی مقام محترم ترتیب و ارسال است انتقال است.
        امید بعد از شمولیت رسمی  این اداره  را اطمینان  بخشیده ممنون سازید.
      </p>

      <div class="footer">
        <p>با احترام</p>
        <p>مدیر لیسه ( <span class="large-space"></span> )</p>
      </div>
    </div>


  </div>

</body>
</html>
