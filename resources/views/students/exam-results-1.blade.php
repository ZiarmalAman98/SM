@php
    use Morilog\Jalali\Jalalian;
    use Illuminate\Support\Facades\Storage;

    $schoolName = trim($settings['app_name'] ?? 'School Management');
    $ministryName = 'Ministry of Education Islamic Republic of Afghanistan';
    $departmentName = 'Department of Private Schools';
    $logo = isset($settings['app_logo']) && filled($settings['app_logo'])
        ? asset('storage/' . $settings['app_logo'])
        : asset('schools/cosmos.png');
    $supportPhones = $settings['support_phone_display'] ?? '0771 581 521 / 0785 600 566';
    $schoolAddress = 'Sayed Jamaluddin Afghani Main Road, Kabul';
    $studentName = trim(($user->name ?? '') . ' ' . ($user->last_name ?? '')) ?: '---';
    $birthDate = filled($user->student?->dob)
        ? Jalalian::fromDateTime($user->student->dob)->format('Y/m/d')
        : 'Not Given';
    $studentPhoto = null;
    if (filled($user->student?->photo_path)) {
        $photoPath = ltrim((string) $user->student->photo_path, '/');
        $normalizedPhotoPath = ltrim(str_replace(['storage/', 'public/'], '', $photoPath), '/');

        if (Storage::disk('public')->exists($normalizedPhotoPath)) {
            $studentPhoto = asset('storage/' . $normalizedPhotoPath) . '?v=' . urlencode((string) optional($user->student->updated_at)->timestamp);
        } elseif (file_exists(public_path($photoPath))) {
            $studentPhoto = asset($photoPath) . '?v=' . urlencode((string) optional($user->student->updated_at)->timestamp);
        } else {
            $studentPhoto = asset('storage/' . $normalizedPhotoPath);
        }
    } elseif (filled($user->avatar) && !str_contains($user->avatar, 'ui-avatars.com')) {
        $studentPhoto = $user->avatar;
    }
    $academicYear = now()->format('Y') . ' / ' . Jalalian::now()->getYear();
    $subjectCount = count($subjects);
    $midMaxPerSubject = 40;
    $finalMaxPerSubject = 60;
    $midTotalMax = $subjectCount * $midMaxPerSubject;
    $finalTotalMax = $subjectCount * $finalMaxPerSubject;

    $numberToWords = function (int $number): string {
        $ones = [
            0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven',
            8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen',
            15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen',
        ];
        $tens = [2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty', 6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'];

        $toWords = function (int $value) use (&$toWords, $ones, $tens): string {
            if ($value < 20) {
                return $ones[$value];
            }
            if ($value < 100) {
                $ten = intdiv($value, 10);
                $remainder = $value % 10;
                return $tens[$ten] . ($remainder ? ' ' . $ones[$remainder] : '');
            }
            if ($value < 1000) {
                $hundreds = intdiv($value, 100);
                $remainder = $value % 100;
                return $ones[$hundreds] . ' Hundred' . ($remainder ? ' ' . $toWords($remainder) : '');
            }
            $thousands = intdiv($value, 1000);
            $remainder = $value % 1000;
            return $toWords($thousands) . ' Thousand' . ($remainder ? ' ' . $toWords($remainder) : '');
        };

        return $toWords(max(0, $number)) . ' Only';
    };

    $midPercent = $midTotalMax > 0 ? round(($result['mid_term'] / $midTotalMax) * 100, 2) : 0;
    $finalPercent = $finalTotalMax > 0 ? round(($result['final'] / $finalTotalMax) * 100, 2) : 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etla Namah - {{ $studentName }}</title>
    <style>
        :root {
            --line: #9ca3af;
            --line-strong: #6b7280;
            --head: #d9dde2;
            --head-dark: #c9d3df;
            --ink: #1f2937;
            --muted: #4b5563;
            --fail: #c2410c;
            --paper: #ffffff;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #e5e7eb;
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
            font-size: 12px;
        }

        .toolbar {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 18px;
        }

        .toolbar button {
            border: 0;
            border-radius: 6px;
            padding: 10px 18px;
            background: #475569;
            color: #fff;
            cursor: pointer;
            font-weight: 700;
        }

        .page {
            width: min(210mm, calc(100vw - 24px));
            min-height: 297mm;
            margin: 0 auto 24px;
            background: var(--paper);
            padding: 10mm;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15);
        }

        .document {
            border: 1px solid var(--line);
            padding: 10px;
        }

        .heading {
            position: relative;
            text-align: center;
            padding: 2px 80px 8px;
        }

        .heading img {
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .heading h1, .heading h2, .heading h3 {
            margin: 0;
            line-height: 1.25;
        }

        .heading h1 { font-size: 19px; font-weight: 700; }
        .heading h2 { font-size: 16px; font-weight: 700; }
        .heading h3 { font-size: 24px; font-weight: 800; letter-spacing: 0.3px; text-transform: uppercase; margin-top: 6px; }

        .identity {
            margin-top: 8px;
            border: 1px solid var(--line);
            display: grid;
            grid-template-columns: 1.2fr 0.95fr 88px;
        }

        .identity-table {
            width: 100%;
            border-collapse: collapse;
        }

        .identity-table td {
            border-bottom: 1px solid var(--line);
            padding: 6px 8px;
            font-size: 13px;
        }

        .identity-table tr:last-child td { border-bottom: 0; }
        .identity-table td:first-child { width: 36%; font-weight: 700; background: #f3f4f6; }

        .photo-box {
            border-left: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }

        .placeholder {
            width: 76px;
            height: 76px;
            border: 2px solid var(--line-strong);
            background: #fff;
        }

        .placeholder img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .result-head {
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 8px;
            align-items: center;
        }

        .result-title { font-size: 14px; font-weight: 800; }
        .term-chip {
            min-width: 96px;
            text-align: center;
            border: 1px solid var(--line-strong);
            background: var(--head);
            padding: 6px 10px;
            font-weight: 800;
        }

        .legend {
            margin-top: 4px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            font-size: 11px;
        }

        .legend span { display: inline-flex; align-items: center; gap: 4px; }
        .legend i { width: 12px; height: 12px; display: inline-block; border: 1px solid var(--line-strong); }
        .legend .neutral { background: #94a3b8; }
        .legend .fail { background: #b91c1c; }

        .marks-table {
            width: 100%;
            margin-top: 6px;
            border-collapse: collapse;
        }

        .marks-table th, .marks-table td {
            border: 1px solid var(--line);
            padding: 5px 6px;
            font-size: 12px;
        }

        .marks-table thead th { background: var(--head); text-align: center; font-weight: 800; }
        .marks-table .sub-head { background: var(--head-dark); font-size: 11px; }
        .sn { width: 38px; text-align: center; }
        .subject { width: 34%; }
        .number, .word { text-align: center; }
        .number { width: 62px; }
        .word { width: 19%; }
        .fail-mark { color: var(--fail); font-weight: 800; }

        .summary-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr 180px;
            gap: 10px;
            margin-top: 12px;
            align-items: start;
        }

        .summary-box { border: 1px solid var(--line); }
        .summary-box table { width: 100%; border-collapse: collapse; }
        .summary-box th, .summary-box td {
            border: 1px solid var(--line);
            padding: 5px 6px;
            text-align: center;
            font-size: 12px;
        }

        .summary-box th { background: var(--head); font-size: 13px; font-weight: 800; }
        .summary-box td:first-child { text-align: left; font-weight: 700; background: #f8fafc; }

        .signature-box { padding-top: 10px; font-size: 12px; }
        .signature-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            border-bottom: 1px solid var(--line);
            padding: 10px 0 6px;
        }

        .obtained-line { margin-top: 6px; font-size: 12px; font-weight: 700; }

        .footer {
            margin-top: 18px;
            padding-top: 6px;
            border-top: 10px solid #cbd5e1;
            display: flex;
            justify-content: center;
            gap: 20px;
            color: #374151;
            font-size: 12px;
        }

        @media (max-width: 960px) {
            .page {
                width: calc(100vw - 12px);
                min-height: auto;
                padding: 12px;
                margin-bottom: 12px;
            }

            .heading { padding-right: 0; }
            .heading img { position: static; display: block; margin: 0 auto 8px; }
            .identity, .result-head, .summary-wrap, .footer { grid-template-columns: 1fr; display: grid; }
            .legend { justify-content: flex-start; flex-wrap: wrap; }
        }

        @media print {
            body {
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .toolbar { display: none !important; }
            .page { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Print Etla Namah</button>
    </div>

    <main class="page">
        <section class="document">
            <header class="heading">
                <img src="{{ $logo }}" alt="School logo">
                <h1>{{ $ministryName }}</h1>
                <h2>{{ $departmentName }}</h2>
                <h2>{{ strtoupper($schoolName) }}</h2>
            </header>

            <section class="identity">
                <table class="identity-table" style="grid-column: span 2;">
                    <tr><td>Family ID:</td><td>{{ $familyCode }}</td><td rowspan="5" style="width:120px; background:#fff; vertical-align:bottom; font-weight:700;">Birth Date</td><td rowspan="5" style="width:150px; background:#fff; vertical-align:bottom;">{{ $birthDate }}</td></tr>
                    <tr><td>Role No:</td><td>{{ $user->student?->roll_no ?? '---' }}</td></tr>
                    <tr><td>Student Name:</td><td>{{ $studentName }}</td></tr>
                    <tr><td>Father Name:</td><td>{{ $user->father_name ?? '---' }}</td></tr>
                    <tr><td>Class:</td><td>{{ $class->class_name ?? '---' }}</td></tr>
                </table>

                <div class="photo-box">
                    <div class="placeholder">
                        @if($studentPhoto)
                            <img src="{{ $studentPhoto }}" alt="Student photo">
                        @endif
                    </div>
                </div>
            </section>

            <section class="result-head">
                <div class="result-title">RESULT SHEET <span style="font-weight:700;">Year {{ $academicYear }}</span></div>
                <div class="term-chip">MID TERM</div>
                <div class="term-chip">FINAL TERM</div>
            </section>

            <div class="legend">
                <span><i class="neutral"></i> Not Given</span>
                <span><i class="fail"></i> Fail</span>
            </div>

            <table class="marks-table">
                <thead>
                    <tr>
                        <th rowspan="2" class="sn">SNo</th>
                        <th rowspan="2" class="subject">Subjects</th>
                        <th colspan="2">MID TERM</th>
                        <th colspan="3">FINAL TERM</th>
                        <th rowspan="2" class="word">Word</th>
                    </tr>
                    <tr>
                        <th class="sub-head number">MAX</th>
                        <th class="sub-head number">Obtain</th>
                        <th class="sub-head number">MAX</th>
                        <th class="sub-head number">Obtain</th>
                        <th class="sub-head number">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subjects as $index => $subject)
                        @php
                            $midMark = (int) ($subject['mid_term'] ?? 0);
                            $finalMark = (int) ($subject['final'] ?? 0);
                            $totalMark = (int) ($subject['total'] ?? 0);
                        @endphp
                        <tr>
                            <td class="sn">{{ $index + 1 }}</td>
                            <td class="subject">{{ $subject['name'] }}</td>
                            <td class="number">{{ $midMaxPerSubject }}</td>
                            <td class="number {{ $midMark < 40 ? 'fail-mark' : '' }}">{{ $midMark }}</td>
                            <td class="number">{{ $finalMaxPerSubject }}</td>
                            <td class="number {{ $finalMark < 40 ? 'fail-mark' : '' }}">{{ $finalMark }}</td>
                            <td class="number">{{ $totalMark }}</td>
                            <td class="word">{{ $numberToWords($totalMark) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <section class="summary-wrap">
                <div>
                    <div class="summary-box">
                        <table>
                            <tr><th colspan="2">MID TERM</th></tr>
                            <tr><td>T Days</td><td>{{ $attendance['mid_term']['present'] + $attendance['mid_term']['absent'] + (int) ($attendance['mid_term']['leave'] ?? 0) }}</td></tr>
                            <tr><td>Presents</td><td>{{ $attendance['mid_term']['present'] }}</td></tr>
                            <tr><td>Absent</td><td>{{ $attendance['mid_term']['absent'] }}</td></tr>
                            <tr><td>Leave</td><td>{{ $attendance['mid_term']['leave'] ?? 0 }}</td></tr>
                            <tr><td>Subjects</td><td>{{ $subjectCount }}</td></tr>
                            <tr><td>T Marks</td><td>{{ $midTotalMax }}</td></tr>
                            <tr><td>O Marks</td><td>{{ $result['mid_term'] }}</td></tr>
                            <tr><td>Per</td><td>{{ $midPercent }}</td></tr>
                            <tr><td>Result</td><td>{{ strtoupper($result['status']) }}</td></tr>
                            <tr><td>Grade</td><td>{{ $result['grade'] }}</td></tr>
                        </table>
                    </div>
                    <div class="obtained-line">Obtained {{ $numberToWords((int) $result['mid_term']) }}</div>
                </div>

                <div>
                    <div class="summary-box">
                        <table>
                            <tr><th colspan="2">FINAL TERM</th></tr>
                            <tr><td>T Days</td><td>{{ $attendance['final']['present'] + $attendance['final']['absent'] + (int) ($attendance['final']['leave'] ?? 0) }}</td></tr>
                            <tr><td>Presents</td><td>{{ $attendance['final']['present'] }}</td></tr>
                            <tr><td>Absent</td><td>{{ $attendance['final']['absent'] }}</td></tr>
                            <tr><td>Leave</td><td>{{ $attendance['final']['leave'] ?? 0 }}</td></tr>
                            <tr><td>T Marks</td><td>{{ $finalTotalMax }}</td></tr>
                            <tr><td>O Marks</td><td>{{ $result['final'] }}</td></tr>
                            <tr><td>Per</td><td>{{ $finalPercent }}</td></tr>
                            <tr><td>Result</td><td>{{ strtoupper($result['status']) }}</td></tr>
                            <tr><td>Grade</td><td>{{ $result['grade'] }}</td></tr>
                        </table>
                    </div>
                    <div class="obtained-line">Obtained {{ $numberToWords((int) $result['final']) }}</div>
                </div>

                <div class="signature-box">
                    <div class="signature-row"><span>Signature</span><span></span></div>
                    <div class="signature-row"><span>Teacher</span><span></span></div>
                    <div class="signature-row"><span>Director</span><span></span></div>
                    <div class="signature-row"><span>President</span><span></span></div>
                </div>
            </section>

            <footer class="footer">
                <span>{{ $supportPhones }}</span>
                <span>{{ $schoolAddress }}</span>
            </footer>
        </section>
    </main>
</body>
</html>
