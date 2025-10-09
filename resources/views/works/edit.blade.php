@extends('adminlte::page')

@section('title', 'Editar Trabajo de Extensión - VIEX')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fas fa-edit text-primary"></i>
            {{ __('Editar Trabajo de Extensión') }}
        </h1>
        <small class="text-muted">{{ $work->title }}</small>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">
                <a href="{{ route('works.index') }}">
                    <i class="fas fa-briefcase"></i> {{ __('Trabajos') }}
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('works.show', $work) }}">{{ $work->title }}</a>
            </li>
            <li class="breadcrumb-item active">{{ __('Editar') }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
@php
$workTypesConfig = $config;
$typeToSections = $workTypesConfig['type_to_sections'] ?? ($workTypesConfig['work_type_sections'] ?? []);
$responsibleName = optional($work->responsibleUser)->name
?? optional($work->primaryResponsible)->name
?? optional(auth()->user())->name;
@endphp
<div class="container-fluid">
    <div class="row">
        <!-- Columna Principal (Formulario) -->
        <div class="col-lg-9">

            <!-- Mensajes Flash -->
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('warning') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6><i class="fas fa-ban"></i> {{ __('Por favor corrige los siguientes errores:') }}</h6>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-info"></i> {{ __('Formulario Oficial de Trabajo de Extensión') }}</h5>
                {{ __('Complete todos los campos requeridos. Las secciones se habilitarán automáticamente según el tipo de trabajo seleccionado.') }}
            </div>

            <form method="POST" action="{{ route('works.update', $work) }}" id="workForm" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <!-- Sección 1: Información General -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            {{ __('1. Información General del Trabajo') }}
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="work_type_id">
                                        <strong>{{ __('Tipo de Trabajo de Extensión') }}</strong> <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control select2 @error('work_type_id') is-invalid @enderror"
                                        id="work_type_id" name="work_type_id" required>
                                        <option disabled value="">{{ __('Seleccione el tipo...') }}</option>
                                        @foreach($workTypes as $workType)
                                        <option value="{{ $workType->id }}"
                                            data-sections="{{ $typeToSections[$workType->id] ?? '' }}"
                                            @selected(old('work_type_id', $work->work_type_id) == $workType->id)>
                                            {{ $workType->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        {{ __('Esta selección determinará qué secciones del formulario se mostrarán.') }}
                                    </small>
                                    @error('work_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="organizational_unit_id">
                                        <strong>{{ __('Unidad Organizacional') }}</strong> <span class="text-danger">*</span>
                                    </label>
                                    @php
                                    $selectedUnitId = old('organizational_unit_id', $work->organizational_unit_id);
                                    @endphp
                                    <select class="form-control select2 @error('organizational_unit_id') is-invalid @enderror"
                                        id="organizational_unit_id" name="organizational_unit_id" required>
                                        <option value="">{{ __('Seleccione la unidad...') }}</option>
                                        @foreach($organizationalUnits as $type => $units)
                                        <optgroup label="{{ ucfirst($type) }}">
                                            @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ $selectedUnitId == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        {{ __('Por defecto se muestra la unidad organizacional registrada en el trabajo.') }}
                                    </small>
                                    @error('organizational_unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="title">
                                <strong>{{ __('Título del Trabajo de Extensión') }}</strong> <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                id="title" name="title" required maxlength="500"
                                value="{{ old('title', $work->title) }}"
                                placeholder="{{ __('Escriba el título completo del trabajo de extensión') }}">
                            <small class="form-text text-muted">
                                {{ __('Mínimo 10 caracteres, máximo 500 caracteres. Sea descriptivo y específico.') }}
                            </small>
                            @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">
                                <strong>{{ __('Resumen del Trabajo') }}</strong> <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                id="description" name="description" rows="4" required maxlength="2000"
                                placeholder="{{ __('Describa brevemente el trabajo, sus objetivos principales, metodología y resultados esperados...') }}">{{ old('description', $work->description) }}</textarea>
                            <small class="form-text text-muted">
                                <span id="char-count">{{ __('Mínimo 50 caracteres. Incluya objetivos, metodología y resultados esperados.') }}</span>
                            </small>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">
                                        <strong>{{ __('Fecha de Inicio') }}</strong> <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date" required
                                        value="{{ old('start_date', optional($work->start_date)->format('Y-m-d')) }}">
                                    <small class="form-text text-muted">
                                        {{ __('Fecha programada de inicio de actividades.') }}
                                    </small>
                                    @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">
                                        <strong>{{ __('Fecha de Finalización') }}</strong> <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date" required
                                        value="{{ old('end_date', optional($work->end_date)->format('Y-m-d')) }}">
                                    <small class="form-text text-muted">
                                        {{ __('Fecha programada de finalización de actividades.') }}
                                    </small>
                                    @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="academic_period">
                                        <strong>{{ __('Período Académico') }}</strong> <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('academic_period') is-invalid @enderror"
                                        id="academic_period" name="academic_period" required readonly
                                        value="{{ old('academic_period', $work->academic_period) }}"
                                        placeholder="{{ __('Se calculará automáticamente...') }}">
                                    <small class="form-text text-muted">
                                        {{ __('Se calcula automáticamente basado en las fechas de inicio y finalización del trabajo.') }}
                                    </small>
                                    @error('academic_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="responsible_name">
                                        <strong>{{ __('Responsable Principal') }}</strong> <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="responsible_name" name="responsible_name"
                                        value="{{ $responsibleName }}" readonly>
                                    <small class="form-text text-muted">
                                        {{ __('Este campo se completa automáticamente con el usuario responsable registrado.') }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="responsible_phone">
                                        <strong>{{ __('Teléfono de Contacto') }}</strong>
                                    </label>
                                    <input type="tel" class="form-control @error('responsible_phone') is-invalid @enderror"
                                        id="responsible_phone" name="responsible_phone"
                                        value="{{ old('responsible_phone', $work->responsible_phone) }}"
                                        placeholder="{{ __('Ej: +507 6888-8888') }}">
                                    @error('responsible_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="publication_consent"
                                    name="publication_consent" value="1" {{ old('publication_consent', $work->publication_consent) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="publication_consent">
                                    <strong>{{ __('Autorizo a la Universidad a publicar información sobre este trabajo') }}</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                {{ __('Al marcar esta opción, autoriza a la institución a divulgar información del trabajo en medios oficiales.') }}
                            </small>
                        </div>
                    </div>
                </div>

                @include('works.partials.project-section', [
                'projectDetail' => $work->projectDetail,
                'workTypesConfig' => $workTypesConfig,
                ])
                @include('works.partials.activity-section', [
                'activityDetail' => $work->activityDetail,
                'workTypesConfig' => $workTypesConfig,
                ])
                @include('works.partials.publication-section', [
                'publicationDetail' => $work->publicationDetail,
                'workTypesConfig' => $workTypesConfig,
                ])
                @include('works.partials.assistance-section', [
                'technicalDetail' => $work->technicalAssistanceDetail,
                'workTypesConfig' => $workTypesConfig,
                ])

                @include('works.partials.attachments-edit')

                <div class="card card-outline card-primary">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="{{ route('works.show', $work) }}" class="btn btn-default btn-lg">
                                    <i class="fas fa-times"></i> {{ __('Cancelar') }}
                                </a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-save"></i> {{ __('Actualizar Trabajo') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Columna Lateral (Información de Subsanación/Rechazo) -->
        @include('works.partials.edit-sidebar')
    </div>
</div>
@stop

@section('css')
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

<style>
    .form-group label {
        font-weight: 600;
    }

    .card-header .card-title {
        font-weight: bold;
    }

    .text-danger {
        font-weight: bold;
    }

    .custom-file-label::after {
        content: "Buscar";
    }

    .alert-light {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .form-control:focus,
    .select2-container--bootstrap4 .select2-selection:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .card-primary .card-header {
        background: linear-gradient(45deg, #007bff, #0056b3);
    }

    .card-info .card-header {
        background: linear-gradient(45deg, #17a2b8, #117a8b);
    }

    .card-secondary .card-header {
        background: linear-gradient(45deg, #6c757d, #545b62);
    }

    .card-warning .card-header {
        background: linear-gradient(45deg, #ffc107, #e0a800);
    }

    @media (max-width: 768px) {
        .btn-lg {
            width: 100%;
            margin-bottom: 10px;
        }
    }

    .upload-zone {
        border: 2px dashed #ddd;
        border-radius: 10px;
        padding: 40px 20px;
        text-align: center;
        background-color: #fafafa;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .upload-zone:hover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }

    .upload-zone.dragover {
        border-color: #007bff;
        background-color: #e3f2fd;
        transform: scale(1.02);
    }

    .upload-zone-content i {
        display: block;
        margin-bottom: 15px;
    }

    .files-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .file-item {
        background-color: #f8f9fa;
        transition: all 0.2s ease;
    }

    .file-item:hover {
        background-color: #e9ecef;
        transform: translateX(5px);
    }

    .file-name {
        max-width: 300px;
        word-break: break-word;
    }

    .document-category {
        margin-top: 15px;
    }

    .remove-file:hover {
        transform: scale(1.1);
    }

    @media (max-width: 768px) {
        .file-item {
            flex-direction: column;
            text-align: center;
        }

        .file-info {
            margin-bottom: 10px;
        }

        .file-name {
            max-width: 100%;
        }
    }
</style>
@stop

@section('js')
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
@include('works.partials.form-js')
@stop