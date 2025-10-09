@extends('adminlte::page')

@section('title', 'VIEX - Asignar Evaluadores')

@section('content_header')
<h1>
    <i class="fas fa-user-plus"></i> Asignar Evaluadores
    <small class="text-muted">Trabajo #{{ $work->id }}</small>
</h1>
@stop

@section('content')
<div class="row">
    {{-- Información del Trabajo --}}
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt"></i> Información del Trabajo
                </h3>
            </div>
            <div class="card-body">
                <dl>
                    <dt>Título:</dt>
                    <dd>{{ $work->title }}</dd>

                    <dt>Tipo:</dt>
                    <dd>
                        <span class="badge badge-info">{{ $work->workType->name }}</span>
                    </dd>

                    <dt>Responsable:</dt>
                    <dd>{{ $work->responsibleUser->name }}</dd>

                    <dt>Unidad:</dt>
                    <dd><small>{{ $work->organizationalUnit->name }}</small></dd>
                </dl>
            </div>
        </div>

        {{-- Evaluadores Ya Asignados --}}
        @if($work->workEvaluators->count() > 0)
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-users"></i> Evaluadores Asignados
                </h3>
                <div class="card-tools">
                    <span class="badge badge-light">{{ $work->workEvaluators->count() }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($work->workEvaluators as $we)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $we->evaluator->name }}</strong>
                                @if($we->role_evaluator == 'lead_evaluator')
                                <span class="badge badge-warning ml-1">
                                    <i class="fas fa-star"></i> Principal
                                </span>
                                @endif
                                <br>
                                <small class="text-muted">
                                    {{ $we->evaluator->email }}
                                </small>
                            </div>
                            <span class="badge badge-{{ $we->status == 'pending' ? 'warning' : ($we->status == 'accepted' ? 'success' : 'secondary') }}">
                                {{ ucfirst($we->status) }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>

    {{-- Formulario de Asignación --}}
    <div class="col-md-8">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-plus-circle"></i> Asignar Nuevo Evaluador
                </h3>
            </div>
            <form method="POST" action="{{ route('viex.assign-evaluator', $work) }}">
                @csrf
                <div class="card-body">
                    @if($availableEvaluators->count() > 0)
                    <div class="form-group">
                        <label for="evaluator_id">
                            Seleccionar Evaluador <span class="text-danger">*</span>
                        </label>
                        <select name="evaluator_id"
                            id="evaluator_id"
                            class="form-control @error('evaluator_id') is-invalid @enderror"
                            required>
                            <option value="">-- Seleccione un evaluador --</option>
                            @foreach($availableEvaluators as $evaluator)
                            <option value="{{ $evaluator->id }}" {{ old('evaluator_id') == $evaluator->id ? 'selected' : '' }}>
                                {{ $evaluator->name }} - {{ $evaluator->email }}
                                @if($evaluator->professor_code)
                                (Cód: {{ $evaluator->professor_code }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('evaluator_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">
                            Seleccione un usuario con rol de evaluador disponible.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="role_evaluator">
                            Rol del Evaluador <span class="text-danger">*</span>
                        </label>
                        <select name="role_evaluator"
                            id="role_evaluator"
                            class="form-control @error('role_evaluator') is-invalid @enderror"
                            required>
                            <option value="evaluator" {{ old('role_evaluator') == 'evaluator' ? 'selected' : '' }}>
                                Evaluador Regular
                            </option>
                            <option value="lead_evaluator" {{ old('role_evaluator') == 'lead_evaluator' ? 'selected' : '' }}>
                                Evaluador Principal (Lead)
                            </option>
                        </select>
                        @error('role_evaluator')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i>
                            El evaluador principal tiene mayor responsabilidad y su opinión tendrá mayor peso.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="assignment_notes">
                            Notas de Asignación / Instrucciones
                        </label>
                        <textarea name="assignment_notes"
                            id="assignment_notes"
                            class="form-control @error('assignment_notes') is-invalid @enderror"
                            rows="4"
                            placeholder="Instrucciones especiales, aspectos a considerar, plazo sugerido, etc.">{{ old('assignment_notes') }}</textarea>
                        @error('assignment_notes')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">
                            Estas notas serán visibles para el evaluador asignado.
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <h5><i class="fas fa-lightbulb"></i> Recomendaciones:</h5>
                        <ul class="mb-0">
                            <li>Asigne al menos <strong>2 evaluadores</strong> por trabajo.</li>
                            <li>Considere asignar un <strong>evaluador principal</strong> con experiencia en el tema.</li>
                            <li>El evaluador recibirá una notificación por correo electrónico.</li>
                        </ul>
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>No hay evaluadores disponibles</strong>
                        <p class="mb-0">
                            Todos los usuarios con rol de evaluador ya están asignados a este trabajo,
                            o no existen evaluadores registrados en el sistema.
                        </p>
                    </div>
                    @endif
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('viex.show', $work) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        @if($availableEvaluators->count() > 0)
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Asignar Evaluador
                        </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .list-group-item {
        border-left: 3px solid #17a2b8;
    }
</style>
@stop