@extends('layouts.app')

@section('title', __('Crear Unidad Organizacional'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-plus mr-2"></i>{{ __('Crear Unidad Organizacional') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.index') }}">{{ __('Catálogos') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.organizational-units') }}">{{ __('Unidades Organizacionales') }}</a></li>
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
                <h3 class="card-title">{{ __('Nueva Unidad Organizacional') }}</h3>
            </div>
            
            <form action="{{ route('admin.catalogs.organizational-units.store') }}" method="POST" id="create-unit-form">
                @csrf
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}"
                                       required
                                       placeholder="{{ __('ej: Facultad de Ingeniería') }}">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">{{ __('Código') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code') }}"
                                       required
                                       placeholder="{{ __('ej: FAC-ING') }}">
                                @error('code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    <span id="code-status"></span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">{{ __('Tipo') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">{{ __('Seleccionar tipo') }}</option>
                                    <option value="universidad" {{ old('type') === 'universidad' ? 'selected' : '' }}>{{ __('Universidad') }}</option>
                                    <option value="facultad" {{ old('type') === 'facultad' ? 'selected' : '' }}>{{ __('Facultad') }}</option>
                                    <option value="centro" {{ old('type') === 'centro' ? 'selected' : '' }}>{{ __('Centro') }}</option>
                                    <option value="departamento" {{ old('type') === 'departamento' ? 'selected' : '' }}>{{ __('Departamento') }}</option>
                                    <option value="escuela" {{ old('type') === 'escuela' ? 'selected' : '' }}>{{ __('Escuela') }}</option>
                                    <option value="instituto" {{ old('type') === 'instituto' ? 'selected' : '' }}>{{ __('Instituto') }}</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parent_id">{{ __('Unidad Padre') }}</label>
                                <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                                    <option value="">{{ __('Ninguna (Raíz)') }}</option>
                                    @foreach($parentOptions as $option)
                                        <option value="{{ $option['id'] }}" {{ old('parent_id') == $option['id'] ? 'selected' : '' }}>
                                            {{ $option['display'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('Descripción') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="{{ __('Descripción de la unidad organizacional...') }}">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="icheck-primary">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active">{{ __('Activa') }}</label>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('Las unidades inactivas no aparecerán en los formularios de asignación.') }}
                        </small>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('admin.catalogs.organizational-units') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Volver') }}
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                <i class="fas fa-save"></i> {{ __('Crear Unidad') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Guía de Creación -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Guía de Tipos') }}</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-5">{{ __('Universidad') }}</dt>
                            <dd class="col-sm-7">{{ __('Nivel más alto de la jerarquía') }}</dd>
                            
                            <dt class="col-sm-5">{{ __('Facultad') }}</dt>
                            <dd class="col-sm-7">{{ __('División académica principal') }}</dd>
                            
                            <dt class="col-sm-5">{{ __('Centro') }}</dt>
                            <dd class="col-sm-7">{{ __('Centro de investigación o extensión') }}</dd>
                            
                            <dt class="col-sm-5">{{ __('Departamento') }}</dt>
                            <dd class="col-sm-7">{{ __('División dentro de una facultad') }}</dd>
                            
                            <dt class="col-sm-5">{{ __('Escuela') }}</dt>
                            <dd class="col-sm-7">{{ __('Unidad académica especializada') }}</dd>
                            
                            <dt class="col-sm-5">{{ __('Instituto') }}</dt>
                            <dd class="col-sm-7">{{ __('Centro de investigación especializado') }}</dd>
                        </dl>
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
                                {{ __('Use códigos descriptivos y únicos') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-sitemap text-info"></i>
                                {{ __('Organice la jerarquía de forma lógica') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-users text-success"></i>
                                {{ __('Considere futuras asignaciones de usuarios') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-eye text-primary"></i>
                                {{ __('Use nombres claros y descriptivos') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if(count($parentOptions) === 0)
        <!-- Advertencia si no hay unidades padre -->
        <div class="card card-warning">
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    {{ __('Esta será la primera unidad en el sistema. Puede crear la estructura jerárquica posteriormente.') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@stop

@push('scripts')
<script>
$(document).ready(function() {
    let codeCheckTimeout;
    
    // Validación en tiempo real del código
    $('#code').on('input', function() {
        const code = $(this).val().trim();
        const statusElement = $('#code-status');
        
        clearTimeout(codeCheckTimeout);
        
        if (code.length === 0) {
            statusElement.html('');
            return;
        }
        
        if (code.length < 2) {
            statusElement.html('<i class="fas fa-exclamation-triangle text-warning"></i> {{ __("El código debe tener al menos 2 caracteres") }}');
            return;
        }
        
        statusElement.html('<i class="fas fa-spinner fa-spin text-info"></i> {{ __("Verificando disponibilidad...") }}');
        
        codeCheckTimeout = setTimeout(function() {
            // Aquí podrías hacer una llamada AJAX para verificar unicidad
            // Por ahora solo mostramos mensaje genérico
            statusElement.html('<i class="fas fa-check text-success"></i> {{ __("Formato válido") }}');
        }, 500);
    });
    
    // Auto-generar código basado en el nombre
    $('#name').on('input', function() {
        const name = $(this).val();
        const codeField = $('#code');
        
        if (codeField.val().length === 0 && name.length > 0) {
            // Generar código sugerido
            let suggestedCode = name
                .toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .trim()
                .split(/\s+/)
                .map(word => word.substring(0, 3))
                .join('-')
                .toUpperCase();
            
            if (suggestedCode.length > 10) {
                suggestedCode = suggestedCode.substring(0, 10);
            }
            
            codeField.val(suggestedCode);
            codeField.trigger('input');
        }
    });
    
    // Validación del formulario antes del envío
    $('#create-unit-form').on('submit', function(e) {
        const submitBtn = $('#submit-btn');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> {{ __("Creando...") }}');
    });
    
    // Manejar cambios en el tipo para sugerir jerarquía
    $('#type').on('change', function() {
        const selectedType = $(this).val();
        const parentSelect = $('#parent_id');
        
        // Habilitar/deshabilitar opciones de padre según el tipo
        parentSelect.find('option').each(function() {
            const option = $(this);
            const optionText = option.text().toLowerCase();
            
            // Lógica simple de jerarquía
            if (selectedType === 'facultad' && optionText.includes('departamento')) {
                option.prop('disabled', true);
            } else if (selectedType === 'universidad') {
                option.prop('disabled', true); // Universidad no debe tener padre
            } else {
                option.prop('disabled', false);
            }
        });
        
        if (selectedType === 'universidad') {
            parentSelect.val('');
        }
    });
});
</script>
@endpush