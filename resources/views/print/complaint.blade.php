@php
    use Morilog\Jalali\Jalalian;

    $schoolName = $settings['app_name'] ?? config('app.name', 'School Management');
    $logo = isset($settings['app_logo']) && filled($settings['app_logo'])
        ? asset('storage/' . $settings['app_logo'])
        : asset('schools/cosmos.png');
    $schoolAddress = $settings['school_address_line'] ?? ($settings['address'] ?? '');
    $schoolContactLine = implode(' | ', array_filter([
        $settings['support_phone_display'] ?? '',
        $settings['support_email'] ?? '',
    ]));

    $complaintDate = $complaint->complaint_date
        ? Jalalian::fromDateTime($complaint->complaint_date)->format('Y/m/d H:i')
        : '-';

    $complainantType = $complaint->complainant_type
        ? __(ucfirst($complaint->complainant_type))
        : __('Not Specified');

    $statusLabel = $complaint->status ? __($complaint->status) : '-';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Complaint') }} #{{ $complaint->id }}</title>
    <style>
        @page { size: A4; margin: 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; background: #eef2f6; color: #172033; font-family: "Segoe UI", Arial, sans-serif; font-size: 13px; line-height: 1.45; }
        .toolbar { display: flex; justify-content: center; gap: 10px; padding: 18px; }
        .toolbar button { border: 0; border-radius: 6px; padding: 10px 18px; background: #155e75; color: #fff; cursor: pointer; font-weight: 600; }
        .toolbar button.secondary { background: #475569; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto 24px; background: #fff; padding: 14mm; border: 1px solid #d8dee8; box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12); }
        .top-line { height: 6px; background: #155e75; margin: -14mm -14mm 18px; }
        .header { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 18px; border-bottom: 2px solid #155e75; padding-bottom: 14px; }
        .school { display: flex; align-items: center; gap: 12px; }
        .logo { width: 68px; height: 68px; object-fit: contain; }
        .school h1 { margin: 0; color: #0f172a; font-size: 24px; font-weight: 800; }
        .muted { color: #64748b; font-size: 12px; }
        .title { text-align: center; }
        .title .en { color: #155e75; font-size: 23px; font-weight: 800; text-transform: uppercase; }
        .title .local { margin-top: 3px; color: #334155; font-size: 16px; font-weight: 700; }
        .number-box { justify-self: end; text-align: right; }
        .badge { display: inline-block; padding: 5px 10px; border: 1px solid #155e75; border-radius: 4px; background: #ecfeff; color: #155e75; font-weight: 800; }
        .meta-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 18px 0; }
        .box { border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 12px; background: #f8fafc; }
        .label { color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .value { margin-top: 4px; color: #0f172a; font-size: 14px; font-weight: 700; }
        .section-title { margin: 20px 0 8px; color: #155e75; font-size: 15px; font-weight: 800; text-transform: uppercase; }
        .details { width: 100%; border-collapse: collapse; }
        .details th { width: 32%; background: #f1f5f9; color: #475569; text-align: left; font-size: 12px; padding: 10px 12px; border: 1px solid #cbd5e1; vertical-align: top; }
        .details td { padding: 10px 12px; border: 1px solid #cbd5e1; color: #0f172a; font-weight: 600; }
        .content-box { min-height: 80px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 12px; background: #f8fafc; }
        .content-box p { margin: 0 0 8px; }
        .content-box p:last-child { margin-bottom: 0; }
        .signatures { display: grid; grid-template-columns: repeat(2, 1fr); gap: 18px; margin-top: 48px; }
        .signature { padding-top: 36px; border-top: 1px solid #0f172a; text-align: center; color: #475569; font-weight: 700; }
        .footer { margin-top: 28px; padding-top: 14px; border-top: 1px solid #cbd5e1; color: #475569; text-align: center; font-size: 12px; line-height: 1.6; }
        .footer .footer-title { color: #155e75; font-size: 13px; font-weight: 800; text-transform: uppercase; margin-bottom: 4px; }
        .footer .footer-address { color: #334155; font-weight: 600; }
        .footer .footer-contact { color: #64748b; margin-top: 2px; }
        @media print {
            body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .toolbar { display: none !important; }
            .page { width: auto; min-height: auto; margin: 0; padding: 0; border: 0; box-shadow: none; }
            .top-line { margin: 0 0 18px; }
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
                <img class="logo" src="{{ $logo }}" alt="{{ $schoolName }}">
                <div>
                    <h1>{{ $schoolName }}</h1>
                </div>
            </div>
            <div class="title">
                <div class="en">{{ __('Complaint') }}</div>
                <div class="local">{{ __('Complaints') }}</div>
            </div>
            <div class="number-box">
                <div class="muted">#</div>
                <div class="badge">{{ $complaint->id }}</div>
            </div>
        </header>

        <section class="meta-grid">
            <div class="box">
                <div class="label">{{ __('Date') }}</div>
                <div class="value">{{ $complaintDate }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Status') }}</div>
                <div class="value">{{ $statusLabel }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Complainant Type') }}</div>
                <div class="value">{{ $complainantType }}</div>
            </div>
            <div class="box">
                <div class="label">{{ __('Complainant') }}</div>
                <div class="value">{{ $complaint->complainant?->name ?? __('Not Specified') }}</div>
            </div>
        </section>

        <div class="section-title">{{ __('Complaint Form') }}</div>
        <table class="details">
            <tbody>
                <tr>
                    <th>{{ __('Correspondent') }}</th>
                    <td>{{ $complaint->correspondent?->name ?? __('Not Specified') }}</td>
                </tr>
                <tr>
                    <th>{{ __('Subject') }}</th>
                    <td>{{ $complaint->subject }}</td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">{{ __('Description') }}</div>
        <div class="content-box">{!! $complaint->description !!}</div>

        <div class="section-title">{{ __('Resolution Notes') }}</div>
        <div class="content-box">
            @if(filled($complaint->resolution_notes))
                {!! $complaint->resolution_notes !!}
            @else
                {{ __('No Notes') }}
            @endif
        </div>

        <div class="signatures">
            <div class="signature">{{ __('Complainant') }}</div>
            <div class="signature">{{ __('Reception') }}</div>
        </div>

        <footer class="footer">
            <div class="footer-title">{{ __('Complaint') }}</div>
            @if($schoolAddress !== '')
                <div class="footer-address">{{ $schoolAddress }}</div>
            @endif
            @if($schoolContactLine !== '')
                <div class="footer-contact">{{ $schoolContactLine }}</div>
            @endif
        </footer>
    </main>
</body>

</html>
