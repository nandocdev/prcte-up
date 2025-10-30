@extends('layouts.app')

@section('title', __('Crear Tipo de Trabajo'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus mr-2"></i>{{ __('Crear Tipo de Trabajo') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.index') }}">{{ __('Catálogos') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.work-types') }}">{{ __('Tipos de Trabajo') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Crear') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">{{ __('Nuevo Tipo de Trabajo') }}</h3>
            </div>
            
            <form action="{{ route('admin.catalogs.work-types.store') }}" method="POST" id="create-work-type-form">
                @csrf
                
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               required
                               placeholder="{{ __('ej: Proyecto de Investigación') }}">
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('Descripción') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="{{ __('Descripción detallada del tipo de trabajo de extensión...') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">
                            {{ __('Proporcione una descripción clara que ayude a los usuarios a identificar cuándo usar este tipo.') }}
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="icheck-primary">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active">{{ __('Activo') }}</label>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('Los tipos inactivos no aparecerán en los formularios de creación de trabajos.') }}
                        </small>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.catalogs.work-types') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Volver') }}
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> {{ __('Crear Tipo') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Información de Ayuda -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Tipos de Trabajo Comunes') }}</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-project-diagram text-primary"></i>
                                <strong>{{ __('Proyecto') }}</strong> - {{ __('Iniciativas institucionales o de unidades académicas') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-calendar-alt text-success"></i>
                                <strong>{{ __('Actividad') }}</strong> - {{ __('Eventos de educación continua o intervenciones') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-book text-warning"></i>
                                <strong>{{ __('Publicación') }}</strong> - {{ __('Artículos, libros que generen conocimiento') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-handshake text-info"></i>
                                <strong>{{ __('Asistencia Técnica') }}</strong> - {{ __('Asesorías y consultorías especializadas') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Recomendaciones') }}</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-lightbulb text-warning"></i>
                                {{ __('Use nombres claros y específicos') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-users text-info"></i>
                                {{ __('Considere el uso que le darán los profesores') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-balance-scale text-success"></i>
                                {{ __('Evite duplicar tipos similares') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-file-alt text-primary"></i>
                                {{ __('La descripción debe ser informativa') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
$(document).ready(function() {
    // Validación del formulario antes del envío
    $('#create-work-type-form').on('submit', function(e) {
        const submitBtn = $('#submit-btn');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> {{ __("Creando...") }}');
    });
    
    // Auto-capitalizar la primera letra
    $('#name').on('input', function() {
        let value = $(this).val();
        if (value.length === 1) {
            $(this).val(value.charAt(0).toUpperCase());
        }
    });
    
    // Contador de caracteres para la descripción
    $('#description').on('input', function() {
        const current = $(this).val().length;
        const max = 1000; // Asumiendo un límite máximo
        const remaining = max - current;
        
        let helpText = $(this).siblings('.form-text');
        if (current > 0) {
            helpText.html(`{{ __('Proporcione una descripción clara que ayude a los usuarios a identificar cuándo usar este tipo.') }} <span class="float-right text-muted">${current}/${max}</span>`);
        }
        
        if (remaining < 100) {
            helpText.addClass('text-warning');
        } else {
            helpText.removeClass('text-warning');
        }
        
        if (remaining < 0) {
            helpText.addClass('text-danger').removeClass('text-warning');
        } else {
            helpText.removeClass('text-danger');
        }
    });
});
</script>
@endpush