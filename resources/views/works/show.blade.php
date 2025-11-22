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

                    {{-- Detalles específicos según tipo --}}
                    <div class="col-md-12 mt-4">
                        @php
                        $workTypeId = (int) $work->work_type_id;
                        @endphp
                        @switch($workTypeId)
                        @case(1)
                        @include('works.partials.show.project-details', ['project' => $work->projectDetail])
                        @break
                        @case(2)
                        @include('works.partials.show.activity-details', ['activity' => $work->activityDetail])
                        @break
                        @case(3)
                        @include('works.partials.show.publication-details', ['publication' => $work->publicationDetail])
                        @break
                        @case(4)
                        @include('works.partials.show.assistance-details', ['assistance' => $work->technicalAssistanceDetail])
                        @break
                        @endswitch
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

        {{-- Archivos Adjuntos --}}
        @php
        $attachments = $work->getMedia('evidencias');
        @endphp
        @if($attachments && $attachments->count() > 0)
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-paperclip"></i>
                    Documentos y Evidencias
                    <span class="badge badge-light ml-2">{{ $attachments->count() }}</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($attachments as $media)
                    <div class="col-md-6 mb-3">
                        <div class="card border-info">
                            <div class="card-body text-center p-3">
                                <div class="mb-2">
                                    @php
                                    $extension = pathinfo($media->name, PATHINFO_EXTENSION);
                                    $iconClass = match (strtolower($extension)) {
                                    'pdf' => 'fas fa-file-pdf text-danger fa-2x',
                                    'doc', 'docx' => 'fas fa-file-word text-primary fa-2x',
                                    'xls', 'xlsx' => 'fas fa-file-excel text-success fa-2x',
                                    'ppt', 'pptx' => 'fas fa-file-powerpoint text-warning fa-2x',
                                    'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-info fa-2x',
                                    'zip', 'rar' => 'fas fa-file-archive text-secondary fa-2x',
                                    default => 'fas fa-file text-muted fa-2x'
                                    };
                                    @endphp
                                    <i class="{{ $iconClass }}"></i>
                                </div>

                                <h6 class="card-title text-truncate" title="{{ $media->name }}">
                                    {{ $media->name }}
                                </h6>

                                <p class="card-text text-muted small mb-2">
                                    {{ number_format($media->size / 1024, 1) }} KB
                                </p>

                                <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Comentarios y Retroalimentación --}}
        @php
        $comments = $work->getCommentsAndFeedback();
        @endphp
        @if($comments && $comments->count() > 0)
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-comments"></i>
                    Comentarios y Retroalimentación
                    <span class="badge badge-light ml-2">{{ $comments->count() }}</span>
                </h3>
            </div>
            <div class="card-body">
                <div class="timeline timeline-inverse">
                    @foreach($comments as $comment)
                    <div class="time-label">
                        <span class="bg-info">
                            {{ $comment->created_at->format('d M Y') }}
                        </span>
                    </div>
                    <div>
                        @php
                        $commentIcon = match ($comment->status->name ?? '') {
                        'Rechazado por Coordinador', 'Rechazado por Decano/Director', 'Rechazado por VIEX' => 'fa-times-circle bg-danger',
                        'Devuelto para Corrección' => 'fa-exclamation-triangle bg-warning',
                        'Aprobado por Coordinador', 'Aprobado por Decano/Director' => 'fa-check-circle bg-success',
                        default => 'fa-comment bg-info'
                        };
                        @endphp
                        <i class="fas {{ $commentIcon }}"></i>
                        <div class="timeline-item">
                            <span class="time">
                                <i class="far fa-clock"></i>
                                {{ $comment->created_at->format('H:i') }}
                            </span>
                            <h3 class="timeline-header">
                                {{ $comment->status->name ?? 'Comentario' }}
                                @if($comment->changedBy)
                                <small class="text-muted">por {{ $comment->changedBy->name }}</small>
                                @endif
                            </h3>
                            <div class="timeline-body">
                                <div class="card border-left-primary">
                                    <div class="card-body py-2">
                                        <p class="mb-0">{{ $comment->comments }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
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
                    <form action="{{ route('works.submit', $work) }}" method="POST" id="submitWorkForm" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block mb-2" id="submitWorkBtn">
                            <i class="fas fa-paper-plane"></i>
                            Enviar para Revisión
                        </button>
                    </form>
                    @endif

                    {{-- Eliminar --}}
                    @can('delete', $work)
                    <form action="{{ route('works.destroy', $work) }}" method="POST" class="d-inline js-delete-work-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block mb-2">
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
                    <form action="{{ route('works.resubmit', $work) }}" method="POST" class="resubmit-work-form">
                        @csrf
                        @method('PATCH')
                        <button type="button" class="btn btn-success btn-block mb-2 js-resubmit-btn">
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
                    <form action="{{ route('works.resubmit', $work) }}" method="POST" class="resubmit-work-form">
                        @csrf
                        @method('PATCH')
                        <button type="button" class="btn btn-success btn-block mb-2 js-resubmit-btn">
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
                    <form action="{{ route('works.resubmit', $work) }}" method="POST" class="resubmit-work-form">
                        @csrf
                        @method('PATCH')
                        <button type="button" class="btn btn-success btn-block mb-2 js-resubmit-btn">
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
                    <form action="{{ route('works.resubmit', $work) }}" method="POST" class="d-inline resubmit-work-form">
                        @csrf
                        @method('PATCH')
                        <button type="button" class="btn btn-success btn-block mb-2 js-resubmit-btn">
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

                    {{-- Chat con Coordinadores y Evaluadores --}}
                    @php
                    $coordinators = \App\Models\User::role('coordinador_extension')
                        ->where('main_organizational_unit_id', $work->organizational_unit_id)
                        ->get();
                    $possibleRecipients = $coordinators->merge($work->evaluators ?? collect())->unique('id');
                    @endphp
                    @if($possibleRecipients->count() > 0)
                    <a href="{{ route('works.messages.show', $work) }}" class="btn btn-primary mb-2">
                        <i class="fas fa-comments"></i>
                        Chat con Revisores
                        @php
                        $unreadCount = $work->messages()
                        ->where('recipient_user_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
                        @endphp
                        @if($unreadCount > 0)
                        <span class="badge badge-light ml-1">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    @endif
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
                    <strong>Enviado:</strong> {{ optional($work->submitted_at)->format('d/m/Y H:i') }}<br>
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

        const submitWorkForm = $('#submitWorkForm');
        const submitWorkBtn = $('#submitWorkBtn');

        if (submitWorkForm.length && submitWorkBtn.length) {
            /**
             * @format-ignore
             */
            const missingFields = @json($work->getMissingFieldsForSubmission());

            const showSubmitConfirmation = () => {
                let title = '¿Enviar trabajo para revisión?';
                let html = '<p class="mb-2">Verifica que los campos obligatorios estén completos antes de enviar.</p>' +
                    '<p class="text-muted small mb-0">Mientras esté en revisión no podrás editarlo.</p>';
                let icon = 'question';
                let confirmButtonText = '<i class="fas fa-paper-plane"></i> Sí, enviar';
                let confirmButtonColor = '#007bff';

                if (missingFields.length > 0) {
                    title = 'Campos incompletos detectados';
                    html = '<p class="mb-2 text-warning"><strong>Advertencia:</strong> Los siguientes campos obligatorios están vacíos o incompletos:</p>' +
                        '<ul class="text-left mb-3" style="max-height: 150px; overflow-y: auto;">' +
                        missingFields.map(field => `<li><i class="fas fa-exclamation-triangle text-warning"></i> ${field}</li>`).join('') +
                        '</ul>' +
                        '<p class="text-muted small mb-0">¿Deseas enviar de todos modos? El sistema validará nuevamente antes de procesar.</p>';
                    icon = 'warning';
                    confirmButtonText = '<i class="fas fa-paper-plane"></i> Enviar de todos modos';
                    confirmButtonColor = '#fd7e14'; // Orange
                }

                Swal.fire({
                    title: title,
                    html: html,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: confirmButtonColor,
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        confirmButton: 'btn btn-lg',
                        cancelButton: 'btn btn-secondary btn-lg'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Enviando trabajo...',
                            html: 'Estamos remitiendo la solicitud al coordinador correspondiente.',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        submitWorkForm.data('confirming', true);
                        submitWorkForm.trigger('submit');
                    }
                });
            };

            submitWorkForm.on('submit', function(e) {
                if (!submitWorkForm.data('confirming')) {
                    e.preventDefault();
                    showSubmitConfirmation();
                } else {
                    submitWorkForm.removeData('confirming');
                }
            });

            submitWorkBtn.on('click', function(e) {
                e.preventDefault();
                showSubmitConfirmation();
            });
        }

        $('.js-delete-work-form').each(function() {
            const deleteForm = $(this);

            deleteForm.on('submit', function(e) {
                if (deleteForm.data('confirming')) {
                    deleteForm.removeData('confirming');
                    return;
                }

                e.preventDefault();

                Swal.fire({
                    title: '¿Eliminar este trabajo?',
                    html: '<p class="mb-2">Esta acción es irreversible y eliminará todo el historial asociado.</p>' +
                        '<p class="text-muted small mb-0">Asegúrate de haber respaldado la información necesaria.</p>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash"></i> Sí, eliminar',
                    cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        confirmButton: 'btn btn-danger btn-lg',
                        cancelButton: 'btn btn-secondary btn-lg'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Eliminando trabajo...',
                            html: 'Estamos removiendo el registro y sus asociaciones.',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        deleteForm.data('confirming', true);
                        deleteForm.trigger('submit');
                    }
                });
            });
        });

        $('.js-resubmit-btn').on('click', function(e) {
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: '¿Reenviar trabajo corregido?',
                html: '<p class="mb-2">Confirma que incorporaste <strong>todas las correcciones solicitadas</strong>.</p>' +
                    '<p class="text-muted small">El trabajo volverá al flujo de revisión correspondiente.</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-redo"></i> Sí, reenviar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn btn-success btn-lg',
                    cancelButton: 'btn btn-secondary btn-lg'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Reenviando trabajo...',
                        html: 'Por favor espere mientras actualizamos el registro.',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    form.trigger('submit');
                }
            });
        });
    });
</script>
@stop