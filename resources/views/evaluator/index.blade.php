@extends('adminlte::page')

@section('title', 'Evaluador - Mis Asignaciones')

@section('content_header')
    <h1>
        <i class="fas fa-clipboard-check"></i> Mis Asignaciones
        <small class="text-muted">Panel del Evaluador</small>
    </h1>
@stop

@section('content')
    {{-- Estadísticas --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['pending'] }}</h3>
                    <p>Pendientes de Aceptar</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="#pending" class="small-box-footer">
                    Ver <i class="fas fa-arrow-circle-down"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['in_progress'] }}</h3>
                    <p>En Progreso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <a href="#in-progress" class="small-box-footer">
                    Ver <i class="fas fa-arrow-circle-down"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['completed_this_month'] }}</h3>
                    <p>Completadas Este Mes</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['total_completed'] }}</h3>
                    <p>Total Completadas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Asignaciones Pendientes --}}
    @if($pendingAssignments->count() > 0)
    <div class="card card-warning" id="pending">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exclamation-circle"></i> Asignaciones Pendientes de Aceptar
            </h3>
            <div class="card-tools">
                <span class="badge badge-warning">{{ $pendingAssignments->count() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título del Trabajo</th>
                            <th>Tipo</th>
                            <th>Responsable</th>
                            <th>Asignado Por</th>
                            <th>Fecha Asignación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingAssignments as $assignment)
                        @php
                            $work = $assignment->workOfExtension;
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-primary">#{{ $work->id }}</strong>
                            </td>
                            <td>
                                {{ Str::limit($work->title, 50) }}
                                @if($assignment->role == 'lead_evaluator')
                                    <span class="badge badge-warning ml-1">
                                        <i class="fas fa-star"></i> Principal
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $work->workType->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $work->responsibleUser->name ?? 'N/A' }}</td>
                            <td>{{ $assignment->assignedBy->name ?? 'N/A' }}</td>
                            <td>
                                <small>{{ $assignment->assigned_at->format('d/m/Y') }}</small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('evaluator.show', $work) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="Ver Detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Asignaciones En Progreso --}}
    @if($acceptedAssignments->count() > 0)
    <div class="card card-info" id="in-progress">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-spinner"></i> Evaluaciones En Progreso
            </h3>
            <div class="card-tools">
                <span class="badge badge-info">{{ $acceptedAssignments->count() }}</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título del Trabajo</th>
                            <th>Tipo</th>
                            <th>Responsable</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($acceptedAssignments as $assignment)
                        @php
                            $work = $assignment->workOfExtension;
                            $evaluation = $work->evaluations()
                                ->where('evaluator_user_id', auth()->id())
                                ->first();
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-primary">#{{ $work->id }}</strong>
                            </td>
                            <td>{{ Str::limit($work->title, 50) }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $work->workType->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $work->responsibleUser->name ?? 'N/A' }}</td>
                            <td>
                                @if($assignment->role == 'lead_evaluator')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-star"></i> Principal
                                    </span>
                                @else
                                    <span class="badge badge-secondary">Regular</span>
                                @endif
                            </td>
                            <td>
                                @if($evaluation && $evaluation->status == 'submitted')
                                    <span class="badge badge-success">Enviada</span>
                                @elseif($evaluation)
                                    <span class="badge badge-warning">Borrador</span>
                                @else
                                    <span class="badge badge-secondary">Sin Iniciar</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('evaluator.show', $work) }}" 
                                       class="btn btn-info" 
                                       title="Ver Detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!$evaluation || $evaluation->status != 'submitted')
                                    <a href="{{ route('evaluator.evaluate', $work) }}" 
                                       class="btn btn-primary" 
                                       title="Evaluar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Evaluaciones Completadas --}}
    @if($completedAssignments->count() > 0)
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-check-double"></i> Evaluaciones Completadas
            </h3>
            <div class="card-tools">
                <span class="badge badge-light">Mostrando últimas 10</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Decisión</th>
                            <th>Fecha Completado</th>
                            <th class="text-center">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completedAssignments as $assignment)
                        @php
                            $work = $assignment->workOfExtension;
                            $evaluation = $work->evaluations()
                                ->where('evaluator_user_id', auth()->id())
                                ->first();
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-success">#{{ $work->id }}</strong>
                            </td>
                            <td>{{ Str::limit($work->title, 50) }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $work->workType->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @if($evaluation)
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
                                            <span class="badge badge-secondary">N/A</span>
                                    @endswitch
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $assignment->completed_at?->format('d/m/Y H:i') ?? 'N/A' }}</small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('evaluator.show', $work) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($pendingAssignments->count() == 0 && $acceptedAssignments->count() == 0)
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        No tiene asignaciones pendientes en este momento.
    </div>
    @endif
@stop

@section('css')
    <style>
        .small-box .icon {
            font-size: 70px;
        }
    </style>
@stop
