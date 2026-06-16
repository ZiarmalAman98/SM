<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>قسمت‌کننده سه‌پرچه</title>
  <style>
    @page { size: A4; margin: 8mm; }
    body {
      font-family: 'Noto Naskh Arabic','Vazirmatn',Tahoma,Arial,sans-serif;
      direction: rtl;
      font-size: 11pt;
      line-height: 1.6;
      margin: 0;
      color: #000;
    }

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
    @media print { .top-buttons { display: none; } }

    .page { padding: 5mm 8mm; }
    .copy { margin-bottom: 10mm; }

    .title {
      text-align: center;
      font-weight: bold;
      margin: 4mm 0;
    }

    table {
      width: 100%;
      border: 1px solid #000;
      border-collapse: collapse;
      table-layout: fixed;
      font-size: 10.5pt;
    }
    th, td {
      border: 1px solid #000;
      padding: 3px;
      text-align: center;
      vertical-align: middle;
      word-wrap: break-word;
    }
    th { font-weight: bold; }

    .note {
      font-size: 10pt;
      text-align: justify;
      margin-top: 4mm;
    }

    .sp-sm { display:inline-block; min-width:90px; }
    .sp-md { display:inline-block; min-width:130px; }
    .sp-lg { display:inline-block; min-width:200px; }
  </style>
</head>
<body>

  <div class="top-buttons">
    <button onclick="window.print()">Print</button>
    <button onclick="window.history.back()">Back</button>
  </div>

  <div class="page">
    @php
      use App\Models\SchoolClass;

      // Fetch class and subjects
      $class = SchoolClass::find($request->class_id ?? $request->class);
      $subjects = $class && $class->subjects->count() > 0
          ? $class->subjects->pluck('name')->toArray()
          : [];

      // Prepare marks
      $midTermScores = [];
      $finalScores = [];
      foreach ($subjects as $subject) {
          $midTermScores[$subject] = method_exists($student, 'getMidTermMark')
              ? $student->getMidTermMark($subject)
              : null;
          $finalScores[$subject] = method_exists($student, 'getFinalMark')
              ? $student->getFinalMark($subject)
              : null;
      }

      $year = $year ?? $request->year ?? '';

      // Fixed 8 rows, dynamic number of columns
      $rows = 8;
      $totalSubjects = count($subjects);
      $cols = ceil($totalSubjects / $rows);
    @endphp

    @for ($copy = 1; $copy <= 3; $copy++)
      <div class="copy">
        <table>
          <thead>
            <tr>
              @for ($set = 1; $set <= $cols; $set++)
                <th>مضمون</th>
                <th>څلورم نیم میاشتنی</th>
                <th>کلنی</th>
                <th>کتنی</th>
              @endfor
            </tr>
          </thead>
          <tbody>
            @for ($row = 0; $row < $rows; $row++)
              <tr>
                @for ($col = 0; $col < $cols; $col++)
                  @php $index = $row + ($col * $rows); @endphp
                  @if ($index < $totalSubjects)
                    <td>{{ $subjects[$index] }}</td>
                    <td>{{ $midTermScores[$subjects[$index]] ?? '' }}</td>
                    <td>{{ $finalScores[$subjects[$index]] ?? '' }}</td>
                    <td></td>
                  @else
                    <td></td><td></td><td></td><td></td>
                  @endif
                @endfor
              </tr>
            @endfor
          </tbody>
        </table>

        <div class="note">
          پورتال متذکره از مضمون‌های فوق در امتحان نیم‌سال شامل گردیده است. شاگرد محترم
          ( <span class="sp-md">{{ $student->user->name ?? '' }}</span> )
          ولد
          ( <span class="sp-md">{{ $student->user->father_name ?? '' }}</span> )
          صنف
          ( <span class="sp-sm">{{ $class->class_name ?? '' }}</span> )
          سال تعلیمی
          ( <span class="sp-sm">{{ $year }}</span> )
          در این لیسه مصروف تعلیم بوده، نتایج فوق درج گردیده است.
          تاریخ ( <span class="sp-sm"></span> ) — امضاء/مهر ( <span class="sp-lg"></span> )
        </div>

        <div class="title">قسمت‌کننده سه‌پرچه</div>
      </div>
    @endfor
  </div>
</body>
</html>
