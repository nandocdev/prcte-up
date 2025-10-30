@extends('layouts.app')

@section('title', __('Tipos de Trabajo'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-tags mr-2"></i>{{ __('Tipos de Trabajo') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.index') }}">{{ __('Catálogos') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Tipos de Trabajo') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-2"></i>{{ __('Gestión de Tipos de Trabajo') }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.catalogs.work-types.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>{{ __('Nuevo Tipo') }}
                    </a>
                </div>
            </div>

            <div class="card-body">
                <!-- Estadísticas Rápidas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-tags"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Total Tipos') }}</span>
                                <span class="info-box-number">{{ $workTypes->total() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Activos') }}</span>
                                <span class="info-box-number">{{ $workTypes->where('is_active', true)->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-pause"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Inactivos') }}</span>
                                <span class="info-box-number">{{ $workTypes->where('is_active', false)->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-exclamation"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Sin Uso') }}</span>
                                <span class="info-box-number">{{ $workTypes->where('works_count', 0)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros y Búsqueda -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" id="searchWorkTypes" class="form-control" placeholder="{{ __('Buscar tipos de trabajo...') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="statusFilter" class="form-control">
                            <option value="">{{ __('Todos los estados') }}</option>
                            <option value="active">{{ __('Solo activos') }}</option>
                            <option value="inactive">{{ __('Solo inactivos') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="exportWorkTypes()">
                                <i class="fas fa-download"></i> {{ __('Exportar') }}
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="bulkActions()">
                                <i class="fas fa-tasks"></i> {{ __('Acciones Masivas') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Tipos de Trabajo -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="30">
                                    <div class="icheck-primary">
                                        <input type="checkbox" id="selectAll">
                                        <label for="selectAll"></label>
                                    </div>
                                </th>
                                <th>{{ __('Nombre') }}</th>
                                <th>{{ __('Descripción') }}</th>
                                <th>{{ __('Estado') }}</th>
                                <th>{{ __('Trabajos Asociados') }}</th>
                                <th>{{ __('Creado') }}</th>
                                <th>{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($workTypes as $workType)
                                <tr class="work-type-row" data-name="{{ strtolower($workType->name) }}" data-status="{{ $workType->is_active ? 'active' : 'inactive' }}">
                                    <td>
                                        <div class="icheck-primary">
                                            <input type="checkbox" id="check_{{ $workType->id }}" class="item-checkbox" value="{{ $workType->id }}">
                                            <label for="check_{{ $workType->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $workType->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $workType->description ? Str::limit($workType->description, 60) : __('Sin descripción') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($workType->is_active)
                                            <span class="badge badge-success">{{ __('Activo') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('Inactivo') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $workType->works_count ?? 0 }} {{ __('trabajos') }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $workType->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.catalogs.work-types.edit', $workType) }}" class="btn btn-outline-primary" title="{{ __('Editar') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-outline-info" onclick="viewWorkType({{ $workType->id }})" title="{{ __('Ver detalles') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if(($workType->works_count ?? 0) === 0)
                                                <button class="btn btn-outline-danger" onclick="deleteWorkType({{ $workType->id }}, '{{ $workType->name }}')" title="{{ __('Eliminar') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-outline-secondary" disabled title="{{ __('No se puede eliminar: tiene trabajos asociados') }}">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-tags fa-3x mb-3"></i>
                                        <p>{{ __('No hay tipos de trabajo configurados.') }}</p>
                                        <a href="{{ route('admin.catalogs.work-types.create') }}" class="btn btn-primary">
                                            {{ __('Crear el primer tipo') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($workTypes->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $workTypes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Tipo de Trabajo -->
<div class="modal fade" id="createWorkTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createWorkTypeForm">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Crear Nuevo Tipo de Trabajo') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required
                               placeholder="{{ __('ej: Proyecto de Investigación') }}">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('Descripción') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="{{ __('Descripción detallada del tipo de trabajo...') }}"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <div class="icheck-primary">
                            <input type="checkbox" id="is_active" name="is_active" checked>
                            <label for="is_active">{{ __('Activo') }}</label>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('Los tipos inactivos no aparecerán en los formularios de creación.') }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Crear Tipo') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Tipo de Trabajo -->
<div class="modal fade" id="editWorkTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editWorkTypeForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Editar Tipo de Trabajo') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="edit_description">{{ __('Descripción') }}</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <div class="icheck-primary">
                            <input type="checkbox" id="edit_is_active" name="is_active">
                            <label for="edit_is_active">{{ __('Activo') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Guardar Cambios') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
// URLs para JavaScript
const workTypeUrls = {
    index: '{{ route("admin.catalogs.work-types") }}',
    store: '{{ route("admin.catalogs.work-types.store") }}',
    show: '{{ url("admin/catalogs/work-types") }}',
    update: '{{ url("admin/catalogs/work-types") }}',
    destroy: '{{ url("admin/catalogs/work-types") }}'
};

$(document).ready(function() {
    // Búsqueda en tiempo real
    $('#searchWorkTypes').on('input', function() {
        filterWorkTypes();
    });

    // Filtro por estado
    $('#statusFilter').on('change', function() {
        filterWorkTypes();
    });

    // Seleccionar todo
    $('#selectAll').on('change', function() {
        $('.item-checkbox').prop('checked', this.checked);
    });

    // Crear tipo de trabajo
    $('#createWorkTypeForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('is_active', $('#is_active').is(':checked') ? 1 : 0);

        $.ajax({
            url: workTypeUrls.store,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#createWorkTypeModal').modal('hide');
                    toastr.success(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').empty();
                
                Object.keys(errors).forEach(field => {
                    $(`#${field}`).addClass('is-invalid');
                    $(`#${field}`).siblings('.invalid-feedback').text(errors[field][0]);
                });
            }
        });
    });

    // Editar tipo de trabajo
    $('#editWorkTypeForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('is_active', $('#edit_is_active').is(':checked') ? 1 : 0);
        formData.append('_method', 'PUT');

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editWorkTypeModal').modal('hide');
                    toastr.success(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').empty();
                
                Object.keys(errors).forEach(field => {
                    const fieldName = field.startsWith('edit_') ? field : `edit_${field}`;
                    $(`#${fieldName}`).addClass('is-invalid');
                    $(`#${fieldName}`).siblings('.invalid-feedback').text(errors[field][0]);
                });
            }
        });
    });
});

function filterWorkTypes() {
    const searchTerm = $('#searchWorkTypes').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();

    $('.work-type-row').each(function() {
        const $row = $(this);
        const name = $row.data('name');
        const status = $row.data('status');

        let showRow = true;

        // Filtro de búsqueda
        if (searchTerm && !name.includes(searchTerm)) {
            showRow = false;
        }

        // Filtro de estado
        if (statusFilter && status !== statusFilter) {
            showRow = false;
        }

        $row.toggle(showRow);
    });
}

function viewWorkType(id) {
    $.get(`${workTypeUrls.show}/${id}`, function(data) {
        // Crear modal de detalles dinámicamente
        const modalContent = `
            <div class="modal fade" id="viewWorkTypeModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="fas fa-eye mr-2"></i>Detalles del Tipo de Trabajo</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Información General</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Nombre:</strong></td>
                                            <td>${data.name}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estado:</strong></td>
                                            <td>
                                                ${data.is_active 
                                                    ? '<span class="badge badge-success">Activo</span>' 
                                                    : '<span class="badge badge-secondary">Inactivo</span>'
                                                }
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Trabajos Asociados:</strong></td>
                                            <td><span class="badge badge-info">${data.work_of_extensions_count} trabajos</span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Fechas</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Creado:</strong></td>
                                            <td>${new Date(data.created_at).toLocaleDateString('es-ES')}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Actualizado:</strong></td>
                                            <td>${new Date(data.updated_at).toLocaleDateString('es-ES')}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            ${data.description ? `
                                <div class="mt-3">
                                    <h6>Descripción</h6>
                                    <p class="text-muted">${data.description}</p>
                                </div>
                            ` : ''}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" onclick="editWorkType(${data.id})" data-dismiss="modal">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remover modal anterior si existe
        $('#viewWorkTypeModal').remove();
        
        // Agregar nuevo modal al body y mostrarlo
        $('body').append(modalContent);
        $('#viewWorkTypeModal').modal('show');
        
        // Limpiar cuando se cierre
        $('#viewWorkTypeModal').on('hidden.bs.modal', function() {
            $(this).remove();
        });
    });
}

function editWorkType(id) {
    $.get(`${workTypeUrls.show}/${id}`, function(data) {
        $('#edit_name').val(data.name);
        $('#edit_description').val(data.description);
        $('#edit_is_active').prop('checked', data.is_active);
        
        $('#editWorkTypeForm').attr('action', `${workTypeUrls.update}/${id}`);
        $('#editWorkTypeModal').modal('show');
    });
}

function deleteWorkType(id, name) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Eliminarás el tipo de trabajo "${name}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${workTypeUrls.destroy}/${id}`,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.message || 'Error al eliminar');
                }
            });
        }
    });
}
</script>
@endpush