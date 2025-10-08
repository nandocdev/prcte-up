@extends('adminlte::page')

@section('title', 'VIEX - Revisar Evaluaciones')

@section('content_header')
    <h1>
        <i class="fas fa-gavel"></i> Revisar Evaluaciones y Tomar Decisión
        <small class="text-muted">Trabajo #{{ $work->id }}</small>
    </h1>
@stop

@section('content')
    {{-- Información del Trabajo --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt"></i> {{ $work->title }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Tipo:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-info">{{ $work->workType->name }}</span>
                                </dd>

                                <dt class="col-sm-4">Responsable:</dt>
                                <dd class="col-sm-8">{{ $work->responsibleUser->name }}</dd>

                                <dt class="col-sm-4">Unidad:</dt>
                                <dd class="col-sm-8">{{ $work->organizationalUnit->name }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-sm-4">Período:</dt>
                                <dd class="col-sm-8">
                                    {{ $work->start_date?->format('d/m/Y') }} - {{ $work->end_date?->format('d/m/Y') }}
                                </dd>

                                <dt class="col-sm-4">Estado:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge badge-info">{{ $work->currentStatus->name }}</span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Resumen Global --}}
    <div class="row">
        <div class="col-md-3">
            <div class="info-box bg-info">
                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Evaluaciones Completadas</span>
                    <span class="info-box-number">
                        {{ $evaluationSummary['completed_evaluations'] }}/{{ $evaluationSummary['total_evaluators'] }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box bg-{{ $evaluationSummary['average_weighted_score'] >= 70 ? 'success' : ($evaluationSummary['average_weighted_score'] >= 50 ? 'warning' : 'danger') }}">
                <span class="info-box-icon"><i class="fas fa-star"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Puntuación Promedio</span>
                    <span class="info-box-number">{{ $evaluationSummary['average_weighted_score'] }}/100</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box bg-success">
                <span class="info-box-icon"><i class="fas fa-thumbs-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Aprobaciones</span>
                    <span class="info-box-number">{{ $evaluationSummary['approvals_count'] }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box bg-danger">
                <span class="info-box-icon"><i class="fas fa-thumbs-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Rechazos</span>
                    <span class="info-box-number">{{ $evaluationSummary['rejections_count'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recomendación --}}
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-{{ $evaluationSummary['approvals_count'] > $evaluationSummary['rejections_count'] ? 'success' : 'warning' }} alert-dismissible">
                <h5><i class="fas fa-lightbulb"></i> Recomendación del Sistema:</h5>
                <p class="mb-0"><strong>{{ $evaluationSummary['recommendation'] }}</strong></p>
            </div>
        </div>
    </div>

    {{-- Detalle de Cada Evaluación --}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i> Detalle de Evaluaciones
                    </h3>
                </div>
                <div class="card-body">
                    @foreach($work->evaluations as $evaluation)
                    <div class="card mb-3 {{ $evaluation->final_decision == 'approve' ? 'border-success' : ($evaluation->final_decision == 'reject' ? 'border-danger' : 'border-warning') }}">
                        <div class="card-header bg-{{ $evaluation->final_decision == 'approve' ? 'success' : ($evaluation->final_decision == 'reject' ? 'danger' : 'warning') }}">
                            <h5 class="mb-0">
                                <i class="fas fa-user-tie"></i> {{ $evaluation->evaluator->name }}
                                @if($evaluation->workEvaluator && $evaluation->workEvaluator->role == 'lead_evaluator')
                                    <span class="badge badge-light ml-2">
                                        <i class="fas fa-star"></i> Evaluador Principal
                                    </span>
                                @endif
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Decisión:</strong>
                                    @switch($evaluation->final_decision)
                                        @case('approve')
                                            <span class="badge badge-success">Aprobar</span>
                                            @break
                                        @case('approve_with_conditions')
                                            <span class="badge badge-warning">Aprobar con Condiciones</span>
                                            @break
                                        @case('reject')
                                            <span class="badge badge-danger">Rechazar</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">Pendiente</span>
                                    @endswitch
                                </div>
                                <div class="col-md-4">
                                    <strong>Puntuación Total:</strong> {{ $evaluation->total_score ?? 'N/A' }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Puntuación Ponderada:</strong> {{ $evaluation->weighted_score ?? 'N/A' }}/100
                                </div>
                            </div>

                            @if($evaluation->general_comments)
                            <div class="mb-3">
                                <strong>Comentarios Generales:</strong>
                                <p class="text-muted">{{ $evaluation->general_comments }}</p>
                            </div>
                            @endif

                            <div class="row">
                                @if($evaluation->strengths)
                                <div class="col-md-4">
                                    <strong class="text-success">Fortalezas:</strong>
                                    <p class="text-muted">{{ $evaluation->strengths }}</p>
                                </div>
                                @endif

                                @if($evaluation->weaknesses)
                                <div class="col-md-4">
                                    <strong class="text-danger">Debilidades:</strong>
                                    <p class="text-muted">{{ $evaluation->weaknesses }}</p>
                                </div>
                                @endif

                                @if($evaluation->recommendations)
                                <div class="col-md-4">
                                    <strong class="text-info">Recomendaciones:</strong>
                                    <p class="text-muted">{{ $evaluation->recommendations }}</p>
                                </div>
                                @endif
                            </div>

                            @if($evaluation->decision_justification)
                            <div class="alert alert-light">
                                <strong>Justificación de la Decisión:</strong>
                                <p class="mb-0">{{ $evaluation->decision_justification }}</p>
                            </div>
                            @endif

                            {{-- Detalles por Criterio --}}
                            @if($evaluation->evaluationDetails->count() > 0)
                            <div class="mt-3">
                                <strong>Puntuación por Criterio:</strong>
                                <table class="table table-sm table-bordered mt-2">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Criterio</th>
                                            <th class="text-center">Puntaje</th>
                                            <th class="text-center">Normalizado</th>
                                            <th>Comentarios</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evaluation->evaluationDetails as $detail)
                                        <tr>
                                            <td>{{ $detail->criteria->name }}</td>
                                            <td class="text-center">
                                                <strong>{{ $detail->score }}/{{ $detail->criteria->max_score }}</strong>
                                            </td>
                                            <td class="text-center">
                                                {{ $detail->normalized_score }}/100
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $detail->comments ?? 'Sin comentarios' }}</small>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                        <div class="card-footer text-muted">
                            <small>
                                Enviado: {{ $evaluation->submitted_at?->format('d/m/Y H:i') ?? 'Pendiente' }}
                            </small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Formularios de Decisión --}}
    <div class="row">
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle"></i> Aprobar Trabajo
                    </h3>
                </div>
                <form method="POST" action="{{ route('viex.approve', $work) }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Comentarios Finales</label>
                            <textarea name="comments" class="form-control" rows="4" placeholder="Comentarios sobre la aprobación...">{{ old('comments') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Recomendaciones</label>
                            <textarea name="recommendations" class="form-control" rows="3" placeholder="Recomendaciones para el responsable...">{{ old('recommendations') }}</textarea>
                        </div>

                        <div class="alert alert-success">
                            <i class="fas fa-info-circle"></i>
                            El trabajo pasará al estado <strong>"En VIEX - Aprobado"</strong> y podrá proceder a la certificación.
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-check"></i> Aprobar Trabajo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-times-circle"></i> Rechazar Trabajo
                    </h3>
                </div>
                <form method="POST" action="{{ route('viex.reject', $work) }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label>Razón del Rechazo <span class="text-danger">*</span></label>
                            <textarea name="reason" 
                                      class="form-control @error('reason') is-invalid @enderror" 
                                      rows="4" 
                                      required
                                      placeholder="Explique las razones del rechazo...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Recomendaciones para Mejorar</label>
                            <textarea name="recommendations" class="form-control" rows="3" placeholder="Sugerencias para que el responsable mejore el trabajo...">{{ old('recommendations') }}</textarea>
                        </div>

                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            El trabajo pasará a <strong>"Rechazado por VIEX"</strong> y volverá al responsable para correcciones.
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-times"></i> Rechazar Trabajo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            <a href="{{ route('viex.show', $work) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Detalles
            </a>
        </div>
    </div>
@stop

@section('css')
    <style>
        .card.border-success { border-width: 3px !important; }
        .card.border-danger { border-width: 3px !important; }
        .card.border-warning { border-width: 3px !important; }
    </style>
@stop
