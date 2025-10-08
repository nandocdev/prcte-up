@extends('adminlte::page')

@section('title', 'Revisar Trabajo - Coordinador')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>Revisar Trabajo de Extensión</h1>
        <p class="text-muted">{{ $work->workType->name ?? 'N/A' }}</p>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Revisar Trabajo</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <!-- Información Principal del Trabajo -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-1"></i>
                    {{ $work->getAttribute('title') }}
                </h3>
                <div class="card-tools">
                    @if($work->currentStatus)
                        @php
    $statusClass = match ($work->currentStatus->getAttribute('name')) {
        'En Revisión Coordinador' => 'badge-warning',
        'Enviado a Decano/Director' => 'badge-success',
        'Devuelto para Corrección' => 'badge-danger',
        default => 'badge-secondary'
    };
                        @endphp
                        <span class="badge {{ $statusClass }}">
                            {{ $work->currentStatus->getAttribute('name') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Profesor Responsable:</strong>
                        <p>{{ $work->responsibleUser->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha de Envío:</strong>
                        <p>{{ $work->getAttribute('submitted_at')?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Duración:</strong>
                        <p>
                            Del {{ $work->getAttribute('start_date')?->format('d/m/Y') ?? 'N/A' }}
                            al {{ $work->getAttribute('end_date')?->format('d/m/Y') ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <strong>Participantes:</strong>
                        <p>{{ $work->getAttribute('participants_count') ?? 0 }} participantes</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <strong>Descripción:</strong>
                        <p>{{ $work->getAttribute('description') ?? 'Sin descripción disponible' }}</p>
                    </div>
                </div>

                @if($work->getAttribute('objectives'))
                    <div class="row">
                        <div class="col-12">
                            <strong>Objetivos:</strong>
                            <p>{{ $work->getAttribute('objectives') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detalles Específicos por Tipo -->
        @if($work->projectDetails)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-project-diagram mr-1"></i>
                        Detalles del Proyecto
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Tipo de Proyecto:</strong>
                            <p>{{ $work->projectDetails->getAttribute('project_type') ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Área de Conocimiento:</strong>
                            <p>{{ $work->projectDetails->getAttribute('knowledge_area') ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($work->projectDetails->getAttribute('justification'))
                        <div class="row">
                            <div class="col-12">
                                <strong>Justificación:</strong>
                                <p>{{ $work->projectDetails->getAttribute('justification') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($work->projectDetails->getAttribute('beneficiaries'))
                        <div class="row">
                            <div class="col-12">
                                <strong>Beneficiarios:</strong>
                                <p>{{ $work->projectDetails->getAttribute('beneficiaries') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Archivos y Evidencias -->
        @if($work->getMedia('evidencias')->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paperclip mr-1"></i>
                        Evidencias y Documentos ({{ $work->getMedia('evidencias')->count() }})
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($work->getMedia('evidencias') as $media)
                            <div class="col-md-6 mb-3">
                                <div class="card card-outline card-info">
                                    <div class="card-body p-2">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                @php
        $extension = pathinfo($media->name, PATHINFO_EXTENSION);
        $iconClass = match (strtolower($extension)) {
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc', 'docx' => 'fas fa-file-word text-primary',
            'xls', 'xlsx' => 'fas fa-file-excel text-success',
            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-warning',
            default => 'fas fa-file text-secondary'
        };
                                                @endphp
                                                <i class="{{ $iconClass }} fa-2x"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 font-weight-bold">{{ $media->name }}</p>
                                                <small class="text-muted">
                                                    {{ number_format($media->size / 1024, 1) }} KB
                                                </small>
                                            </div>
                                            <div>
                                                <a href="{{ $media->getUrl() }}" target="_blank"
                                                    class="btn btn-sm btn-outline-primary">
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
        @endif
    </div>

    <!-- Panel de Acciones -->
    <div class="col-md-4">
        <!-- Estado y Acciones -->
        @can('coordinate_extension_works')
            @php
    $currentStatus = $work->currentStatus->name ?? '';
            @endphp

            @if($currentStatus === 'Enviado a Coordinador')
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-tasks mr-1"></i>
                            Acciones de Coordinación
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Trabajo pendiente de revisión</strong><br>
                            Este trabajo está esperando su revisión como coordinador de extensión.
                        </div>

                        <!-- Aprobar trabajo -->
                        <form action="{{ route('coordinator.approve', $work) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="form-group">
                                <label for="approval_comments">Comentarios de aprobación (opcional):</label>
                                <textarea name="comments" id="approval_comments" class="form-control" rows="3"
                                    placeholder="Escriba comentarios sobre la aprobación..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block"
                                onclick="return confirm('¿Está seguro de que desea aprobar este trabajo y enviarlo al Decano/Director?')">
                                <i class="fas fa-check mr-1"></i>
                                Aprobar y Enviar al Decano/Director
                            </button>
                        </form>

                        <hr>

                        <!-- Solicitar cambios -->
                        <button type="button" class="btn btn-warning btn-block" data-toggle="modal"
                            data-target="#requestChangesModal">
                            <i class="fas fa-edit mr-1"></i>
                            Solicitar Subsanaciones
                        </button>

                        <!-- Rechazar trabajo -->
                        <button type="button" class="btn btn-danger btn-block mt-2" data-toggle="modal"
                            data-target="#rejectModal">
                            <i class="fas fa-times mr-1"></i>
                            Rechazar Trabajo
                        </button>
                    </div>
                </div>

            @elseif($currentStatus === 'En Revisión Coordinador')
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-search mr-1"></i>
                            Estado de Revisión
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-clock"></i>
                            <strong>En revisión</strong><br>
                            Este trabajo está siendo procesado por usted.
                        </div>
                        <!-- Mismas acciones que arriba -->
                        <form action="{{ route('coordinator.approve', $work) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="form-group">
                                <label for="approval_comments2">Comentarios de aprobación (opcional):</label>
                                <textarea name="comments" id="approval_comments2" class="form-control" rows="3"
                                    placeholder="Escriba comentarios sobre la aprobación..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block"
                                onclick="return confirm('¿Está seguro de que desea aprobar este trabajo y enviarlo al Decano/Director?')">
                                <i class="fas fa-check mr-1"></i>
                                Aprobar y Enviar al Decano/Director
                            </button>
                        </form>

                        <hr>

                        <button type="button" class="btn btn-warning btn-block" data-toggle="modal"
                            data-target="#requestChangesModal">
                            <i class="fas fa-edit mr-1"></i>
                            Solicitar Subsanaciones
                        </button>

                        <button type="button" class="btn btn-danger btn-block mt-2" data-toggle="modal"
                            data-target="#rejectModal">
                            <i class="fas fa-times mr-1"></i>
                            Rechazar Trabajo
                        </button>
                    </div>
                </div>

            @else
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle mr-1"></i>
                            Estado del Trabajo
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light">
                            <i class="fas fa-check-circle text-success"></i>
                            <strong>Trabajo procesado</strong><br>
                            Este trabajo ya ha sido procesado y no requiere acciones adicionales de su parte.
                        </div>

                        <p><strong>Estado actual:</strong>                    <span class="badge badge-info">{{ $currentStatus }}</span>
                        </p>

                        @if($work->statusHistory->where('status.name', 'Aprobado por Coordinador')->isNotEmpty())
                            <p class="text-success">
                                <i class="fas fa-check"></i> Aprobado por usted el                    {{ $work->statusHistory->where('status.name', 'Aprobado por Coordinador')->first()->created_at->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        @endcan

        <!-- Historial de Estados -->
        @if($work->statusHistory->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-1"></i>
                        Historial del Trabajo
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="timeline">
                        @foreach($work->statusHistory->sortByDesc('created_at') as $history)
                            <div class="time-label">
                                <span class="bg-blue">{{ $history->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-circle bg-{{ $loop->first ? 'success' : 'secondary' }}"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $history->created_at->format('H:i') }}
                                    </span>
                                    <h3 class="timeline-header">
                                        {{ $history->status->name ?? 'Estado desconocido' }}
                                    </h3>
                                    @if($history->comments)
                                        <div class="timeline-body">
                                            {{ $history->comments }}
                                        </div>
                                    @endif
                                    @if($history->changed_by_user_id)
                                        <div class="timeline-footer">
                                            <small class="text-muted">
                                                Por: {{ $history->changedBy->name ?? 'Usuario desconocido' }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal para Solicitar Cambios -->
<div class="modal fade" id="requestChangesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('coordinator.request-changes', $work) }}" method="POST">
                @csrf
                <div class="modal-header bg-warning">
                    <h4 class="modal-title">
                        <i class="fas fa-edit mr-1"></i>
                        Solicitar Subsanaciones
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="change_comments">Comentarios sobre las subsanaciones requeridas: *</label>
                        <textarea name="comments" id="change_comments" class="form-control" rows="4" required
                            placeholder="Especifique claramente qué aspectos del trabajo deben ser mejorados o corregidos..."></textarea>
                        <small class="form-text text-muted">
                            Proporcione comentarios claros y específicos para que el profesor pueda realizar las
                            correcciones necesarias.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-edit mr-1"></i>
                        Enviar Solicitud de Subsanaciones
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Rechazar Trabajo -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('coordinator.reject', $work) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger">
                    <h4 class="modal-title">
                        <i class="fas fa-times mr-1"></i>
                        Rechazar Trabajo
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atención:</strong> Al rechazar este trabajo, será devuelto al profesor y deberá realizar
                        las correcciones necesarias antes de poder reenviarlo.
                    </div>
                    <div class="form-group">
                        <label for="rejection_reason">Motivo del rechazo: *</label>
                        <textarea name="comments" id="rejection_reason" class="form-control" rows="4" required
                            placeholder="Explique claramente las razones por las cuales se rechaza este trabajo..."></textarea>
                        <small class="form-text text-muted">
                            Sea específico sobre los problemas encontrados para que el profesor pueda corregirlos
                            adecuadamente.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-1"></i>
                        Confirmar Rechazo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .timeline {
        margin: 0;
        padding: 0;
    }

    .timeline-item {
        background: #fff;
        border: 1px solid #ddd;
        margin: 10px 0;
        padding: 10px;
        border-radius: 4px;
    }

    .timeline-header {
        font-size: 1rem;
        margin: 0 0 5px 0;
    }

    .card-outline {
        border-top: 3px solid #17a2b8;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // Auto-expandir textareas
        $('textarea').on('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Confirmar acciones críticas
        $('form').on('submit', function (e) {
            if ($(this).find('button[type="submit"]').hasClass('btn-warning') ||
                $(this).find('button[type="submit"]').hasClass('btn-success')) {
                if (!confirm('¿Está seguro de realizar esta acción?')) {
                    e.preventDefault();
                }
            }
        });
    });
</script>
@stop
