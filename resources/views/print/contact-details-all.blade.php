@php
    use Morilog\Jalali\Jalalian;

    $schoolName = trim($settings['app_name'] ?? config('app.name', 'School Management'));
    $logo = $settings['app_logo_url'] ?? asset('schools/cosmos.png');
    $schoolAddress = $settings['school_address_line'] ?? ($settings['address'] ?? 'Kabul, Afghanistan');
    $schoolContactLine = $settings['support_phone_display'] ?? '';
    $primaryCount = $contacts->where('is_primary', true)->count();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Print All Contact Details') }}</title>
    <style>
        :root {
            --brand: #c8642c;
            --brand-dark: #9f4d20;
            --brand-soft: #fff4ec;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #e5d4c7;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: linear-gradient(180deg, #f7ede5 0%, #f4f5f7 100%);
            color: var(--ink);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
        }

        .toolbar { display: flex; justify-content: center; gap: 10px; padding: 16px; }
        .toolbar button {
            border: 0;
            border-radius: 6px;
            padding: 9px 16px;
            background: var(--brand);
            color: #fff;
            cursor: pointer;
            font-weight: 700;
        }
        .toolbar button.secondary { background: #6b7280; }

        .page {
            width: min(277mm, calc(100vw - 24px));
            min-height: 190mm;
            margin: 0 auto 20px;
            background: #fff;
            padding: 12mm;
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 18px 40px rgba(77, 37, 12, 0.10);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            border-bottom: 2px solid rgba(200, 100, 44, 0.25);
            padding-bottom: 12px;
        }

        .school { display: flex; align-items: center; gap: 12px; }
        .logo { width: 58px; height: 58px; object-fit: contain; }
        h1, h2 { margin: 0; }
        h1 { font-size: 22px; }
        h2 { color: var(--brand-dark); font-size: 18px; text-transform: uppercase; }
        .muted { color: var(--muted); font-size: 11px; }

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 16px 0;
        }

        .box {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px;
            background: var(--brand-soft);
        }

        .label { color: var(--muted); font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .value { margin-top: 4px; font-size: 16px; font-weight: 800; }

        table { width: 100%; border-collapse: collapse; }
        th {
            background: var(--brand);
            color: #fff;
            padding: 8px;
            text-align: left;
            border: 1px solid var(--brand-dark);
            font-size: 11px;
            text-transform: uppercase;
        }
        td { padding: 7px 8px; border: 1px solid var(--line); vertical-align: top; overflow-wrap: anywhere; }
        tbody tr:nth-child(even) { background: #fff9f5; }
        .empty { padding: 30px; text-align: center; color: var(--muted); border: 1px solid var(--line); }

        .footer {
            margin-top: 16px;
            padding-top: 10px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        @media print {
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .toolbar { display: none !important; }
            .page { width: auto; min-height: auto; margin: 0; padding: 0; border: 0; box-shadow: none; border-radius: 0; }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()">{{ __('Print All') }}</button>
        <button class="secondary" onclick="window.close()">{{ __('Close') }}</button>
    </div>

    <main class="page">
        <header class="header">
            <div class="school">
                <img class="logo" src="{{ $logo }}" alt="School logo">
                <div>
                    <h1>{{ $schoolName }}</h1>
                    <div class="muted">{{ __('Reception') }}</div>
                    @if ($schoolAddress !== '')
                        <div class="muted">{{ $schoolAddress }}</div>
                    @endif
                    @if ($schoolContactLine !== '')
                        <div class="muted">{{ $schoolContactLine }}</div>
                    @endif
                </div>
            </div>
            <div>
                <h2>{{ __('Contact Details') }}</h2>
                <div class="muted">{{ __('All phone numbers') }}</div>
                <div class="muted">{{ Jalalian::now()->format('Y/m/d') }}</div>
            </div>
        </header>

        <section class="summary">
            <div class="box">
                <div class="label">{{ __('Total Numbers') }}</div>
                <div class="value">{{ $contacts->count() }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Primary') }}</div>
                <div class="value">{{ $primaryCount }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Printed') }}</div>
                <div class="value">{{ Jalalian::now()->format('Y/m/d') }}</div>
            </div>
        </section>

        @if ($contacts->isEmpty())
            <div class="empty">{{ __('No phone numbers found') }}</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Full Name') }}</th>
                        <th>{{ __('F/Name') }}</th>
                        <th>{{ __('User Type') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone Number') }}</th>
                        <th>{{ __('Phone Type') }}</th>
                        <th>{{ __('Primary') }}</th>
                        <th>{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contacts as $contact)
                        @php
                            $user = $contact->user;
                            $fullName = trim(($user?->name ?? '') . ' ' . ($user?->last_name ?? '')) ?: __('Deleted user');
                            $userType = filled($user?->type) ? __(ucfirst($user->type)) : __('Unknown');
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $fullName }}</td>
                            <td>{{ $user?->father_name ?: '-' }}</td>
                            <td>{{ $userType }}</td>
                            <td>{{ $user?->email ?: '-' }}</td>
                            <td>{{ $contact->phone_number }}</td>
                            <td>{{ __($contact->phone_type) }}</td>
                            <td>{{ $contact->is_primary ? __('Yes') : __('No') }}</td>
                            <td>{{ $contact->notes ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <footer class="footer">
            <span>{{ $schoolName }}</span>
            <span>{{ __('Contact Details') }} — {{ $contacts->count() }}</span>
        </footer>
    </main>
</body>

</html>
