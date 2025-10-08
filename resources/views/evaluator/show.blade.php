@extends('adminlte::page')

@section('title', 'Evaluador - Detalles del Trabajo')

@section('content_header')
    <h1>
        <i class="fas fa-file-alt"></i> Detalles del Trabajo Asignado
        <small class="text-muted">#{{ $work->id }}</small>
    </h1>
@stop

@section('content')
    {{-- Estado de Asignación --}}
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-{{ $assignment->status == 'pending' ? 'warning' : ($assignment->status == 'completed' ? 'success' : 'info') }}">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5>
                            <i class="fas fa-info-circle"></i> 
                            Estado de su Asignación: 
                            <strong>
                                @switch($assignment->status)
                                    @case('pending') Pendiente de Aceptar @break
                                    @case('accepted') Aceptada @break
                                    @case('in_progress') En Progreso @break
                                    @case('completed') Completada @break
                                    @case('declined') Rechazada @break
                                @endswitch
                            </strong>
                        </h5>
                        @if($assignment->role == 'lead_evaluator')
                        <p class="mb-0">
                            <i class="fas fa-star text-warning"></i> 
                            Usted es el <strong>Evaluador Principal</strong> de este trabajo.
                        </p>
                        @endif
                    </div>
                    <div class="col-md-4 text-right">
                        @if($assignment->status == 'pending')
                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#acceptModal">
                                <i class="fas fa-check"></i> Aceptar Asignación
                            </button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#declineModal">
                                <i class="fas fa-times"></i> Rechazar
                            </button>
                        @elseif(in_array($assignment->status, ['accepted', 'in_progress']) && (!$evaluation || $evaluation->status != 'submitted'))
                            <a href="{{ route('evaluator.evaluate', $work) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-edit"></i> Iniciar/Continuar Evaluación
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Información del Trabajo --}}
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">
                        <i class="fas fa-info"></i> Información del Trabajo
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Título:</dt>
                        <dd class="col-sm-9">{{ $work->title }}</dd>

                        <dt class="col-sm-3">Tipo:</dt>
                        <dd class="col-sm-9">
                            <span class="badge badge-info">{{ $work->workType->name }}</span>
                        </dd>

                        <dt class="col-sm-3">Descripción:</dt>
                        <dd class="col-sm-9">{{ $work->description ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Período:</dt>
                        <dd class="col-sm-9">
                            {{ $work->start_date?->format('d/m/Y') ?? 'N/A' }} - 
                            {{ $work->end_date?->format('d/m/Y') ?? 'N/A' }}
                        </dd>

                        <dt class="col-sm-3">Período Académico:</dt>
                        <dd class="col-sm-9">{{ $work->academic_period ?? 'N/A' }}</dd>
                    </dl>

                    @if($work->projectDetail)
                        <hr>
                        <h5 class="text-primary">Detalles del Proyecto</h5>
                        <dl class="row">
                            <dt class="col-sm-3">Tipo de Proyecto:</dt>
                            <dd class="col-sm-9">{{ $work->projectDetail->institutionalProjectType->name ?? 'N/A' }}</dd>

                            <dt class="col-sm-3">Objetivos:</dt>
                            <dd class="col-sm-9">{{ $work->projectDetail->objectives ?? 'N/A' }}</dd>

                            <dt class="col-sm-3">Beneficiarios:</dt>
                            <dd class="col-sm-9">{{ $work->projectDetail->beneficiaries ?? 'N/A' }}</dd>
                        </dl>
                    @elseif($work->activityDetail)
                        <hr>
                        <h5 class="text-primary">Detalles de la Actividad</h5>
                        <dl class="row">
                            <dt class="col-sm-3">Modalidad:</dt>
                            <dd class="col-sm-9">{{ $work->activityDetail->modality ?? 'N/A' }}</dd>

                            <dt class="col-sm-3">Participantes:</dt>
                            <dd class="col-sm-9">{{ $work->activityDetail->participants_count ?? 'N/A' }}</dd>
                        </dl>
                    @elseif($work->publicationDetail)
                        <hr>
                        <h5 class="text-primary">Detalles de la Publicación</h5>
                        <dl class="row">
                            <dt class="col-sm-3">Tipo:</dt>
                            <dd class="col-sm-9">{{ $work->publicationDetail->publication_type ?? 'N/A' }}</dd>

                            <dt class="col-sm-3">Editorial/Revista:</dt>
                            <dd class="col-sm-9">{{ $work->publicationDetail->publisher ?? 'N/A' }}</dd>

                            <dt class="col-sm-3">ISBN/ISSN:</dt>
                            <dd class="col-sm-9">{{ $work->publicationDetail->isbn ?? $work->publicationDetail->issn ?? 'N/A' }}</dd>
                        </dl>
                    @elseif($work->technicalAssistanceDetail)
                        <hr>
                        <h5 class="text-primary">Detalles de la Asistencia Técnica</h5>
                        <dl class="row">
                            <dt class="col-sm-3">Tipo:</dt>
                            <dd class="col-sm-9">{{ $work->technicalAssistanceDetail->assistance_type ?? 'N/A' }}</dd>

                            <dt class="col-sm-3">Entidad Beneficiaria:</dt>
                            <dd class="col-sm-9">{{ $work->technicalAssistanceDetail->beneficiary_entity ?? 'N/A' }}</dd>
                        </dl>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Responsable del Trabajo
                    </h3>
                </div>
                <div class="card-body">
                    <p><strong>{{ $work->responsibleUser->name }}</strong></p>
                    <p class="mb-1">
                        <i class="fas fa-id-badge"></i> 
                        {{ $work->responsibleUser->professor_code ?? 'N/A' }}
                    </p>
                    <p class="mb-1">
                        <i class="fas fa-envelope"></i> 
                        {{ $work->responsibleUser->email }}
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-building"></i> 
                        {{ $work->organizationalUnit->name ?? 'N/A' }}
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title">
                        <i class="fas fa-calendar"></i> Información de Asignación
                    </h3>
                </div>
                <div class="card-body">
                    <dl>
                        <dt>Asignado por:</dt>
                        <dd>{{ $assignment->assignedBy->name ?? 'N/A' }}</dd>

                        <dt>Fecha de Asignación:</dt>
                        <dd>{{ $assignment->assigned_at->format('d/m/Y H:i') }}</dd>

                        @if($assignment->accepted_at)
                        <dt>Fecha de Aceptación:</dt>
                        <dd>{{ $assignment->accepted_at->format('d/m/Y H:i') }}</dd>
                        @endif

                        @if($assignment->completed_at)
                        <dt>Fecha de Completado:</dt>
                        <dd>{{ $assignment->completed_at->format('d/m/Y H:i') }}</dd>
                        @endif

                        @if($assignment->assignment_notes)
                        <dt>Instrucciones:</dt>
                        <dd class="text-muted">{{ $assignment->assignment_notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            @if($evaluation)
            <div class="card card-{{ $evaluation->status == 'submitted' ? 'success' : 'warning' }}">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-check"></i> Estado de Evaluación
                    </h3>
                </div>
                <div class="card-body">
                    <p>
                        <strong>Estado:</strong>
                        @switch($evaluation->status)
                            @case('draft') <span class="badge badge-secondary">Borrador</span> @break
                            @case('in_progress') <span class="badge badge-warning">En Progreso</span> @break
                            @case('submitted') <span class="badge badge-success">Enviada</span> @break
                            @case('reviewed') <span class="badge badge-info">Revisada</span> @break
                        @endswitch
                    </p>

                    @if($evaluation->weighted_score)
                    <p>
                        <strong>Puntuación:</strong> {{ $evaluation->weighted_score }}/100
                    </p>
                    @endif

                    @if($evaluation->final_decision && $evaluation->final_decision != 'pending')
                    <p>
                        <strong>Decisión:</strong>
                        @switch($evaluation->final_decision)
                            @case('approve') <span class="badge badge-success">Aprobar</span> @break
                            @case('approve_with_conditions') <span class="badge badge-warning">Aprobar con Condiciones</span> @break
                            @case('reject') <span class="badge badge-danger">Rechazar</span> @break
                        @endswitch
                    </p>
                    @endif

                    @if($evaluation->status != 'submitted')
                    <a href="{{ route('evaluator.evaluate', $work) }}" class="btn btn-primary btn-block">
                        <i class="fas fa-edit"></i> Continuar Evaluación
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Participantes --}}
    @if($work->participants->count() > 0)
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-secondary">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Participantes del Trabajo
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Rol</th>
                                    <th>Horas Dedicadas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($work->participants as $participant)
                                <tr>
                                    <td>{{ $participant->user->name ?? $participant->external_name }}</td>
                                    <td>{{ $participant->role ?? 'N/A' }}</td>
                                    <td>{{ $participant->hours_dedicated ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12 text-center">
            <a href="{{ route('evaluator.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Mis Asignaciones
            </a>
        </div>
    </div>

    {{-- Modal: Aceptar Asignación --}}
    <div class="modal fade" id="acceptModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('evaluator.accept-assignment', $work) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title">Aceptar Asignación de Evaluación</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Confirma que acepta evaluar este trabajo de extensión?</p>
                        <p class="text-muted">
                            Al aceptar, se compromete a realizar una evaluación objetiva y profesional
                            del trabajo dentro de los plazos establecidos.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Aceptar Asignación
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Rechazar Asignación --}}
    <div class="modal fade" id="declineModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('evaluator.decline-assignment', $work) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title">Rechazar Asignación</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Razón del Rechazo <span class="text-danger">*</span></label>
                            <textarea name="reason" 
                                      class="form-control" 
                                      rows="4" 
                                      required
                                      placeholder="Explique por qué no puede aceptar esta evaluación..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-times"></i> Rechazar Asignación
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop
