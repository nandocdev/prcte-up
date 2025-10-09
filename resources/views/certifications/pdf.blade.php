<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ __('certifications.pdf.title') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1a202c;
            margin: 0;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 22px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header h2 {
            font-size: 14px;
            margin: 8px 0 0 0;
            color: #4a5568;
        }

        .section {
            margin-bottom: 24px;
        }

        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr td {
            padding: 6px 4px;
            vertical-align: top;
        }

        table tr td.label {
            width: 30%;
            font-weight: bold;
            text-transform: uppercase;
            color: #2d3748;
        }

        .footer {
            margin-top: 40px;
            font-size: 11px;
            color: #4a5568;
            text-align: center;
        }

        .highlight {
            background-color: #edf2f7;
            padding: 10px;
            border-radius: 6px;
            margin-top: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('certifications.pdf.title') }}</h1>
        <h2>{{ __('certifications.pdf.subtitle') }}</h2>
    </div>

    <div class="section">
        <div class="section-title">{{ __('certifications.pdf.certification_number') }}</div>
        <div class="highlight">
            <strong>{{ $certification->certification_number }}</strong>
        </div>
    </div>

    <div class="section">
        <table>
            <tr>
                <td class="label">{{ __('certifications.pdf.work_title') }}</td>
                <td>{{ $work->title }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('certifications.pdf.work_type') }}</td>
                <td>{{ $work->workType?->name }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('certifications.pdf.responsible') }}</td>
                <td>{{ $work->primaryResponsible?->name }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('certifications.pdf.organizational_unit') }}</td>
                <td>{{ $work->organizationalUnit?->name }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr>
                <td class="label">{{ __('certifications.pdf.issue_date') }}</td>
                <td>{{ optional($certification->issue_date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('certifications.pdf.valid_until') }}</td>
                <td>{{ optional($certification->valid_until)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('certifications.pdf.issued_by') }}</td>
                <td>{{ $certification->issuedByUser?->name }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">{{ __('certifications.pdf.verification_code') }}</div>
        <div class="highlight">
            <strong>{{ $certification->certification_number }}</strong><br>
            <small>{{ __('certifications.pdf.verification_hint') }}</small>
        </div>
    </div>

    @if($certification->comments)
        <div class="section">
            <div class="section-title">{{ __('certifications.pdf.comments') }}</div>
            <div class="highlight">
                {{ $certification->comments }}
            </div>
        </div>
    @endif

    <div class="footer">
        {{ __('certifications.pdf.footer') }}
    </div>
</body>
</html>
