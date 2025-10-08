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
    <div class="container-fluid">

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

        <!-- Formulario Principal -->
        <form action="{{ route('works.update', $work) }}" method="POST" enctype="multipart/form-data" id="workForm">
            @csrf
            @method('PATCH')

            <!-- Card Principal -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        {{ __('Información del Trabajo de Extensión') }}
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-info">{{ $work->workType->name ?? 'Tipo no definido' }}</span>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Tipo de Trabajo -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="work_type_id" class="required">
                                    <i class="fas fa-layer-group"></i>
                                    {{ __('Tipo de Trabajo') }}
                                </label>
                                <select name="work_type_id" id="work_type_id"
                                        class="form-control select2 @error('work_type_id') is-invalid @enderror"
                                        required data-placeholder="{{ __('Selecciona el tipo...') }}">
                                    <option value="">{{ __('Selecciona el tipo de trabajo...') }}</option>
                                    @foreach($workTypes as $workType)
                                        <option value="{{ $workType->id }}"
                                                @selected(old('work_type_id', $work->work_type_id) == $workType->id)
                                                data-sections="{{ $config['type_to_sections'][$workType->id] ?? '' }}"
                                                data-description="{{ $workType->description }}">
                                            {{ $workType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('work_type_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    {{ __('Selecciona el tipo de trabajo que deseas registrar') }}
                                </small>
                            </div>
                        </div>

                        <!-- Unidad Organizacional -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="organizational_unit_id" class="required">
                                    <i class="fas fa-university"></i>
                                    {{ __('Unidad Organizacional') }}
                                </label>
                                <select name="organizational_unit_id" id="organizational_unit_id"
                                        class="form-control select2 @error('organizational_unit_id') is-invalid @enderror"
                                        required data-placeholder="{{ __('Selecciona la unidad...') }}">
                                    <option value="">{{ __('Selecciona la unidad organizacional...') }}</option>
                                    @foreach($organizationalUnits as $type => $units)
                                        <optgroup label="{{ ucfirst($type) }}">
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}"
                                                        @selected(old('organizational_unit_id', $work->organizational_unit_id) == $unit->id)>
                                                    {{ $unit->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('organizational_unit_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Título -->
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="title" class="required">
                                    <i class="fas fa-heading"></i>
                                    {{ __('Título del Trabajo') }}
                                </label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $work->title) }}"
                                       required
                                       maxlength="500"
                                       placeholder="{{ __('Ingresa el título del trabajo...') }}">
                                @error('title')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    <span id="title-counter">{{ strlen(old('title', $work->title ?? '')) }}</span>/500 caracteres
                                </small>
                            </div>
                        </div>

                        <!-- Período Académico -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="academic_period" class="required">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ __('Período Académico') }}
                                </label>
                                <select name="academic_period" id="academic_period"
                                        class="form-control @error('academic_period') is-invalid @enderror" required>
                                    @foreach($periods as $period)
                                        <option value="{{ $period }}"
                                                @selected(old('academic_period', $work->academic_period) == $period)>
                                            {{ $period }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_period')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Descripción -->
                        <div class="col-12">
                            <div class="form-group">
                                <label for="description" class="required">
                                    <i class="fas fa-align-left"></i>
                                    {{ __('Descripción del Trabajo') }}
                                </label>
                                <textarea name="description"
                                          id="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          rows="4"
                                          required
                                          placeholder="{{ __('Describe los objetivos, alcance y metodología del trabajo...') }}">{{ old('description', $work->description) }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    {{ __('Proporciona una descripción detallada del trabajo (mínimo 50 caracteres)') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Fecha de Inicio -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="start_date" class="required">
                                    <i class="fas fa-calendar-plus"></i>
                                    {{ __('Fecha de Inicio') }}
                                </label>
                                <input type="date"
                                       name="start_date"
                                       id="start_date"
                                       class="form-control @error('start_date') is-invalid @enderror"
                                       value="{{ old('start_date', optional($work->start_date)->format('Y-m-d')) }}"
                                       required>
                                @error('start_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Fecha de Finalización -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="end_date" class="required">
                                    <i class="fas fa-calendar-minus"></i>
                                    {{ __('Fecha de Finalización') }}
                                </label>
                                <input type="date"
                                       name="end_date"
                                       id="end_date"
                                       class="form-control @error('end_date') is-invalid @enderror"
                                       value="{{ old('end_date', optional($work->end_date)->format('Y-m-d')) }}"
                                       required>
                                @error('end_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Teléfono del Responsable -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="responsible_phone">
                                    <i class="fas fa-phone"></i>
                                    {{ __('Teléfono del Responsable') }}
                                </label>
                                <input type="tel"
                                       name="responsible_phone"
                                       id="responsible_phone"
                                       class="form-control @error('responsible_phone') is-invalid @enderror"
                                       value="{{ old('responsible_phone', $work->responsible_phone) }}"
                                       placeholder="{{ __('Ej: 507-1234-5678') }}">
                                @error('responsible_phone')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Consentimiento de Publicación -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class="custom-control-input"
                                           id="publication_consent"
                                           name="publication_consent"
                                           value="1"
                                           @checked(old('publication_consent', $work->publication_consent))>
                                    <label class="custom-control-label" for="publication_consent">
                                        <i class="fas fa-globe"></i>
                                        {{ __('Autorizo la publicación de este trabajo en el portal institucional') }}
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i>
                                    {{ __('Al marcar esta casilla, permites que tu trabajo sea visible públicamente (opcional)') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                        {{-- Secciones dinámicas según el tipo de trabajo --}}
            @include('works.partials.dynamic-sections-edit')

            {{-- Archivos adjuntos con gestión de existentes --}}
            @include('works.partials.attachments-edit')

            <!-- Botones de Acción -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save"></i>
                                        {{ __('Actualizar Trabajo') }}
                                    </button>
                                    <a href="{{ route('works.show', $work) }}" class="btn btn-secondary btn-lg ml-2">
                                        <i class="fas fa-times"></i>
                                        {{ __('Cancelar') }}
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i>
                                        {{ __('Los campos marcados con') }} <span class="text-danger">*</span> {{ __('son obligatorios') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@stop

<!-- Include the same JavaScript as create form -->
@include('works.partials.form-js')

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

    <style>
        .required:after {
            content: " *";
            color: red;
        }

        .work-type-section {
            display: none;
            animation: fadeIn 0.3s ease-in-out;
        }

        .work-type-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .file-drop-zone {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s ease;
            background: #f8f9fa;
            cursor: pointer;
        }

        .file-drop-zone:hover,
        .file-drop-zone.dragover {
            border-color: #0056b3;
            background: #e3f2fd;
            transform: scale(1.02);
        }
    </style>
@stop

@section('js')
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        // Initialize form with existing data
        $(document).ready(function() {
            // Get current work type and show appropriate section
            const currentWorkType = '{{ $work->work_type_id }}';
            if (currentWorkType) {
                showWorkTypeSection(currentWorkType);
            }
        });
    </script>
@stop
