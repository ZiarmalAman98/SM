<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>اطمینانیه</title>
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

    @media print {
      .top-buttons {
        display: none;
      }
    }

    .container {
      padding: 20mm 2mm;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin-bottom: 15px;
    }

    .header img {
      width: 75px;
      height: auto;
    }

    .header-text {
      text-align: center;
      flex: 1;
      line-height: 1.5;
      margin: 0 20px;
    }

    .title {
      text-align: center;
      font-weight: bold;
      font-size: 16pt;
      margin: 20px 0 30px 0;
      text-decoration: underline;
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
      min-width: 200px;
    }

    .md-space {
      display: inline-block;
      min-width: 120px;
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

    <!-- HEADER -->
    <div class="header">
      <img src="{{ $settings['app_logo_url'] ?? asset('schools/cosmos.png') }}" alt="لوگو راست">
      <div class="header-text">
        <div>امارت اسلامی افغانستان</div>
        <div>وزارت معارف</div>
        <div>ریاست معارف شهر کابل</div>
        <div>آمریت حوزه یازدهم تعلیمی</div>
        <div>مدیریت لیسه ({{ $settings['app_name'] ?? config('app.name') }})</div>
      </div>
      <img src="{{ asset('schools/ministry_of_education.jpeg') }}" alt="لوگو چپ">
    </div>

    <!-- BODY -->
    <p><strong>به مدیریت محترم</strong> ( <span class="large-space"></span> )</p>
    <p>
      محترم سوابق تعلیمی و پرچه دوم تبدیلی ارجمند
      ( <span class="md-space">{{ $student->user->name ?? '' }}</span> )
      فرزند
      ( <span class="md-space">{{ $student->user->father_name ?? '' }}</span> )
      متعلم صنف
      ( <span class="md-space">{{ $class->class_name ?? '' }}</span> )
      شما که قرار مکتوب
      ( <span class="md-space"></span> )
      به این اداره مواصلت نموده است در صنف شامل شماره (  <span class="md-space"></span>       )
      دفتر اساس ګردیده  از شمولیت شان اطمینان داده شد
    </p>

    <p>
      این اطمینانیه جهت تأیید و استفاده در موارد ضروری صادر گردیده است.
    </p>

    <!-- FOOTER -->
    <div class="footer">
        <p>با احترام</p>
        <p>مدیر لیسه ( <span class="large-space"></span> )</p>
      </div>
  </div>

</body>
</html>
