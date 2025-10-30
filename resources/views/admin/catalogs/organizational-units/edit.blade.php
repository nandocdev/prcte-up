@extends('layouts.app')

@section('title', __('Editar Unidad Organizacional'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-edit mr-2"></i>{{ __('Editar Unidad Organizacional') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.index') }}">{{ __('Catálogos') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.organizational-units') }}">{{ __('Unidades Organizacionales') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.organizational-units.show', $unit) }}">{{ $unit->name }}</a></li>
                <li class="breadcrumb-item active">{{ __('Editar') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">{{ __('Información de la Unidad') }}</h3>
            </div>
            
            <form action="{{ route('admin.catalogs.organizational-units.update', $unit) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $unit->name) }}"
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
                                       value="{{ old('code', $unit->code) }}"
                                       required
                                       placeholder="{{ __('ej: FAC-ING') }}">
                                @error('code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">{{ __('Tipo') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">{{ __('Seleccionar tipo') }}</option>
                                    <option value="universidad" {{ old('type', $unit->type) === 'universidad' ? 'selected' : '' }}>{{ __('Universidad') }}</option>
                                    <option value="facultad" {{ old('type', $unit->type) === 'facultad' ? 'selected' : '' }}>{{ __('Facultad') }}</option>
                                    <option value="centro" {{ old('type', $unit->type) === 'centro' ? 'selected' : '' }}>{{ __('Centro') }}</option>
                                    <option value="departamento" {{ old('type', $unit->type) === 'departamento' ? 'selected' : '' }}>{{ __('Departamento') }}</option>
                                    <option value="escuela" {{ old('type', $unit->type) === 'escuela' ? 'selected' : '' }}>{{ __('Escuela') }}</option>
                                    <option value="instituto" {{ old('type', $unit->type) === 'instituto' ? 'selected' : '' }}>{{ __('Instituto') }}</option>
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
                                        @if($option['id'] != $unit->id && !in_array($option['id'], $unit->getDescendantIds()))
                                            <option value="{{ $option['id'] }}" {{ old('parent_id', $unit->parent_id) == $option['id'] ? 'selected' : '' }}>
                                                {{ $option['display'] }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    {{ __('No se pueden seleccionar la misma unidad o sus descendientes como padre.') }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('Descripción') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="{{ __('Descripción de la unidad organizacional...') }}">{{ old('description', $unit->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="icheck-primary">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   {{ old('is_active', $unit->is_active) ? 'checked' : '' }}>
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
                            <a href="{{ route('admin.catalogs.organizational-units.show', $unit) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('Volver') }}
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> {{ __('Guardar Cambios') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Información Adicional -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Impacto de los Cambios') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-primary">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <h5 class="description-header">{{ $unit->users->count() }}</h5>
                                    <span class="description-text">{{ __('Usuarios Afectados') }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <span class="description-percentage text-warning">
                                        <i class="fas fa-sitemap"></i>
                                    </span>
                                    <h5 class="description-header">{{ $unit->children->count() }}</h5>
                                    <span class="description-text">{{ __('Subunidades') }}</span>
                                </div>
                            </div>
                        </div>

                        @if($unit->users->count() > 0 || $unit->children->count() > 0)
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ __('Los cambios afectarán a los usuarios y subunidades asociadas.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Validaciones') }}</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                {{ __('El código debe ser único en el sistema') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                {{ __('No se puede asignar como padre a sí misma') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success"></i>
                                {{ __('No se puede asignar un descendiente como padre') }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-info-circle text-info"></i>
                                {{ __('Los cambios se aplicarán inmediatamente') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if($unit->children->count() === 0 && $unit->users->count() === 0)
        <!-- Zona de Peligro -->
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">{{ __('Zona de Peligro') }}</h3>
            </div>
            <div class="card-body">
                <p>{{ __('Las siguientes acciones son irreversibles y pueden afectar el funcionamiento del sistema.') }}</p>
                
                <button type="button" 
                        class="btn btn-danger" 
                        onclick="confirmDelete('{{ $unit->id }}', '{{ $unit->name }}')">
                    <i class="fas fa-trash"></i> {{ __('Eliminar Unidad') }}
                </button>
                
                <form id="delete-form-{{ $unit->id }}" 
                      action="{{ route('admin.catalogs.organizational-units.destroy', $unit) }}" 
                      method="POST" 
                      class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@stop

@push('scripts')
<script>
function confirmDelete(unitId, unitName) {
    Swal.fire({
        title: '¿Estás seguro?',
        html: `
            <p>Estás a punto de eliminar la unidad organizacional <strong>${unitName}</strong>.</p>
            <p>Esta acción no se puede deshacer.</p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${unitId}`).submit();
        }
    });
}

// Validación en tiempo real
$(document).ready(function() {
    $('#code').on('input', function() {
        const code = $(this).val();
        if (code.length > 0) {
            // Aquí podrías agregar validación AJAX para verificar unicidad
        }
    });
});
</script>
@endpush