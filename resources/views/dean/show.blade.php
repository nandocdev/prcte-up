@extends('adminlte::page')

@section('title', 'Revisar Trabajo - Decano/Director')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>Revisar Trabajo de Extensión</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
                <a href="{{ route('dean.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active">Revisar Trabajo</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Información Principal del Trabajo -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>
                    Información del Trabajo
                </h3>
                <div class="card-tools">
                    @if($work->currentStatus)
                    @php
                    $statusClass = match ($work->currentStatus->getAttribute('name')) {
                    'Enviado a Decano/Director' => 'badge-warning',
                    'Enviado a VIEX' => 'badge-success',
                    'Rechazado por Decano/Director' => 'badge-danger',
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
                <dl class="row">
                    <dt class="col-sm-3">Título:</dt>
                    <dd class="col-sm-9">{{ $work->getAttribute('title') }}</dd>

                    <dt class="col-sm-3">Tipo de Trabajo:</dt>
                    <dd class="col-sm-9">
                        <span class="badge badge-info">{{ $work->workType->name ?? 'N/A' }}</span>
                    </dd>

                    <dt class="col-sm-3">Descripción:</dt>
                    <dd class="col-sm-9">{{ $work->getAttribute('description') ?? 'No especificada' }}</dd>

                    <dt class="col-sm-3">Unidad Organizacional:</dt>
                    <dd class="col-sm-9">{{ $work->organizationalUnit->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Fecha de Inicio:</dt>
                    <dd class="col-sm-9">
                        {{ $work->getAttribute('start_date') ? \Carbon\Carbon::parse($work->getAttribute('start_date'))->format('d/m/Y') : 'N/A' }}
                    </dd>

                    <dt class="col-sm-3">Fecha de Fin:</dt>
                    <dd class="col-sm-9">
                        {{ $work->getAttribute('end_date') ? \Carbon\Carbon::parse($work->getAttribute('end_date'))->format('d/m/Y') : 'N/A' }}
                    </dd>

                    @if($work->getAttribute('objectives'))
                    <dt class="col-sm-3">Objetivos:</dt>
                    <dd class="col-sm-9">{{ $work->getAttribute('objectives') }}</dd>
                    @endif

                    @if($work->getAttribute('methodology'))
                    <dt class="col-sm-3">Metodología:</dt>
                    <dd class="col-sm-9">{{ $work->getAttribute('methodology') }}</dd>
                    @endif

                    @if($work->getAttribute('expected_results'))
                    <dt class="col-sm-3">Resultados Esperados:</dt>
                    <dd class="col-sm-9">{{ $work->getAttribute('expected_results') }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Información del Profesor Responsable -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-tie mr-2"></i>
                    Profesor Responsable
                </h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-3">Nombre:</dt>
                    <dd class="col-sm-9">{{ $work->responsibleUser->name ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Email:</dt>
                    <dd class="col-sm-9">{{ $work->responsibleUser->email ?? 'N/A' }}</dd>

                    <dt class="col-sm-3">Unidad:</dt>
                    <dd class="col-sm-9">{{ $work->organizationalUnit->name ?? 'N/A' }}</dd>
                </dl>
            </div>
        </div>

        <!-- Historial de Estados -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-2"></i>
                    Historial del Trabajo
                </h3>
            </div>
            <div class="card-body">
                @if($work->statusHistory && $work->statusHistory->count() > 0)
                <div class="timeline">
                    @foreach($work->statusHistory->sortByDesc('created_at') as $history)
                    <div class="time-label">
                        <span class="bg-primary">{{ $history->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        @php
                        $iconClass = match ($history->status->getAttribute('name')) {
                        'Enviado' => 'fas fa-paper-plane bg-info',
                        'En Revisión por Coordinador' => 'fas fa-search bg-warning',
                        'Avalado por Coordinador' => 'fas fa-thumbs-up bg-success',
                        'Enviado a Decano/Director' => 'fas fa-user-tie bg-warning',
                        'Enviado a VIEX' => 'fas fa-check-circle bg-success',
                        'Rechazado por Coordinador' => 'fas fa-times-circle bg-danger',
                        'Rechazado por Decano/Director' => 'fas fa-ban bg-danger',
                        'Certificado' => 'fas fa-certificate bg-primary',
                        default => 'fas fa-circle bg-secondary'
                        };
                        @endphp
                        <i class="{{ $iconClass }}"></i>
                        <div class="timeline-item">
                            <span class="time">
                                <i class="fas fa-clock"></i> {{ $history->created_at->format('H:i') }}
                            </span>
                            <h3 class="timeline-header">
                                <strong>{{ $history->status->getAttribute('name') }}</strong>
                            </h3>
                            @if($history->getAttribute('comments'))
                            <div class="timeline-body">
                                <p><strong>Observaciones:</strong></p>
                                <p>{{ $history->getAttribute('comments') }}</p>
                            </div>
                            @endif
                            @if($history->changedBy)
                            <div class="timeline-footer">
                                <small class="text-muted">
                                    Por: {{ $history->changedBy->name }}
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-history fa-3x mb-3"></i>
                    <p>No hay historial disponible para este trabajo.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Panel de Acciones -->
    <div class="col-md-4">
        <div class="card card-primary sticky-top">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cogs mr-2"></i>
                    Acciones de Decano/Director
                </h3>
            </div>
            <div class="card-body">
                @php
                $currentStatus = $work->currentStatus->name ?? '';
                @endphp

                @if(in_array($currentStatus, ['Enviado a Decano/Director', 'En Revisión Decano/Director']))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>Trabajo pendiente de revisión</strong><br>
                    Este trabajo ha sido avalado por el coordinador y está listo para su evaluación institucional.
                </div>

                <!-- Aprobar y Enviar a VIEX -->
                <div class="mb-3">
                    <button type="button" class="btn btn-success btn-block" data-toggle="modal"
                        data-target="#approveModal">
                        <i class="fas fa-check mr-2"></i>
                        Aprobar y Enviar a VIEX
                    </button>
                </div>

                <!-- Solicitar Cambios -->
                <div class="mb-3">
                    <button type="button" class="btn btn-warning btn-block" data-toggle="modal"
                        data-target="#changesModal">
                        <i class="fas fa-edit mr-2"></i>
                        Devolver para Corrección
                    </button>
                </div>

                <!-- Rechazar Definitivamente -->
                <div class="mb-3">
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal"
                        data-target="#rejectModal">
                        <i class="fas fa-times mr-2"></i>
                        Rechazar Trabajo
                    </button>
                </div>

                <hr>

                <small class="text-muted">
                    <i class="fas fa-lightbulb mr-1"></i>
                    <strong>Recuerde:</strong> Una vez aprobado, el trabajo será enviado automáticamente a VIEX para su
                    certificación final.
                </small>

                @elseif($currentStatus === 'Aprobado por Decano/Director')
                <div class="alert alert-success">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong>Trabajo aprobado exitosamente</strong><br>
                    Este trabajo ha sido enviado a VIEX para evaluación final.
                </div>

                @elseif($currentStatus === 'Rechazado por Decano/Director')
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle mr-2"></i>
                    <strong>Trabajo rechazado</strong><br>
                    Este trabajo fue rechazado y devuelto al profesor.
                </div>

                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>No requiere acción</strong><br>
                    Este trabajo ya fue procesado y no requiere acciones adicionales.
                </div>

                <p><strong>Estado actual:</strong>
                    <span class="badge badge-info">{{ $currentStatus }}</span>
                </p>
                @endif
            </div>

            <div class="card-footer">
                <a href="{{ route('dean.dashboard') }}" class="btn btn-default">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info mr-2"></i>
                    Información del Sistema
                </h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-6">Creado:</dt>
                    <dd class="col-6">{{ $work->created_at->format('d/m/Y H:i') }}</dd>

                    <dt class="col-6">Última actualización:</dt>
                    <dd class="col-6">{{ $work->updated_at->format('d/m/Y H:i') }}</dd>

                    @if($work->updated_at->diffInDays(now()) > 0)
                    <dt class="col-6">Días pendiente:</dt>
                    <dd class="col-6">
                        <span class="badge badge-warning">
                            {{ $work->updated_at->diffInDays(now()) }} días
                        </span>
                    </dd>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Aprobar -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('dean.approve', $work) }}">
                @csrf
                <div class="modal-header bg-success">
                    <h4 class="modal-title">
                        <i class="fas fa-check mr-2"></i>
                        Aprobar y Enviar a VIEX
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>¿Está seguro de que desea aprobar este trabajo?</strong></p>
                    <p class="text-muted">
                        Al aprobar, el trabajo será enviado automáticamente a VIEX para su certificación final.
                    </p>

                    <div class="form-group">
                        <label for="approve_comments">Observaciones (opcional):</label>
                        <textarea class="form-control" id="approve_comments" name="comments" rows="3"
                            placeholder="Ingrese cualquier observación adicional..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-2"></i>
                        Confirmar Aprobación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Solicitar Cambios -->
<div class="modal fade" id="changesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('dean.request-changes', $work) }}">
                @csrf
                <div class="modal-header bg-warning">
                    <h4 class="modal-title">
                        <i class="fas fa-edit mr-2"></i>
                        Devolver para Corrección
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Indique las correcciones requeridas:</strong></p>

                    <div class="form-group">
                        <label for="change_comments">Observaciones requeridas <span
                                class="text-danger">*</span>:</label>
                        <textarea class="form-control" id="change_comments" name="comments" rows="4" required
                            placeholder="Describa detalladamente los cambios que el profesor debe realizar..."></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Nota:</strong> El trabajo regresará al profesor para que realice las correcciones
                        solicitadas y lo reenvíe a través del coordinador.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-edit mr-2"></i>
                        Devolver para Corrección
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Rechazar Definitivamente -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="{{ route('dean.reject', $work) }}">
                @csrf
                <div class="modal-header bg-danger">
                    <h4 class="modal-title">
                        <i class="fas fa-times mr-2"></i>
                        Rechazar Trabajo Definitivamente
                    </h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Atención:</strong> Esta acción rechazará el trabajo de forma definitiva. El profesor
                        deberá iniciar un nuevo proceso si desea continuar.
                    </div>

                    <div class="form-group">
                        <label for="rejection_reason">Motivo del rechazo definitivo <span
                                class="text-danger">*</span>:</label>
                        <textarea class="form-control" id="rejection_reason" name="comments" rows="4" required
                            placeholder="Explique las razones por las cuales este trabajo no puede ser aprobado..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-2"></i>
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
    .sticky-top {
        top: 20px;
    }

    .timeline {
        position: relative;
        margin: 0 0 30px 0;
        padding: 0;
        list-style: none;
    }

    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 31px;
        width: 4px;
        background: #ddd;
    }

    .timeline>div {
        margin-bottom: 15px;
        position: relative;
    }

    .timeline>div>.timeline-item {
        box-shadow: 0 0 1px rgba(0, 0, 0, 0.125), 0 1px 3px rgba(0, 0, 0, 0.2);
        border-radius: 3px;
        margin-top: 10px;
        background: #fff;
        color: #444;
        margin-left: 60px;
        margin-right: 15px;
        padding: 10px;
        position: relative;
    }

    .timeline>div>.fa,
    .timeline>div>.fas,
    .timeline>div>.far,
    .timeline>div>.fab,
    .timeline>div>.fal,
    .timeline>div>.fad {
        position: absolute;
        left: 18px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        text-align: center;
        line-height: 30px;
        font-size: 15px;
    }

    .timeline>.time-label>span {
        font-weight: 600;
        color: #fff;
        border-radius: 4px;
        display: inline-block;
        padding: 5px 10px;
    }

    .timeline-header {
        margin-top: 0;
        color: #555;
    }

    .timeline-body,
    .timeline-footer {
        padding-top: 10px;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Validación del modal de cambios con SweetAlert
        $('#changesModal form').on('submit', function(e) {
            const comments = $('#change_comments').val().trim();
            if (comments.length < 10) {
                e.preventDefault();
                Swal.fire({
                    title: 'Observaciones insuficientes',
                    text: 'Por favor, describa las correcciones con al menos 10 caracteres para orientar al profesor.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#ffc107',
                }).then(() => {
                    $('#change_comments').focus();
                });
            }
        });

        // Confirmación para aprobar con SweetAlert
        $('#approveModal form').on('submit', function(e) {
            const $form = $(this);

            if ($form.data('sweetalert-confirmed')) {
                return true;
            }

            e.preventDefault();

            Swal.fire({
                title: '¿Aprobar y enviar a VIEX?',
                html: '<p class="mb-2">Esta acción enviará el trabajo a VIEX para su evaluación final.</p>' +
                    '<p class="text-muted small">Confirme solo si ya verificó toda la información.</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check"></i> Sí, aprobar',
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
                        title: 'Enviando a VIEX...',
                        html: 'Estamos registrando su aprobación. Esto puede tardar unos segundos.',
                        icon: 'info',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $form.data('sweetalert-confirmed', true);
                    $form.trigger('submit');
                }
            });
        });
    });
</script>
@stop