@extends('adminlte::page')

@section('title', 'Detalle del Trabajo - VIEX')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fas fa-eye text-primary"></i>
            Detalle del Trabajo
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('works.index') }}">Trabajos</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($work->title, 30) }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="row">
    {{-- Información Principal --}}
    <div class="col-lg-8">
        {{-- Detalles del Trabajo --}}
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información del Trabajo
                </h3>
                <div class="card-tools">
                    {{-- Estado Actual --}}
                    @php
                    $statusColors = [
                    'Borrador' => 'secondary',
                    'Enviado a Coordinador' => 'warning',
                    'En Revisión Coordinador' => 'info',
                    'Enviado a Decano' => 'primary',
                    'En Revisión Decano' => 'primary',
                    'Enviado a VIEX' => 'dark',
                    'En Evaluación VIEX' => 'dark',
                    'Certificado' => 'success',
                    'Rechazado' => 'danger',
                    'Subsanar' => 'orange'
                    ];
                    $statusColor = $statusColors[$work->currentStatus->name ?? 'Borrador'] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $statusColor }} badge-lg">
                        <i class="fas fa-circle"></i>
                        {{ $work->currentStatus->name ?? 'Borrador' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Título --}}
                    <div class="col-md-12 mb-3">
                        <h4 class="text-primary mb-2">
                            <i class="fas fa-heading"></i>
                            {{ $work->title }}
                        </h4>
                    </div>

                    {{-- Información Básica --}}
                    <div class="col-md-6">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-primary">
                                <i class="fas fa-tag"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Tipo de Trabajo</span>
                                <span class="info-box-number">{{ $work->workType->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-building"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Unidad Académica</span>
                                <span class="info-box-number">{{ $work->organizationalUnit->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-success">
                                <i class="fas fa-user"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Responsable Principal</span>
                                <span class="info-box-number">{{ $work->responsibleUser->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box bg-light">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">Período Académico</span>
                                <span class="info-box-number">{{ $work->academic_period ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div class="col-md-12 mt-3">
                        <div class="row">
                            <div class="col-md-4">
                                <strong><i class="fas fa-play-circle text-success"></i> Fecha Inicio:</strong><br>
                                <span
                                    class="text-muted">{{ $work->start_date ? $work->start_date->format('d/m/Y') : 'No definida' }}</span>
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-stop-circle text-danger"></i> Fecha Fin:</strong><br>
                                <span
                                    class="text-muted">{{ $work->end_date ? $work->end_date->format('d/m/Y') : 'No definida' }}</span>
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-clock text-info"></i> Duración:</strong><br>
                                <span class="text-muted">
                                    @if($work->start_date && $work->end_date)
                                    {{ $work->start_date->diffInDays($work->end_date) + 1 }} días
                                    @else
                                    No calculable
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    <div class="col-md-12 mt-4">
                        <h5><i class="fas fa-align-left text-primary"></i> Descripción del Trabajo</h5>
                        <div class="card">
                            <div class="card-body">
                                <p class="text-justify">{{ $work->description ?? 'Sin descripción disponible.' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Consentimiento de Publicación --}}
                    <div class="col-md-12 mt-3">
                        <div class="alert {{ $work->publication_consent ? 'alert-success' : 'alert-warning' }}">
                            <i
                                class="fas {{ $work->publication_consent ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
                            <strong>Consentimiento de Publicación:</strong>
                            {{ $work->publication_consent ? 'AUTORIZADO para publicación' : 'NO autorizado para publicación' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Participantes --}}
        @if($work->participants && $work->participants->count() > 0)
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users"></i>
                    Participantes del Trabajo
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Rol</th>
                                <th>Unidad</th>
                                <th>Contacto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($work->participants as $participant)
                            <tr>
                                <td>
                                    <i class="fas fa-user text-primary"></i>
                                    {{ $participant->user->name ?? 'N/A' }}
                                </td>
                                <td>
                                    <span
                                        class="badge badge-secondary">{{ $participant->role ?? 'Participante' }}</span>
                                </td>
                                <td>{{ $participant->user->organizationalUnit->name ?? 'N/A' }}</td>
                                <td>{{ $participant->user->email ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Documentos Adjuntos --}}
        @if($work->media && $work->media->count() > 0)
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-paperclip"></i>
                    Documentos Adjuntos
                    <span class="badge badge-light ml-2">{{ $work->media->count() }}</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($work->media as $document)
                    <div class="col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        @php
                                        $extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
                                        $iconClass = match (strtolower($extension)) {
                                        'pdf' => 'fa-file-pdf text-danger',
                                        'doc', 'docx' => 'fa-file-word text-primary',
                                        'xls', 'xlsx' => 'fa-file-excel text-success',
                                        'jpg', 'jpeg', 'png', 'gif' => 'fa-file-image text-warning',
                                        default => 'fa-file text-secondary'
                                        };
                                        @endphp
                                        <i class="fas {{ $iconClass }} fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $document->name }}</h6>
                                        <small class="text-muted">
                                            {{ number_format($document->size / 1024, 2) }} KB •
                                            {{ $document->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="ml-2">
                                        <a href="{{ $document->getUrl() }}" class="btn btn-sm btn-outline-primary"
                                            target="_blank" title="Descargar archivo">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div class="card card-secondary">
            <div class="card-body text-center text-muted">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <h5>No hay documentos adjuntos</h5>
                <p>Este trabajo aún no tiene documentos de soporte adjuntos.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Panel Lateral --}}
    <div class="col-lg-4">
        {{-- Acciones Rápidas --}}
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs"></i>
                    Acciones
                </h3>
            </div>
            <div class="card-body">
                <div class="btn-group-vertical btn-block">
                    {{-- Acciones según el estado actual --}}
                    @php
                    $currentStatus = $work->currentStatus->name ?? 'Borrador';
                    @endphp

                    {{-- Estado: Borrador --}}
                    @if($currentStatus === 'Borrador')
                    {{-- Editar --}}
                    <a href="{{ route('works.edit', $work) }}" class="btn btn-warning mb-2">
                        <i class="fas fa-edit"></i>
                        Editar Trabajo
                    </a>

                    {{-- Enviar para Revisión --}}
                    @if($work->title && $work->work_type_id)
                    <form action="{{ route('works.submit', $work) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-block mb-2"
                            onclick="return confirm('¿Está seguro de enviar este trabajo para revisión? Una vez enviado no podrá editarlo.')">
                            <i class="fas fa-paper-plane"></i>
                            Enviar para Revisión
                        </button>
                    </form>
                    @endif

                    {{-- Eliminar --}}
                    @can('delete', $work)
                    <form action="{{ route('works.destroy', $work) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block mb-2"
                            onclick="return confirm('¿Está seguro de eliminar este trabajo? Esta acción no se puede deshacer.')">
                            <i class="fas fa-trash"></i>
                            Eliminar Trabajo
                        </button>
                    </form>
                    @endcan

                    @endif

                    {{-- Autorización de Publicación (disponible en cualquier estado para el profesor responsable) --}}
                    @can('update', $work)
                    <hr class="my-3">
                    <div class="mb-2">
                        <h6 class="text-muted mb-2">
                            <i class="fas fa-book-open"></i>
                            Autorización de Publicación
                        </h6>
                        <p class="small text-muted mb-3">
                            {{ __('Autoriza a VIEX a publicar los resultados de este trabajo en medios institucionales y académicos.') }}
                        </p>

                        @if($work->publication_consent)
                        {{-- Mostrar estado autorizado y opción de revocar --}}
                        <div class="alert alert-success py-2 px-3 mb-2">
                            <i class="fas fa-check-circle"></i>
                            <strong>Publicación Autorizada</strong>
                            <br>
                            <small>Has autorizado la publicación de este trabajo.</small>
                        </div>
                        <form action="{{ route('works.authorize-publication', $work) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="authorized" value="0">
                            <button type="submit" class="btn btn-outline-warning btn-block btn-sm"
                                onclick="return confirm('¿Está seguro de revocar la autorización de publicación? VIEX será notificado del cambio.')">
                                <i class="fas fa-times-circle"></i>
                                Revocar Autorización
                            </button>
                        </form>
                        @else
                        {{-- Mostrar estado no autorizado y opción de autorizar --}}
                        <div class="alert alert-info py-2 px-3 mb-2">
                            <i class="fas fa-info-circle"></i>
                            <small>Aún no has autorizado la publicación de este trabajo.</small>
                        </div>
                        <form action="{{ route('works.authorize-publication', $work) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="authorized" value="1">
                            <button type="submit" class="btn btn-success btn-block btn-sm"
                                onclick="return confirm('¿Autoriza a VIEX a publicar los resultados de este trabajo en medios académicos e institucionales?')">
                                <i class="fas fa-check-circle"></i>
                                Autorizar Publicación
                            </button>
                        </form>
                        @endif
                    </div>
                    @endcan

                    @if($currentStatus !== 'Borrador')
                    {{-- Aquí continúan los demás estados --}}

                    {{-- Estado: Rechazado por Coordinador --}}
                    @if($currentStatus === 'Rechazado por Coordinador')
                    @php
                    $lastRejection = $work->statusHistory
                    ->where('status.name', 'Rechazado por Coordinador')
                    ->sortByDesc('created_at')
                    ->first();
                    @endphp

                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Trabajo rechazado por el Coordinador</strong><br>
                        Debe realizar las correcciones solicitadas.

                        @if($lastRejection && $lastRejection->comments)
                        <hr class="my-2">
                        <strong><i class="fas fa-comment-dots"></i> Motivo del rechazo:</strong>
                        <p class="mb-2 mt-1">{{ $lastRejection->comments }}</p>
                        <small class="text-muted">
                            <i class="fas fa-user"></i> Por: {{ $lastRejection->changedBy->name ?? 'Sistema' }} ·
                            <i class="fas fa-clock"></i> {{ $lastRejection->created_at->diffForHumans() }}
                        </small>
                        @endif
                    </div>

                    <a href="{{ route('works.edit', $work) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit"></i>
                        Realizar Correcciones
                    </a>

                    {{-- Botón de reenvío después de correcciones --}}
                    <form action="{{ route('works.resubmit', $work) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-block mb-2"
                            onclick="return confirm('¿Ha realizado todas las correcciones solicitadas? El trabajo será reenviado para revisión.')">
                            <i class="fas fa-redo"></i>
                            Reenviar Trabajo Corregido
                        </button>
                    </form>

                    {{-- Estado: Rechazado por Decano/Director --}}
                    @elseif($currentStatus === 'Rechazado por Decano/Director')
                    @php
                    $lastRejection = $work->statusHistory
                    ->where('status.name', 'Rechazado por Decano/Director')
                    ->sortByDesc('created_at')
                    ->first();
                    @endphp

                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Trabajo rechazado por el Decano/Director</strong><br>
                        Debe realizar las correcciones solicitadas.

                        @if($lastRejection && $lastRejection->comments)
                        <hr class="my-2">
                        <strong><i class="fas fa-comment-dots"></i> Motivo del rechazo:</strong>
                        <p class="mb-2 mt-1">{{ $lastRejection->comments }}</p>
                        <small class="text-muted">
                            <i class="fas fa-user"></i> Por: {{ $lastRejection->changedBy->name ?? 'Sistema' }} ·
                            <i class="fas fa-clock"></i> {{ $lastRejection->created_at->diffForHumans() }}
                        </small>
                        @endif
                    </div>

                    <a href="{{ route('works.edit', $work) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit"></i>
                        Realizar Correcciones
                    </a>

                    {{-- Botón de reenvío después de correcciones --}}
                    <form action="{{ route('works.resubmit', $work) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-block mb-2"
                            onclick="return confirm('¿Ha realizado todas las correcciones solicitadas? El trabajo será reenviado para revisión.')">
                            <i class="fas fa-redo"></i>
                            Reenviar Trabajo Corregido
                        </button>
                    </form>

                    {{-- Estado: Rechazado por VIEX --}}
                    @elseif($currentStatus === 'Rechazado por VIEX')
                    @php
                    $lastRejection = $work->statusHistory
                    ->where('status.name', 'Rechazado por VIEX')
                    ->sortByDesc('created_at')
                    ->first();
                    @endphp

                    <div class="alert alert-danger mb-3">
                        <i class="fas fa-times-circle"></i>
                        <strong>Trabajo rechazado por VIEX</strong><br>
                        Debe realizar las correcciones solicitadas.

                        @if($lastRejection && $lastRejection->comments)
                        <hr class="my-2">
                        <strong><i class="fas fa-comment-dots"></i> Motivo del rechazo:</strong>
                        <p class="mb-2 mt-1">{{ $lastRejection->comments }}</p>
                        <small class="text-muted">
                            <i class="fas fa-user"></i> Por: {{ $lastRejection->changedBy->name ?? 'Sistema' }} ·
                            <i class="fas fa-clock"></i> {{ $lastRejection->created_at->diffForHumans() }}
                        </small>
                        @endif
                    </div>

                    <a href="{{ route('works.edit', $work) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit"></i>
                        Realizar Correcciones
                    </a>

                    {{-- Botón de reenvío después de correcciones --}}
                    <form action="{{ route('works.resubmit', $work) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-block mb-2"
                            onclick="return confirm('¿Ha realizado todas las correcciones solicitadas? El trabajo será reenviado para revisión.')">
                            <i class="fas fa-redo"></i>
                            Reenviar Trabajo Corregido
                        </button>
                    </form>

                    {{-- Estado: Devuelto para Corrección --}}
                    @elseif($currentStatus === 'Devuelto para Corrección')
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Trabajo devuelto para corrección</strong><br>
                        Realice las modificaciones solicitadas y reenvíe.
                    </div>

                    <a href="{{ route('works.edit', $work) }}" class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit"></i>
                        Realizar Correcciones
                    </a>

                    {{-- Reenviar después de correcciones --}}
                    <form action="{{ route('works.resubmit', $work) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success btn-block mb-2"
                            onclick="return confirm('¿Ha realizado todas las correcciones solicitadas? El trabajo será reenviado para revisión.')">
                            <i class="fas fa-redo"></i>
                            Reenviar Trabajo Corregido
                        </button>
                    </form>

                    {{-- Estado: Certificado --}}
                    @elseif($currentStatus === 'Certificado')
                    <div class="alert alert-success mb-3">
                        <i class="fas fa-certificate"></i>
                        <strong>¡Felicitaciones!</strong><br>
                        Su trabajo ha sido certificado oficialmente.
                    </div>

                    @if($work->certification)
                    <a href="{{ route('certificates.download', $work->certification) }}"
                        class="btn btn-success btn-block mb-2">
                        <i class="fas fa-download"></i>
                        Descargar Certificado
                    </a>
                    @endif

                    {{-- Estados de revisión (solo visualización) --}}
                    @elseif(in_array($currentStatus, [
                    'Enviado a Coordinador',
                    'En Revisión Coordinador',
                    'Aprobado por Coordinador',
                    'Enviado a Decano/Director',
                    'En Revisión Decano/Director',
                    'Aprobado por Decano/Director',
                    'Enviado a VIEX',
                    'En VIEX - Pendiente Asignación',
                    'En VIEX - En Evaluación',
                    'En VIEX - Aprobado'
                    ]))
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-clock"></i>
                        <strong>Trabajo en proceso</strong><br>
                        Su trabajo está siendo revisado. Será notificado de cualquier actualización.
                    </div>
                    @endif

                    @endif {{-- Cierre de @if($currentStatus !== 'Borrador') --}}

                    {{-- Duplicar (siempre disponible) --}}
                    <hr>
                    <a href="{{ route('works.create') }}?duplicate={{ $work->id }}" class="btn btn-info mb-2"
                        title="Crear nuevo trabajo basado en este">
                        <i class="fas fa-copy"></i>
                        Duplicar Trabajo
                    </a>
                </div>

                {{-- Volver --}}
                <hr>
                <a href="{{ route('works.index') }}" class="btn btn-secondary btn-block">
                    <i class="fas fa-arrow-left"></i>
                    Volver al Listado
                </a>
            </div>
        </div>

        {{-- Timeline de Estados --}}
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history"></i>
                    Historial de Estados
                </h3>
            </div>
            <div class="card-body">
                @if($timeline && $timeline->count() > 0)
                <div class="timeline timeline-inverse">
                    @foreach($timeline as $history)
                    <div class="time-label">
                        <span class="bg-primary">
                            {{ $history->created_at->format('d M Y') }}
                        </span>
                    </div>
                    <div>
                        @php
                        $iconClass = match ($history->status->name ?? '') {
                        'Borrador' => 'fa-pencil-alt bg-secondary',
                        'Enviado a Coordinador' => 'fa-paper-plane bg-warning',
                        'En Revisión Coordinador' => 'fa-search bg-info',
                        'Enviado a Decano' => 'fa-level-up-alt bg-primary',
                        'En Revisión Decano' => 'fa-user-tie bg-primary',
                        'Enviado a VIEX' => 'fa-university bg-dark',
                        'En Evaluación VIEX' => 'fa-clipboard-check bg-dark',
                        'Certificado' => 'fa-certificate bg-success',
                        'Rechazado' => 'fa-times-circle bg-danger',
                        'Subsanar' => 'fa-exclamation-triangle bg-orange',
                        default => 'fa-circle bg-secondary'
                        };
                        @endphp
                        <i class="fas {{ $iconClass }}"></i>
                        <div class="timeline-item">
                            <span class="time">
                                <i class="far fa-clock"></i>
                                {{ $history->created_at->format('H:i') }}
                            </span>
                            <h3 class="timeline-header">{{ $history->status->name ?? 'Estado Desconocido' }}</h3>
                            <div class="timeline-body">
                                @if($history->comments)
                                <p>{{ $history->comments }}</p>
                                @endif
                                <small class="text-muted">
                                    Por: {{ $history->changedBy->name ?? 'Sistema' }}
                                </small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div>
                        <i class="far fa-clock bg-gray"></i>
                    </div>
                </div>
                @else
                <div class="text-center text-muted">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <p>Sin historial de cambios disponible</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Información Adicional --}}
        <div class="card card-light">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información del Sistema
                </h3>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>ID del Trabajo:</strong> #{{ $work->id }}<br>
                    <strong>Creado:</strong> {{ $work->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Última Modificación:</strong> {{ $work->updated_at->format('d/m/Y H:i') }}<br>
                    @if($work->submitted_at)
                    <strong>Enviado:</strong> {{ $work->submitted_at->format('d/m/Y H:i') }}<br>
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .info-box {
        min-height: 90px;
    }

    .info-box-number {
        font-size: 1rem !important;
        font-weight: 600;
    }

    .timeline>div>.timeline-item {
        background: #fff;
        border-radius: 3px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border-left: 3px solid #007bff;
    }

    .timeline-header {
        border-bottom: 1px solid #f4f4f4;
        color: #555;
        font-size: 16px;
        font-weight: 600;
        line-height: 1.1;
        margin: 0 0 10px 0;
        padding: 0 0 5px 0;
    }

    .timeline-body {
        padding: 5px 0 0 0;
    }

    .bg-orange {
        background-color: #fd7e14 !important;
    }

    .card-header .badge-lg {
        font-size: 0.9rem;
        padding: 8px 12px;
    }

    .btn-group-vertical .btn {
        border-radius: 0.25rem !important;
    }

    .text-justify {
        text-align: justify;
    }

    @media (max-width: 768px) {
        .info-box {
            min-height: 70px;
        }

        .btn-group-vertical .btn {
            margin-bottom: 5px !important;
        }

        .timeline {
            margin-left: 10px;
        }
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Tooltips
        $('[title]').tooltip();

        // Confirmación para acciones críticas
        $('form[action*="destroy"], form[action*="submit"]').on('submit', function(e) {
            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

            // Re-habilitar después de 3 segundos por si hay error
            setTimeout(() => {
                btn.prop('disabled', false).html(btn.data('original-text') || btn.html());
            }, 3000);
        });

        // Copiar ID al hacer clic
        $('.card-light').on('click', function() {
            const workId = '{{ $work->id }}';
            if (navigator.clipboard) {
                navigator.clipboard.writeText(workId).then(() => {
                    toastr?.success('ID copiado al portapapeles');
                });
            }
        });

        // Mostrar detalles completos de archivos al hacer hover
        $('[data-toggle="popover"]').popover();
    });
</script>
@stop