@extends('layouts.app')

@section('title', 'Trabajo de Extensión - VIEX')

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
            <div class="col-lg-8">
                <!-- Información General del Trabajo -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-file-alt mr-2"></i>
                            {{ $work->title }}
                        </h3>
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
                        <dl class="row">
                            <dt class="col-sm-4">{{ __('Tipo de Trabajo:') }}</dt>
                            <dd class="col-sm-8">
                                <span class="badge badge-info">{{ $work->workType->name }}</span>
                            </dd>

                            <dt class="col-sm-4">{{ __('Descripción:') }}</dt>
                            <dd class="col-sm-8">{{ $work->description ?? __('No especificada') }}</dd>

                            <dt class="col-sm-4">{{ __('Unidad Académica:') }}</dt>
                            <dd class="col-sm-8">{{ $work->organizationalUnit->getAttribute('name') }}</dd>

                            <dt class="col-sm-4">{{ __('Fecha de Inicio:') }}</dt>
                            <dd class="col-sm-8">
                                {{ $work->start_date ? $work->start_date->format('d/m/Y') : __('No especificada') }}
                            </dd>

                            <dt class="col-sm-4">{{ __('Fecha de Fin:') }}</dt>
                            <dd class="col-sm-8">
                                {{ $work->end_date ? $work->end_date->format('d/m/Y') : __('No especificada') }}
                            </dd>

                            @if($work->objectives)
                            <dt class="col-sm-4">{{ __('Objetivos:') }}</dt>
                            <dd class="col-sm-8">{{ $work->objectives }}</dd>
                            @endif

                            @if($work->methodology)
                            <dt class="col-sm-4">{{ __('Metodología:') }}</dt>
                            <dd class="col-sm-8">{{ $work->methodology }}</dd>
                            @endif

                            @if($work->expected_results)
                            <dt class="col-sm-4">{{ __('Resultados Esperados:') }}</dt>
                            <dd class="col-sm-8">{{ $work->expected_results }}</dd>
                            @endif

                            <dt class="col-sm-4">{{ __('Fecha de Envío a VIEX:') }}</dt>
                            <dd class="col-sm-8">{{ $work->submitted_at?->format('d/m/Y H:i') ?? __('No especificado') }}</dd>
                        </dl>
                    </div>
                </div>

                <!-- Información del Profesor Responsable -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-tie mr-2"></i>
                            {{ __('Profesor Responsable') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">{{ __('Nombre:') }}</dt>
                            <dd class="col-sm-8">{{ $work->primaryResponsible->getAttribute('name') }}</dd>

                            <dt class="col-sm-4">{{ __('Código de Profesor:') }}</dt>
                            <dd class="col-sm-8">{{ $work->primaryResponsible->getAttribute('professor_code') }}</dd>

                            <dt class="col-sm-4">{{ __('Email:') }}</dt>
                            <dd class="col-sm-8">{{ $work->primaryResponsible->getAttribute('email') }}</dd>

                            <dt class="col-sm-4">{{ __('Unidad:') }}</dt>
                            <dd class="col-sm-8">{{ $work->organizationalUnit->getAttribute('name') }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Lista de Participantes --}}
                @if($work->participants && $work->participants->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-users mr-2"></i>
                            {{ __('Participantes del Trabajo') }}
                            <span class="badge badge-info ml-2">{{ $work->participants->count() }}</span>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>{{ __('Nombre Completo') }}</th>
                                    <th>{{ __('Rol') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Institución') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($work->participants as $index => $participant)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $participant->getAttribute('name') }}</strong>
                                        @if($participant->getAttribute('is_primary'))
                                        <span class="badge badge-primary ml-1">{{ __('Coordinador') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ $participant->getAttribute('role') ?? __('Participante') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($participant->getAttribute('email'))
                                        <a href="mailto:{{ $participant->getAttribute('email') }}">
                                            <i class="fas fa-envelope"></i>
                                            {{ $participant->getAttribute('email') }}
                                        </a>
                                        @else
                                        <span class="text-muted">{{ __('N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $participant->getAttribute('institution') ?? __('N/A') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Archivos y Evidencias --}}
                @if($work->getMedia('evidencias')->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-paperclip mr-2"></i>
                            {{ __('Evidencias y Documentos') }}
                            <span class="badge badge-success ml-2">{{ $work->getMedia('evidencias')->count() }}</span>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($work->getMedia('evidencias') as $media)
                            <div class="col-md-6 mb-3">
                                <div class="card card-outline card-info">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                @php
                                                $extension = pathinfo($media->name, PATHINFO_EXTENSION);
                                                $iconClass = match (strtolower($extension)) {
                                                'pdf' => 'fas fa-file-pdf text-danger',
                                                'doc', 'docx' => 'fas fa-file-word text-primary',
                                                'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                                'jpg', 'jpeg', 'png', 'gif', 'svg' => 'fas fa-file-image text-warning',
                                                'zip', 'rar' => 'fas fa-file-archive text-secondary',
                                                default => 'fas fa-file text-secondary'
                                                };
                                                @endphp
                                                <i class="{{ $iconClass }} fa-3x"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1 font-weight-bold">
                                                    {{ Str::limit($media->name, 35) }}
                                                </p>
                                                <small class="text-muted">
                                                    <i class="fas fa-weight"></i>
                                                    {{ number_format($media->size / 1024, 1) }} KB
                                                    <br>
                                                    <i class="fas fa-clock"></i>
                                                    {{ $media->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                            <div>
                                                <a href="{{ $media->getUrl() }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="{{ __('Descargar') }} {{ $media->name }}">
                                                    <i class="fas fa-download"></i>
                                                    {{ __('Descargar') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- Resumen de Archivos --}}
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>{{ __('Total de archivos:') }}</strong> {{ $work->getMedia('evidencias')->count() }}
                            <br>
                            <strong>{{ __('Tamaño total:') }}</strong>
                            {{ number_format($work->getMedia('evidencias')->sum('size') / 1024 / 1024, 2) }} MB
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-body">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-paperclip fa-3x mb-3"></i>
                            <p>{{ __('No se han adjuntado evidencias o documentos a este trabajo.') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Historial Completo del Trabajo --}}
                @if($work->statusHistory && $work->statusHistory->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history mr-2"></i>
                            {{ __('Historial del Trabajo') }}
                            <span class="badge badge-secondary ml-2">{{ $work->statusHistory->count() }} {{ __('eventos') }}</span>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($work->statusHistory->sortByDesc('created_at') as $history)
                            {{-- Time Label --}}
                            <div class="time-label">
                                <span class="bg-{{ $loop->first ? 'primary' : 'gray' }}">
                                    <i class="fas fa-calendar-day"></i>
                                    {{ $history->created_at->format('d/m/Y') }}
                                </span>
                            </div>

                            {{-- Timeline Item --}}
                            <div>
                                @php
                                $iconClass = match ($history->status->name ?? '') {
                                'Borrador' => 'fas fa-edit bg-secondary',
                                'Enviado a Coordinador', 'En Revisión Coordinador' => 'fas fa-clock bg-warning',
                                'Enviado a Decano/Director' => 'fas fa-arrow-up bg-success',
                                'Enviado a VIEX' => 'fas fa-check-circle bg-primary',
                                'Aprobado', 'Certificado' => 'fas fa-check-double bg-success',
                                'Devuelto para Corrección' => 'fas fa-redo bg-info',
                                'Rechazado por Coordinador', 'Rechazado por Decano', 'Rechazado por VIEX' => 'fas fa-times-circle bg-danger',
                                default => 'fas fa-circle bg-gray'
                                };
                                @endphp
                                <i class="{{ $iconClass }}"></i>

                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i>
                                        {{ $history->created_at->format('H:i') }}
                                    </span>

                                    <h3 class="timeline-header">
                                        <strong>{{ $history->status->name ?? __('Estado desconocido') }}</strong>
                                    </h3>

                                    @if($history->comments)
                                    <div class="timeline-body">
                                        <div class="callout callout-info">
                                            <p class="mb-0" style="white-space: pre-wrap;">{{ $history->comments }}</p>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="timeline-footer">
                                        @if($history->changedBy)
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i>
                                            <strong>{{ __('Por:') }}</strong> {{ $history->changedBy->name }}
                                        </small>
                                        @endif
                                        <small class="text-muted ml-3">
                                            <i class="fas fa-calendar"></i>
                                            {{ $history->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            {{-- End of Timeline --}}
                            <div>
                                <i class="fas fa-flag-checkered bg-gray"></i>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-body">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-history fa-3x mb-3"></i>
                            <p>{{ __('No hay historial de estados disponible para este trabajo.') }}</p>
                        </div>
                    </div>
                </div>
                @endif

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
                <!-- Información del Sistema -->
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info mr-2"></i>
                            {{ __('Información del Sistema') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-6">{{ __('Creado:') }}</dt>
                            <dd class="col-6">{{ $work->created_at->format('d/m/Y H:i') }}</dd>

                            <dt class="col-6">{{ __('Última actualización:') }}</dt>
                            <dd class="col-6">{{ $work->updated_at->format('d/m/Y H:i') }}</dd>

                            @if($work->updated_at->diffInDays(now()) > 0)
                            <dt class="col-6">{{ __('Días pendiente:') }}</dt>
                            <dd class="col-6">
                                <span class="badge badge-warning">
                                    {{ $work->updated_at->diffInDays(now()) }} {{ __('días') }}
                                </span>
                            </dd>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Acciones de VIEX -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Acciones de VIEX') }}</h3>
                    </div>
                    <div class="card-body">
                        @php
                        $currentStatus = $work->currentStatus->name ?? '';
                        @endphp

                        @if(in_array($currentStatus, ['Enviado a VIEX', 'En VIEX - En Evaluación']))
                        <x-adminlte-alert theme="warning" icon="fa-exclamation-triangle" title="Trabajo pendiente de evaluación">
                            Este trabajo requiere evaluación final por parte de VIEX.
                        </x-adminlte-alert>

                        <!-- Acciones directas de aprobación/certificación/rechazo -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-success btn-block" data-toggle="modal"
                                data-target="#approveCertifyModal">
                                <i class="fas fa-check mr-2"></i>
                                {{ __('Aprobar y Certificar') }}
                            </button>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-warning btn-block" data-toggle="modal"
                                data-target="#requestChangesModal">
                                <i class="fas fa-edit mr-2"></i>
                                {{ __('Devolver para Corrección') }}
                            </button>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-danger btn-block" data-toggle="modal"
                                data-target="#rejectModal">
                                <i class="fas fa-times mr-2"></i>
                                {{ __('Rechazar Trabajo') }}
                            </button>
                        </div>

                        <hr>

                        <small class="text-muted">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Nota:</strong> La aprobación genera automáticamente la certificación oficial válida por 2 años.
                        </small>

                        @elseif($currentStatus === 'En VIEX - Aprobado')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>Trabajo aprobado</strong><br>
                            Proceda a generar la certificación oficial.
                        </div>

                        <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                            data-target="#certifyModal">
                            <i class="fas fa-certificate mr-2"></i>
                            {{ __('Generar Certificación') }}
                        </button>

                        @elseif($currentStatus === 'Certificado')
                        <x-adminlte-alert theme="success" icon="fa-certificate" title="Trabajo certificado">
                            Este trabajo ha sido certificado oficialmente.
                        </x-adminlte-alert>

                        @elseif($currentStatus === 'Rechazado por VIEX')
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle mr-2"></i>
                            <strong>Trabajo rechazado</strong><br>
                            Este trabajo fue rechazado por VIEX.
                        </div>

                        @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>No requiere acción</strong><br>
                            Este trabajo ya fue procesado.
                        </div>

                        <p><strong>Estado actual:</strong>
                            <span class="badge badge-info">{{ $currentStatus }}</span>
                        </p>
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
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Atención:</strong> Esta acción rechazará el trabajo de forma definitiva.
                    </div>

                    <div class="form-group">
                        <label for="rejection_reason">{{ __('Motivo del Rechazo') }} <span class="text-danger">*</span></label>
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
</div>

<!-- Modal de Solicitar Cambios -->
<div class="modal fade" id="requestChangesModal" tabindex="-1" role="dialog" aria-labelledby="requestChangesModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="requestChangesModalLabel">{{ __('Devolver para Corrección') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('viex.request-changes', $work) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-edit mr-2"></i>
                        <strong>Devolución para corrección</strong><br>
                        El trabajo regresará al profesor para que realice las correcciones solicitadas.
                    </div>

                    <div class="form-group">
                        <label for="change_comments">{{ __('Observaciones requeridas') }} <span class="text-danger">*</span></label>
                        <textarea name="comments" id="change_comments" rows="4" class="form-control" required
                            placeholder="{{ __('Describa detalladamente los cambios que el profesor debe realizar...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-edit mr-2"></i>
                        {{ __('Devolver para Corrección') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Generar Certificación -->
<div class="modal fade" id="certifyModal" tabindex="-1" role="dialog" aria-labelledby="certifyModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title" id="certifyModalLabel">{{ __('Generar Certificación') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('viex.certify', $work) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="fas fa-certificate mr-2"></i>
                        <strong>Generación de Certificación Oficial</strong><br>
                        Se generará una certificación oficial válida por el período seleccionado.
                    </div>

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
                                <label for="certification_number">{{ __('Número de Certificación (opcional)') }}</label>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-certificate mr-2"></i>
                        {{ __('Generar Certificación') }}
                    </button>
                </div>
            </form>
        </div>
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

        // Validación del modal de solicitar cambios
        $('#requestChangesModal form').on('submit', function(e) {
            const comments = $('#change_comments').val().trim();

            if (comments.length < 10) {
                e.preventDefault();
                alert('Por favor, especifique las correcciones con al menos 10 caracteres para orientar al profesor.');
                return false;
            }

            if (!confirm('¿Está seguro de que desea devolver este trabajo para corrección?')) {
                e.preventDefault();
                return false;
            }

            // Deshabilitar botón
            const $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true);
            $submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...');
        });

        // Validación del modal de certificación
        $('#certifyModal form').on('submit', function(e) {
            if (!confirm('¿Está seguro de que desea generar la certificación oficial?')) {
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
                if ($(this).hasClass('btn-success')) {
                    return '<i class="fas fa-certificate mr-1"></i>Confirmar Aprobación y Certificación';
                } else if ($(this).hasClass('btn-warning')) {
                    return '<i class="fas fa-edit mr-2"></i>Devolver para Corrección';
                } else if ($(this).hasClass('btn-danger')) {
                    return '<i class="fas fa-times mr-2"></i>Rechazar Trabajo';
                } else if ($(this).hasClass('btn-primary')) {
                    return '<i class="fas fa-certificate mr-2"></i>Generar Certificación';
                }
            });
        });
    });
</script>
@endpush