@extends('layouts.app')

@section('title', __('Editar Tipo de Trabajo'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-edit mr-2"></i>{{ __('Editar Tipo de Trabajo') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.index') }}">{{ __('Catálogos') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.work-types') }}">{{ __('Tipos de Trabajo') }}</a></li>
                <li class="breadcrumb-item active">{{ $workType->name }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">{{ __('Editar Información del Tipo') }}</h3>
            </div>
            
            <form action="{{ route('admin.catalogs.work-types.update', $workType) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $workType->name) }}"
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
                                  placeholder="{{ __('Descripción detallada del tipo de trabajo de extensión...') }}">{{ old('description', $workType->description) }}</textarea>
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
                                   {{ old('is_active', $workType->is_active) ? 'checked' : '' }}>
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
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> {{ __('Guardar Cambios') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Información de Uso -->
        <div class="row">
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Estadísticas de Uso') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-12">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-primary">
                                        <i class="fas fa-chart-bar"></i>
                                    </span>
                                    <h5 class="description-header">{{ $workType->works_count ?? 0 }}</h5>
                                    <span class="description-text">{{ __('Trabajos de Extensión') }}</span>
                                </div>
                            </div>
                        </div>

                        @if(($workType->works_count ?? 0) > 0)
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i>
                                {{ __('Este tipo está siendo utilizado activamente.') }}
                            </div>
                        @else
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ __('Este tipo no ha sido utilizado aún.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Información del Sistema') }}</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-5">{{ __('Creado:') }}</dt>
                            <dd class="col-sm-7">{{ $workType->created_at->format('d/m/Y H:i') }}</dd>
                            
                            <dt class="col-sm-5">{{ __('Actualizado:') }}</dt>
                            <dd class="col-sm-7">{{ $workType->updated_at->format('d/m/Y H:i') }}</dd>
                            
                            <dt class="col-sm-5">{{ __('Estado Actual:') }}</dt>
                            <dd class="col-sm-7">
                                @if($workType->is_active)
                                    <span class="badge badge-success">{{ __('Activo') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Inactivo') }}</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        @if(($workType->works_count ?? 0) === 0)
        <!-- Zona de Peligro -->
        <div class="card card-danger">
            <div class="card-header">
                <h3 class="card-title">{{ __('Zona de Peligro') }}</h3>
            </div>
            <div class="card-body">
                <p>{{ __('Las siguientes acciones son irreversibles y pueden afectar el funcionamiento del sistema.') }}</p>
                
                <button type="button" 
                        class="btn btn-danger" 
                        onclick="confirmDelete('{{ $workType->id }}', '{{ $workType->name }}')">
                    <i class="fas fa-trash"></i> {{ __('Eliminar Tipo') }}
                </button>
                
                <form id="delete-form-{{ $workType->id }}" 
                      action="{{ route('admin.catalogs.work-types.destroy', $workType) }}" 
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
// URLs para JavaScript
const workTypeUrls = {
    index: '{{ route("admin.catalogs.work-types") }}',
    destroy: '{{ url("admin/catalogs/work-types") }}'
};

function confirmDelete(typeId, typeName) {
    Swal.fire({
        title: '¿Estás seguro?',
        html: `
            <p>Estás a punto de eliminar el tipo de trabajo <strong>${typeName}</strong>.</p>
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
            // Envío por AJAX para mantener la interfaz
            $.ajax({
                url: `${workTypeUrls.destroy}/${typeId}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Eliminado',
                            text: response.message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = workTypeUrls.index;
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON.message || 'Error al eliminar el tipo',
                        icon: 'error'
                    });
                }
            });
        }
    });
}

$(document).ready(function() {
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
        const max = 1000;
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