@extends('layouts.app')

@section('title', 'Trabajo de Extensión - VIEX')

@push('styles')
<style>
    .timeline-item {
        border-left: 3px solid #007bff;
        margin-left: 20px;
        padding-left: 20px;
        position: relative;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 10px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #007bff;
    }

    .evaluation-panel {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .evaluator-card {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 10px;
    }
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('Trabajo de Extensión') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
                    <li class="breadcrumb-item"><a
                            href="{{ route('viex.dashboard') }}">{{ __('Dashboard VIEX') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Trabajo #:id', ['id' => $work->getAttribute('id')]) }}
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Información Principal -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $work->title }}</h3>
                        <div class="card-tools">
                            @php
                            $statusColors = [
                            'En VIEX - Pendiente Asignación' => 'warning',
                            'En VIEX - En Evaluación' => 'info',
                            'En VIEX - Aprobado' => 'success',
                            'Certificado' => 'primary',
                            'Rechazado por VIEX' => 'danger',
                            ];
                            $color = $statusColors[$work->currentStatus->getAttribute('name')] ?? 'secondary';
                            @endphp
                            <span
                                class="badge badge-{{ $color }} badge-lg">{{ $work->currentStatus->getAttribute('name') }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>{{ __('Tipo de Trabajo:') }}</strong>
                                <p>{{ $work->workType->name }}</p>

                                <strong>{{ __('Profesor Responsable:') }}</strong>
                                <p>{{ $work->primaryResponsible->getAttribute('name') }}</p>

                                <strong>{{ __('Código de Profesor:') }}</strong>
                                <p>{{ $work->primaryResponsible->getAttribute('professor_code') }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>{{ __('Unidad Académica:') }}</strong>
                                <p>{{ $work->organizationalUnit->getAttribute('name') }}</p>

                                <strong>{{ __('Período de Ejecución:') }}</strong>
                                <p>
                                    @if($work->start_date && $work->end_date)
                                    {{ $work->start_date->format('d/m/Y') }} -
                                    {{ $work->end_date->format('d/m/Y') }}
                                    @else
                                    {{ __('No especificado') }}
                                    @endif
                                </p>

                                <strong>{{ __('Fecha de Envío a VIEX:') }}</strong>
                                <p>{{ $work->submitted_at?->format('d/m/Y H:i') ?? __('No especificado') }}</p>
                            </div>
                        </div>

                        @if($work->description)
                        <div class="mt-3">
                            <strong>{{ __('Descripción:') }}</strong>
                            <p>{{ $work->description }}</p>
                        </div>
                        @endif

                        <!-- Archivos Adjuntos -->
                        @if($work->getMedia()->isNotEmpty())
                        <div class="mt-4">
                            <strong>{{ __('Documentos Adjuntos:') }}</strong>
                            <div class="list-group mt-2">
                                @foreach($work->getMedia() as $media)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-alt text-info mr-2"></i>
                                        {{ $media->name }}
                                        <small class="text-muted d-block">
                                            {{ $media->human_readable_size }} -
                                            {{ $media->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <a href="{{ $media->getUrl() }}" class="btn btn-sm btn-outline-primary"
                                        target="_blank">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Panel de Evaluación (solo si está en estado adecuado) -->
                @php
                $currentStatus = $work->currentStatus->name ?? '';
                @endphp

                @if(in_array($currentStatus, ['Enviado a VIEX', 'En VIEX - En Evaluación']))
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Panel de Evaluación VIEX') }}</h3>
                    </div>
                    <div class="card-body">
                        @if($currentStatus === 'Enviado a VIEX')
                        <!-- Formulario de recepción en VIEX -->
                        <div class="evaluation-panel">
                            <div class="alert alert-info">
                                <i class="fas fa-inbox"></i>
                                <strong>Trabajo pendiente de recepción</strong><br>
                                Debe recibir el trabajo en VIEX para proceder con la evaluación directa.
                            </div>

                            <h5>{{ __('Recibir Trabajo en VIEX') }}</h5>
                            <form action="{{ route('viex.receive', $work) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="comments">{{ __('Comentarios (opcional)') }}</label>
                                    <textarea name="comments" id="comments" rows="3"
                                        class="form-control @error('comments') is-invalid @enderror"
                                        placeholder="{{ __('Comentarios sobre la recepción...') }}"></textarea>
                                    @error('comments')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-inbox mr-2"></i>
                                    {{ __('Recibir Trabajo') }}
                                </button>
                            </form>
                        </div>

                        @elseif($currentStatus === 'En VIEX - En Evaluación')
                        <!-- Panel de evaluación directa -->
                        <div class="evaluation-panel">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Evaluación lista</strong><br>
                                El trabajo está listo para evaluación directa por parte del administrador VIEX.
                            </div>

                            <!-- Acciones directas de aprobación/rechazo -->
                            <div class="mt-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-success btn-block" data-toggle="modal"
                                            data-target="#approveCertifyModal">
                                            <i class="fas fa-check mr-2"></i>
                                            {{ __('Aprobar y Certificar') }}
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-danger btn-block" data-toggle="modal"
                                            data-target="#rejectModal">
                                            <i class="fas fa-times mr-2"></i>
                                            {{ __('Rechazar Trabajo') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @elseif($currentStatus === 'En VIEX - Aprobado')
                        <!-- Formulario de Certificación -->
                        <div class="evaluation-panel">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Trabajo aprobado</strong><br>
                                Proceda a generar la certificación oficial.
                            </div>

                            <h5>{{ __('Generar Certificación') }}</h5>
                            @if(!$work->certification)
                            <form action="{{ route('viex.certify', $work) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="validity_years">{{ __('Años de Vigencia') }}</label>
                                            <select name="validity_years" id="validity_years"
                                                class="form-control @error('validity_years') is-invalid @enderror"
                                                required>
                                                <option value="1">{{ __('1 año') }}</option>
                                                <option value="2" selected>{{ __('2 años') }}</option>
                                                <option value="3">{{ __('3 años') }}</option>
                                                <option value="5">{{ __('5 años') }}</option>
                                            </select>
                                            @error('validity_years')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label
                                                for="certification_number">{{ __('Número de Certificación (opcional)') }}</label>
                                            <input type="text" name="certification_number" id="certification_number"
                                                class="form-control @error('certification_number') is-invalid @enderror"
                                                placeholder="{{ __('Se generará automáticamente si se deja vacío') }}">
                                            @error('certification_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="cert_comments">{{ __('Comentarios (opcional)') }}</label>
                                    <textarea name="comments" id="cert_comments" rows="3"
                                        class="form-control @error('comments') is-invalid @enderror"
                                        placeholder="{{ __('Comentarios sobre la certificación...') }}"></textarea>
                                    @error('comments')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-certificate mr-2"></i>
                                    {{ __('Generar Certificación') }}
                                </button>
                            </form>
                            @else
                            <div class="alert alert-success">
                                <i class="fas fa-certificate mr-2"></i>
                                {{ __('Trabajo ya certificado con número: ') }}
                                <strong>{{ $work->certification->certification_number }}</strong>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                @elseif($currentStatus === 'Certificado')
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title text-white">{{ __('Trabajo Certificado') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <i class="fas fa-certificate fa-2x"></i>
                            <strong>¡Certificación Completa!</strong><br>
                            Este trabajo ha sido certificado oficialmente por VIEX.
                        </div>
                    </div>
                </div>

                @elseif($currentStatus === 'Rechazado por VIEX')
                <div class="card">
                    <div class="card-header bg-danger">
                        <h3 class="card-title text-white">{{ __('Trabajo Rechazado') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i>
                            <strong>Trabajo Rechazado</strong><br>
                            Este trabajo fue rechazado por VIEX con observaciones.
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Panel Lateral -->
            <div class="col-md-4">
                <!-- Historial de Estados -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Historial de Estados') }}</h3>
                    </div>
                    <div class="card-body">
                        @if($work->statusHistory->isEmpty())
                        <p class="text-muted">{{ __('No hay historial disponible') }}</p>
                        @else
                        <div class="timeline">
                            @foreach($work->statusHistory->sortByDesc('created_at') as $history)
                            <div class="timeline-item mb-3">
                                <strong>{{ $history->toStatus->getAttribute('name') }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $history->created_at->format('d/m/Y H:i') }}
                                    @if($history->changedByUser)
                                    <br>{{ __('por') }} {{ $history->changedByUser->getAttribute('name') }}
                                    @endif
                                </small>
                                @if($history->comments)
                                <p class="mt-2 small">{{ $history->comments }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Información de Certificación -->
                @if($work->certification)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Certificación') }}</h3>
                    </div>
                    <div class="card-body">
                        <strong>{{ __('Número:') }}</strong>
                        <p>{{ $work->certification->certification_number }}</p>

                        <strong>{{ __('Fecha de Emisión:') }}</strong>
                        <p>{{ $work->certification->issue_date->format('d/m/Y') }}</p>

                        <strong>{{ __('Válida hasta:') }}</strong>
                        <p>{{ $work->certification->valid_until->format('d/m/Y') }}</p>

                        <strong>{{ __('Emitida por:') }}</strong>
                        <p>{{ $work->certification->issuedByUser->getAttribute('name') }}</p>

                        @if($work->certification->comments)
                        <strong>{{ __('Comentarios:') }}</strong>
                        <p>{{ $work->certification->comments }}</p>
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('viex.certificate.download', $work->certification) }}"
                                class="btn btn-primary btn-block">
                                <i class="fas fa-download mr-2"></i>
                                {{ __('Descargar Certificado') }}
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Acciones Adicionales -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Acciones') }}</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('viex.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left mr-2"></i>
                            {{ __('Volver al Listado') }}
                        </a>

                        @if($work->canGenerateReport())
                        <a href="{{ route('viex.report', $work) }}" class="btn btn-info btn-block">
                            <i class="fas fa-file-pdf mr-2"></i>
                            {{ __('Generar Reporte') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<!-- Modal de Aprobar y Certificar -->
<div class="modal fade" id="approveCertifyModal" tabindex="-1" role="dialog" aria-labelledby="approveCertifyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title" id="approveCertifyModalLabel">{{ __('Aprobar y Certificar Trabajo') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('viex.approve-and-certify', $work) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-info-circle"></i>
                        <strong>Confirmación de Aprobación y Certificación</strong>
                        <p class="mb-0 mt-2">
                            Al aprobar y certificar este trabajo, se completará el proceso de evaluación y se generará automáticamente una certificación oficial válida por 2 años.
                        </p>
                    </div>

                    {{-- Resumen del Trabajo --}}
                    <div class="card card-outline card-success">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-alt"></i>
                                Resumen del Trabajo
                            </h5>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Título:</dt>
                                <dd class="col-sm-8">{{ $work->title }}</dd>

                                <dt class="col-sm-4">Tipo:</dt>
                                <dd class="col-sm-8">{{ $work->workType->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Profesor:</dt>
                                <dd class="col-sm-8">{{ $work->responsibleUser->name ?? 'N/A' }}</dd>

                                <dt class="col-sm-4">Unidad:</dt>
                                <dd class="col-sm-8">{{ $work->organizationalUnit->name ?? 'N/A' }}</dd>
                            </dl>
                        </div>
                    </div>

                    {{-- Comentarios Opcionales --}}
                    <div class="form-group">
                        <label for="certification_comments">
                            <i class="fas fa-comment-alt"></i>
                            Comentarios de Certificación (opcional)
                        </label>
                        <textarea
                            name="comments"
                            id="certification_comments"
                            class="form-control"
                            rows="4"
                            placeholder="Puede agregar comentarios sobre la certificación, observaciones finales, o recomendaciones..."></textarea>
                        <small class="form-text text-muted">
                            Estos comentarios serán incluidos en la certificación y visibles para el profesor.
                        </small>
                    </div>

                    {{-- Confirmación --}}
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="confirm_approve_certify" required>
                        <label class="custom-control-label" for="confirm_approve_certify">
                            <strong>Confirmo que he revisado completamente este trabajo y apruebo su certificación oficial.</strong>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-certificate mr-1"></i>
                        Confirmar Aprobación y Certificación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Rechazo -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">{{ __('Rechazar Trabajo') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('viex.reject', $work) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">{{ __('Motivo del Rechazo') }}</label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="4" class="form-control" required
                            placeholder="{{ __('Especifique el motivo del rechazo...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times mr-2"></i>
                        {{ __('Rechazar Trabajo') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @endsection

    @push('scripts')
    <script>
        const confirmMessage = "{{ __('Esta seguro de aprobar este trabajo?') }}";
        const currentStatusName = "{{ $work->currentStatus->getAttribute('name') }}";

        function confirmApproval() {
            return confirm(confirmMessage);
        }

        $(document).ready(function() {
            // Auto-refresh para trabajos en evaluación
            if (currentStatusName === 'En VIEX - En Evaluación') {
                setTimeout(function() {
                    window.location.reload();
                }, 300000); // 5 minutos
            }

            // Validación del modal de aprobación y certificación
            $('#approveCertifyModal form').on('submit', function(e) {
                const checkbox = $('#confirm_approve_certify');

                if (!checkbox.is(':checked')) {
                    e.preventDefault();
                    alert('Debe confirmar que ha revisado completamente el trabajo antes de aprobar y certificar.');
                    return false;
                }

                // Deshabilitar botón para evitar doble-clic
                const $submitBtn = $(this).find('button[type="submit"]');
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...');

                // Si falla, restaurar después de 5 segundos
                setTimeout(function() {
                    if ($submitBtn.prop('disabled')) {
                        $submitBtn.prop('disabled', false);
                        $submitBtn.html('<i class="fas fa-certificate mr-1"></i>Confirmar Aprobación y Certificación');
                    }
                }, 5000);
            });

            // Validación del modal de rechazo
            $('#rejectModal form').on('submit', function(e) {
                const reason = $('#rejection_reason').val().trim();

                if (reason.length < 20) {
                    e.preventDefault();
                    alert('Por favor, proporcione una razón detallada del rechazo (mínimo 20 caracteres).');
                    return false;
                }

                if (!confirm('⚠️ ATENCIÓN: ¿Está completamente seguro de que desea RECHAZAR este trabajo? Esta es una acción seria.')) {
                    e.preventDefault();
                    return false;
                }

                // Deshabilitar botón
                const $submitBtn = $(this).find('button[type="submit"]');
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...');
            });

            // Cerrar modales al hacer clic en cancelar
            $('.modal').on('hidden.bs.modal', function() {
                $(this).find('form')[0]?.reset();
                $(this).find('button[type="submit"]').prop('disabled', false);
                $(this).find('button[type="submit"]').html(function() {
                    return $(this).hasClass('btn-success') ?
                        '<i class="fas fa-certificate mr-1"></i>Confirmar Aprobación y Certificación' :
                        '<i class="fas fa-times mr-2"></i>Rechazar Trabajo';
                });
            });
        });
    </script>
    @endpush