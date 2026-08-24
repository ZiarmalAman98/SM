@php
    use Morilog\Jalali\Jalalian;

    $schoolName = trim($settings['app_name'] ?? config('app.name', 'School Management'));
    $logo = $settings['app_logo_url'] ?? asset('schools/cosmos.png');
    $schoolAddress = $settings['school_address_line'] ?? ($settings['address'] ?? 'Kabul, Afghanistan');
    $schoolContactLine = $settings['support_phone_display'] ?? '';
    $user = $contact->user;
    $fullName = trim(($user?->name ?? '') . ' ' . ($user?->last_name ?? '')) ?: __('Deleted user');
    $fatherName = $user?->father_name ?: '-';
    $userType = filled($user?->type) ? __(ucfirst($user->type)) : __('Unknown');
    $role = $user?->roles?->first()?->name;
    $createdAt = $contact->created_at ? Jalalian::fromDateTime($contact->created_at)->format('Y/m/d H:i') : '-';
    $updatedAt = $contact->updated_at ? Jalalian::fromDateTime($contact->updated_at)->format('Y/m/d H:i') : '-';
    $otherNumbers = $user?->contactDetails
        ? $user->contactDetails->where('id', '!=', $contact->id)->values()
        : collect();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Contact Detail') }} - {{ $contact->phone_number }}</title>
    <style>
        :root {
            --brand: #c8642c;
            --brand-dark: #9f4d20;
            --brand-soft: #fff4ec;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #e5d4c7;
            --panel: #fffaf6;
        }

        @page {
            size: A4;
            margin: 12mm;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: linear-gradient(180deg, #f7ede5 0%, #f4f5f7 100%);
            color: var(--ink);
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 13px;
            line-height: 1.45;
        }

        .toolbar { display: flex; justify-content: center; gap: 10px; padding: 18px; }
        .toolbar button {
            border: 0;
            border-radius: 6px;
            padding: 10px 18px;
            background: var(--brand);
            color: #fff;
            cursor: pointer;
            font-weight: 700;
        }
        .toolbar button.secondary { background: #6b7280; }

        .page {
            width: min(210mm, calc(100vw - 24px));
            min-height: 297mm;
            margin: 0 auto 24px;
            background: #fff;
            padding: 14mm;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 24px 50px rgba(77, 37, 12, 0.12);
        }

        .top-line {
            height: 8px;
            background: linear-gradient(90deg, var(--brand-dark), var(--brand));
            margin: -14mm -14mm 18px;
            border-radius: 18px 18px 0 0;
        }

        .header {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 18px;
            border-bottom: 2px solid rgba(200, 100, 44, 0.2);
            padding-bottom: 14px;
        }

        .school { display: flex; align-items: center; gap: 12px; }
        .logo { width: 68px; height: 68px; object-fit: contain; }
        .school h1 { margin: 0; color: #101828; font-size: 24px; font-weight: 800; }
        .muted { margin: 3px 0 0; color: var(--muted); font-size: 12px; }
        .title { text-align: center; }
        .title .en { color: var(--brand-dark); font-size: 22px; font-weight: 800; text-transform: uppercase; }
        .title .local { margin-top: 3px; color: #7c2d12; font-size: 16px; font-weight: 700; }
        .status { justify-self: end; text-align: right; }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border: 1px solid #f0b486;
            border-radius: 4px;
            background: var(--brand-soft);
            color: var(--brand-dark);
            font-weight: 700;
            text-transform: uppercase;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin: 18px 0;
        }

        .box {
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px 12px;
            background: var(--panel);
            min-width: 0;
        }

        .label { color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .value { margin-top: 4px; color: #111827; font-size: 14px; font-weight: 700; overflow-wrap: anywhere; }
        .section-title { margin: 20px 0 8px; color: var(--brand-dark); font-size: 15px; font-weight: 800; text-transform: uppercase; }

        table { width: 100%; border-collapse: collapse; }
        th {
            background: var(--brand);
            color: #fff;
            font-size: 11px;
            padding: 8px 10px;
            text-align: left;
            text-transform: uppercase;
        }
        td { border: 1px solid var(--line); padding: 8px 10px; vertical-align: top; }
        .details th { width: 32%; background: var(--brand-soft); color: var(--brand-dark); text-transform: none; }
        tbody tr:nth-child(even) td { background: #fff9f5; }

        .notes {
            min-height: 72px;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 10px;
            background: var(--panel);
            white-space: pre-wrap;
        }

        .footer {
            margin-top: 28px;
            padding-top: 10px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            display: flex;
            justify-content: space-between;
            gap: 16px;
            font-size: 11px;
        }

        .footer-brand { color: var(--brand-dark); font-size: 12px; font-weight: 800; }

        @media print {
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .toolbar { display: none !important; }
            .page { width: auto; min-height: auto; margin: 0; padding: 0; border: 0; box-shadow: none; border-radius: 0; }
            .top-line { margin: 0 0 18px; border-radius: 0; }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <button onclick="window.print()">{{ __('Print') }}</button>
        <button class="secondary" onclick="window.close()">{{ __('Close') }}</button>
    </div>

    <main class="page">
        <div class="top-line"></div>

        <header class="header">
            <div class="school">
                <img class="logo" src="{{ $logo }}" alt="School logo">
                <div>
                    <h1>{{ $schoolName }}</h1>
                    <p class="muted">{{ __('Reception') }}</p>
                </div>
            </div>
            <div class="title">
                <div class="en">{{ __('Contact Detail') }}</div>
                <div class="local">جزئیات تماس / د اړیکې جزئیات</div>
            </div>
            <div class="status">
                <div class="label">{{ __('Phone Type') }}</div>
                <div class="badge">{{ __($contact->phone_type) }}</div>
                <div class="muted">{{ Jalalian::now()->format('Y/m/d') }}</div>
            </div>
        </header>

        <section class="meta-grid">
            <div class="box">
                <div class="label">{{ __('Full Name') }}</div>
                <div class="value">{{ $fullName }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('F/Name') }}</div>
                <div class="value">{{ $fatherName }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('User Type') }}</div>
                <div class="value">{{ $userType }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Phone') }}</div>
                <div class="value">{{ $contact->phone_number }}</div>
            </div>
        </section>

        <div class="section-title">{{ __('Contact Details') }}</div>
        <table class="details">
            <tbody>
                <tr>
                    <th>{{ __('Full Name') }}</th>
                    <td>{{ $fullName }}</td>
                </tr>
                <tr>
                    <th>{{ __('Last Name') }}</th>
                    <td>{{ $user?->last_name ?: '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __('F/Name') }}</th>
                    <td>{{ $fatherName }}</td>
                </tr>
                <tr>
                    <th>{{ __('User Type') }}</th>
                    <td>{{ $userType }}{{ $role ? ' / ' . __(ucfirst($role)) : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Email') }}</th>
                    <td>{{ $user?->email ?: __('No Email') }}</td>
                </tr>
                <tr>
                    <th>{{ __('Phone Number') }}</th>
                    <td>{{ $contact->phone_number }}</td>
                </tr>
                <tr>
                    <th>{{ __('Phone Type') }}</th>
                    <td>{{ __($contact->phone_type) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Primary') }}</th>
                    <td>{{ $contact->is_primary ? __('Primary contact') : __('Secondary contact') }}</td>
                </tr>
                <tr>
                    <th>{{ __('Created At') }}</th>
                    <td>{{ $createdAt }}</td>
                </tr>
                <tr>
                    <th>{{ __('Updated At') }}</th>
                    <td>{{ $updatedAt }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">{{ __('Notes') }}</div>
        <div class="notes">{{ $contact->notes ?: __('No Notes') }}</div>

        @if ($otherNumbers->isNotEmpty())
            <div class="section-title">{{ __('Other Numbers') }}</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Phone Number') }}</th>
                        <th>{{ __('Phone Type') }}</th>
                        <th>{{ __('Primary') }}</th>
                        <th>{{ __('Notes') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($otherNumbers as $other)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $other->phone_number }}</td>
                            <td>{{ __($other->phone_type) }}</td>
                            <td>{{ $other->is_primary ? __('Yes') : __('No') }}</td>
                            <td>{{ $other->notes ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <footer class="footer">
            <div>
                <div class="footer-brand">{{ $schoolName }}</div>
                <div>{{ $schoolAddress }}</div>
                <div>{{ $schoolContactLine }}</div>
            </div>
            <div style="text-align: right;">
                <div>{{ __('Contact Detail') }}</div>
                <div>{{ $contact->phone_number }}</div>
            </div>
        </footer>
    </main>
</body>

</html>
