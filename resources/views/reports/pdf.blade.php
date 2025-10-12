<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ __('reports.pdf.title') }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a202c;
            margin: 0;
            padding: 30px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #2d3748;
        }

        .header h2 {
            font-size: 12px;
            margin: 6px 0 0 0;
            color: #4a5568;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table tr td {
            padding: 5px 3px;
            vertical-align: top;
            border-bottom: 1px solid #e2e8f0;
        }

        table tr td.label {
            width: 35%;
            font-weight: bold;
            text-transform: uppercase;
            color: #2d3748;
            background-color: #f7fafc;
        }

        .status-history {
            margin-top: 15px;
        }

        .status-item {
            padding: 8px;
            margin-bottom: 5px;
            border-left: 3px solid #3182ce;
            background-color: #f7fafc;
        }

        .status-date {
            font-size: 10px;
            color: #718096;
            font-weight: bold;
        }

        .status-user {
            font-size: 10px;
            color: #4a5568;
            margin-top: 2px;
        }

        .status-comments {
            margin-top: 5px;
            font-style: italic;
            color: #2d3748;
        }

        .participants {
            margin-top: 10px;
        }

        .participant-item {
            padding: 6px;
            margin-bottom: 3px;
            background-color: #f7fafc;
            border-radius: 3px;
        }

        .files-list {
            margin-top: 10px;
        }

        .file-item {
            padding: 4px 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 10px;
        }

        .footer {
            margin-top: 30px;
            font-size: 9px;
            color: #718096;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }

        .highlight {
            background-color: #edf2f7;
            padding: 8px;
            border-radius: 4px;
            margin: 5px 0;
        }

        .evaluation-section {
            margin-top: 15px;
        }

        .evaluation-item {
            background-color: #f0fff4;
            border: 1px solid #9ae6b4;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 8px;
        }

        .evaluation-header {
            font-weight: bold;
            color: #22543d;
            margin-bottom: 5px;
        }

        .evaluation-content {
            color: #2d3748;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('reports.pdf.title') }}</h1>
        <h2>{{ __('reports.pdf.subtitle') }}</h2>
        <div style="font-size: 10px; color: #718096; margin-top: 5px;">
            {{ __('reports.pdf.generated_at') }}: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Información Básica del Trabajo -->
    <div class="section">
        <div class="section-title">{{ __('reports.pdf.work_information') }}</div>
        <table>
            <tr>
                <td class="label">{{ __('reports.pdf.work_title') }}</td>
                <td>{{ $work->title }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('reports.pdf.work_type') }}</td>
                <td>{{ $work->workType?->name }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('reports.pdf.current_status') }}</td>
                <td>{{ $work->currentStatus?->name }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('reports.pdf.professor') }}</td>
                <td>{{ $work->primaryResponsible?->name }} ({{ $work->primaryResponsible?->professor_code }})</td>
            </tr>
            <tr>
                <td class="label">{{ __('reports.pdf.organizational_unit') }}</td>
                <td>{{ $work->organizationalUnit?->name }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('reports.pdf.created_at') }}</td>
                <td>{{ optional($work->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('reports.pdf.updated_at') }}</td>
                <td>{{ optional($work->updated_at)->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <!-- Descripción del Trabajo -->
    @if($work->description)
    <div class="section">
        <div class="section-title">{{ __('reports.pdf.description') }}</div>
        <div class="highlight">
            {{ $work->description }}
        </div>
    </div>
    @endif

    <!-- Detalles Específicos del Tipo de Trabajo -->
    @if($work->workType)
    <div class="section">
        <div class="section-title">{{ __('reports.pdf.work_details') }}</div>
        @if($work->workType->name === 'Proyecto')
            @if($work->projectDetail)
            <table>
                <tr>
                    <td class="label">{{ __('reports.pdf.project_type') }}</td>
                    <td>{{ $work->projectDetail->project_type }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('reports.pdf.beneficiaries') }}</td>
                    <td>{{ $work->projectDetail->beneficiaries_count }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('reports.pdf.duration_months') }}</td>
                    <td>{{ $work->projectDetail->duration_months }}</td>
                </tr>
            </table>
            @endif
        @elseif($work->workType->name === 'Actividad')
            @if($work->activityDetail)
            <table>
                <tr>
                    <td class="label">{{ __('reports.pdf.activity_type') }}</td>
                    <td>{{ $work->activityDetail->activity_type }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('reports.pdf.participants_count') }}</td>
                    <td>{{ $work->activityDetail->participants_count }}</td>
                </tr>
            </table>
            @endif
        @elseif($work->workType->name === 'Publicación')
            @if($work->publicationDetail)
            <table>
                <tr>
                    <td class="label">{{ __('reports.pdf.publication_type') }}</td>
                    <td>{{ $work->publicationDetail->publication_type }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('reports.pdf.isbn') }}</td>
                    <td>{{ $work->publicationDetail->isbn }}</td>
                </tr>
            </table>
            @endif
        @elseif($work->workType->name === 'Asistencia Técnica')
            @if($work->technicalAssistanceDetail)
            <table>
                <tr>
                    <td class="label">{{ __('reports.pdf.assistance_type') }}</td>
                    <td>{{ $work->technicalAssistanceDetail->assistance_type }}</td>
                </tr>
                <tr>
                    <td class="label">{{ __('reports.pdf.beneficiary_institution') }}</td>
                    <td>{{ $work->technicalAssistanceDetail->beneficiary_institution }}</td>
                </tr>
            </table>
            @endif
        @endif
    </div>
    @endif

    <!-- Participantes -->
    @if($work->participants && $work->participants->count() > 0)
    <div class="section">
        <div class="section-title">{{ __('reports.pdf.participants') }} ({{ $work->participants->count() }})</div>
        <div class="participants">
            @foreach($work->participants as $participant)
            <div class="participant-item">
                <strong>{{ $participant->name }}</strong>
                @if($participant->role)
                    - {{ $participant->role }}
                @endif
                @if($participant->institution)
                    ({{ $participant->institution }})
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Archivos Adjuntos -->
    @if($work->getMedia('evidencias') && $work->getMedia('evidencias')->count() > 0)
    <div class="section">
        <div class="section-title">{{ __('reports.pdf.attached_files') }} ({{ $work->getMedia('evidencias')->count() }})</div>
        <div class="files-list">
            @foreach($work->getMedia('evidencias') as $media)
            <div class="file-item">
                <strong>{{ $media->name }}</strong> ({{ number_format($media->size / 1024, 1) }} KB)
                @if($media->getCustomProperty('original_name'))
                    - {{ $media->getCustomProperty('original_name') }}
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Historial de Estados -->
    @if($work->statusHistory && $work->statusHistory->count() > 0)
    <div class="section">
        <div class="section-title">{{ __('reports.pdf.status_history') }}</div>
        <div class="status-history">
            @foreach($work->statusHistory->sortBy('created_at') as $history)
            <div class="status-item">
                <div class="status-date">
                    {{ $history->created_at->format('d/m/Y H:i') }} - {{ $history->status->name }}
                </div>
                @if($history->changedBy)
                <div class="status-user">
                    {{ __('reports.pdf.changed_by') }}: {{ $history->changedBy->name }}
                </div>
                @endif
                @if($history->comments)
                <div class="status-comments">
                    {{ $history->comments }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Información de Certificación -->
    @if($work->certification)
    <div class="section">
        <div class="section-title">{{ __('reports.pdf.certification_info') }}</div>
        <table>
            <tr>
                <td class="label">{{ __('reports.pdf.certification_number') }}</td>
                <td>{{ $work->certification->certification_number }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('reports.pdf.issue_date') }}</td>
                <td>{{ optional($work->certification->issue_date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('reports.pdf.valid_until') }}</td>
                <td>{{ optional($work->certification->valid_until)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('reports.pdf.issued_by') }}</td>
                <td>{{ $work->certification->issuedByUser?->name }}</td>
            </tr>
        </table>
        @if($work->certification->comments)
        <div class="highlight" style="margin-top: 10px;">
            <strong>{{ __('reports.pdf.certification_comments') }}:</strong><br>
            {{ $work->certification->comments }}
        </div>
        @endif
    </div>
    @endif

    <!-- Estadísticas VIEX -->
    @if($work->getViexStatistics())
    <div class="section">
        <div class="section-title">{{ __('reports.pdf.viex_statistics') }}</div>
        <table>
            @php $stats = $work->getViexStatistics(); @endphp
            @if($stats['received_at'])
            <tr>
                <td class="label">{{ __('reports.pdf.received_at_viex') }}</td>
                <td>{{ $stats['received_at']->format('d/m/Y H:i') }}</td>
            </tr>
            @endif
            @if($stats['evaluation_started_at'])
            <tr>
                <td class="label">{{ __('reports.pdf.evaluation_started') }}</td>
                <td>{{ $stats['evaluation_started_at']->format('d/m/Y H:i') }}</td>
            </tr>
            @endif
            @if($stats['completed_at'])
            <tr>
                <td class="label">{{ __('reports.pdf.completed_at') }}</td>
                <td>{{ $stats['completed_at']->format('d/m/Y H:i') }}</td>
            </tr>
            @endif
            @if($stats['days_in_viex'])
            <tr>
                <td class="label">{{ __('reports.pdf.days_in_viex') }}</td>
                <td>{{ $stats['days_in_viex'] }}</td>
            </tr>
            @endif
            @if($stats['is_overdue'])
            <tr>
                <td class="label">{{ __('reports.pdf.is_overdue') }}</td>
                <td style="color: #e53e3e;">{{ __('reports.pdf.yes') }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    <div class="footer">
        {{ __('reports.pdf.footer') }}<br>
        {{ __('reports.pdf.generated_by') }}: {{ Auth::user()->name }} - {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>