<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Trabajos Certificados - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }

        .header p {
            margin: 5px 0;
            color: #666;
        }

        .user-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .user-info h3 {
            margin-top: 0;
            color: #007bff;
        }

        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .summary-item {
            text-align: center;
            flex: 1;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin: 0 5px;
        }

        .summary-item .number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
        }

        .summary-item .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .work-details {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 3px;
        }

        .work-details h4 {
            margin: 0 0 10px 0;
            color: #007bff;
            font-size: 14px;
        }

        .work-details p {
            margin: 3px 0;
            font-size: 11px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 3px;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Universidad de Panamá</h1>
        <h2>Vicerrectoría de Extensión</h2>
        <p>Reporte de Trabajos de Extensión Certificados</p>
        <p>Generado el {{ $generated_at->format('d/m/Y H:i') }}</p>
    </div>

    <div class="user-info">
        <h3>Información del Profesor</h3>
        <p><strong>Nombre:</strong> {{ $user->name }}</p>
        <p><strong>Código:</strong> {{ $user->professor_code ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="number">{{ $total_works }}</div>
            <div class="label">Trabajos Certificados</div>
        </div>
    </div>

    @if($works->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Unidad Académica</th>
                    <th>Período</th>
                    <th>Certificación</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($works as $work)
                <tr>
                    <td>{{ $work->id }}</td>
                    <td>{{ $work->title }}</td>
                    <td>{{ $work->workType->name ?? 'N/A' }}</td>
                    <td>{{ $work->organizationalUnit->name ?? 'N/A' }}</td>
                    <td>
                        @if($work->start_date && $work->end_date)
                            {{ $work->start_date->format('d/m/Y') }} - {{ $work->end_date->format('d/m/Y') }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($work->certification)
                            <div><strong>N°:</strong> {{ $work->certification->certification_number }}</div>
                            <div><strong>Fecha:</strong> {{ $work->certification->created_at->format('d/m/Y') }}</div>
                        @else
                            Pendiente
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-success">{{ $work->currentStatus->name ?? 'N/A' }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="7">
                        <div class="work-details">
                            <h4>Detalles del Trabajo</h4>
                            <p><strong>Descripción:</strong> {{ Str::limit($work->description, 200) }}</p>
                            <p><strong>Período Académico:</strong> {{ $work->academic_period ?? 'N/A' }}</p>
                            <p><strong>Consentimiento de Publicación:</strong>
                                <span class="badge {{ $work->publication_consent ? 'badge-success' : 'badge-warning' }}">
                                    {{ $work->publication_consent ? 'Autorizado' : 'No autorizado' }}
                                </span>
                            </p>

                            {{-- Detalles específicos según tipo --}}
                            @if($work->workType->name === 'Proyecto' && $work->projectDetail)
                                <p><strong>Objetivos:</strong> {{ Str::limit($work->projectDetail->objectives, 150) }}</p>
                                <p><strong>Metodología:</strong> {{ Str::limit($work->projectDetail->methodology, 150) }}</p>
                            @elseif($work->workType->name === 'Actividad' && $work->activityDetail)
                                <p><strong>Tipo de Actividad:</strong> {{ $work->activityDetail->activity_type }}</p>
                                <p><strong>Modalidad:</strong> {{ $work->activityDetail->modality }}</p>
                            @elseif($work->workType->name === 'Publicación' && $work->publicationDetail)
                                <p><strong>Tipo de Publicación:</strong> {{ $work->publicationDetail->publication_type }}</p>
                            @elseif($work->workType->name === 'Asistencia Técnica' && $work->technicalAssistanceDetail)
                                <p><strong>Tipo de Asistencia:</strong> {{ $work->technicalAssistanceDetail->assistance_type }}</p>
                                <p><strong>Institución:</strong> {{ $work->technicalAssistanceDetail->collaborating_institution }}</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="text-align: center; padding: 50px; background-color: #f8f9fa; border-radius: 5px;">
            <h3>No se encontraron trabajos certificados</h3>
            <p>El profesor no tiene trabajos de extensión certificados en el sistema.</p>
        </div>
    @endif

    <div class="footer">
        <p>Reporte generado por el Sistema PRCTE - Vicerrectoría de Extensión</p>
        <p>Universidad de Panamá - {{ now()->format('Y') }}</p>
    </div>
</body>
</html>