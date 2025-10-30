@extends('layouts.app')

@section('title', __('Tipos de Proyectos Institucionales'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-layer-group mr-2"></i>{{ __('Tipos de Proyectos Institucionales') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.index') }}">{{ __('Catálogos') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Tipos de Proyectos') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-2"></i>{{ __('Clasificación de Proyectos Institucionales') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#createProjectTypeModal">
                        <i class="fas fa-plus mr-1"></i>{{ __('Nuevo Tipo') }}
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Información sobre Tipos de Proyectos -->
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> {{ __('Acerca de los Tipos de Proyectos Institucionales') }}</h5>
                    <p>{{ __('Los tipos de proyectos institucionales clasifican los trabajos de extensión según su naturaleza y alcance dentro de la universidad.') }}</p>
                    <ul class="mb-0">
                        <li><strong>{{ __('Institucionales:') }}</strong> {{ __('Proyectos que involucran toda la universidad') }}</li>
                        <li><strong>{{ __('Unidades Académicas:') }}</strong> {{ __('Proyectos específicos de facultades o departamentos') }}</li>
                        <li><strong>{{ __('Servicio Social:') }}</strong> {{ __('Proyectos de responsabilidad social universitaria') }}</li>
                    </ul>
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    @php
                        $totalTypes = collect($projectTypes ?? [])->count();
                        $activeTypes = collect($projectTypes ?? [])->where('is_active', true)->count();
                        $usedTypes = collect($projectTypes ?? [])->filter(fn($t) => ($t->project_details_count ?? 0) > 0)->count();
                    @endphp
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-layer-group"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Total Tipos') }}</span>
                                <span class="info-box-number">{{ $totalTypes }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Activos') }}</span>
                                <span class="info-box-number">{{ $activeTypes }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-chart-bar"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('En Uso') }}</span>
                                <span class="info-box-number">{{ $usedTypes }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-exclamation"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Sin Proyectos') }}</span>
                                <span class="info-box-number">{{ $totalTypes - $usedTypes }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" id="searchProjectTypes" class="form-control" placeholder="{{ __('Buscar tipos de proyecto...') }}">
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
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="exportProjectTypes()">
                                <i class="fas fa-download"></i> {{ __('Exportar') }}
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="bulkActions()">
                                <i class="fas fa-tasks"></i> {{ __('Acciones Masivas') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Tipos de Proyectos -->
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
                                <th>{{ __('Proyectos Asociados') }}</th>
                                <th>{{ __('Creado') }}</th>
                                <th>{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projectTypes ?? [] as $projectType)
                                <tr class="project-type-row" data-name="{{ strtolower($projectType->name) }}" data-status="{{ $projectType->is_active ? 'active' : 'inactive' }}">
                                    <td>
                                        <div class="icheck-primary">
                                            <input type="checkbox" id="check_{{ $projectType->id }}" class="item-checkbox" value="{{ $projectType->id }}">
                                            <label for="check_{{ $projectType->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $projectType->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $projectType->description ? Str::limit($projectType->description, 60) : __('Sin descripción') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($projectType->is_active)
                                            <span class="badge badge-success">{{ __('Activo') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('Inactivo') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $projectType->project_details_count ?? 0 }} {{ __('proyectos') }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $projectType->created_at->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="editProjectType({{ $projectType->id }})" title="{{ __('Editar') }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-info" onclick="viewProjectType({{ $projectType->id }})" title="{{ __('Ver detalles') }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if(($projectType->project_details_count ?? 0) === 0)
                                                <button class="btn btn-outline-danger" onclick="deleteProjectType({{ $projectType->id }}, '{{ $projectType->name }}')" title="{{ __('Eliminar') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-outline-secondary" disabled title="{{ __('No se puede eliminar: tiene proyectos asociados') }}">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-layer-group fa-3x mb-3"></i>
                                        <p>{{ __('No hay tipos de proyecto configurados.') }}</p>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#createProjectTypeModal">
                                            {{ __('Crear el primer tipo') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            @if($projectTypes->hasPages())
                <div class="card-footer">
                    {{ $projectTypes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Crear Tipo de Proyecto -->
<div class="modal fade" id="createProjectTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createProjectTypeForm">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Crear Nuevo Tipo de Proyecto') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required
                               placeholder="{{ __('ej: Proyecto Institucional') }}">
                        <div class="invalid-feedback"></div>
                        <small class="form-text text-muted">
                            {{ __('Nombre descriptivo del tipo de proyecto institucional') }}
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('Descripción') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                                  placeholder="{{ __('Descripción detallada del tipo de proyecto y sus características...') }}"></textarea>
                        <div class="invalid-feedback"></div>
                        <small class="form-text text-muted">
                            {{ __('Explica las características y alcance de este tipo de proyecto') }}
                        </small>
                    </div>

                    <div class="form-group">
                        <div class="icheck-primary">
                            <input type="checkbox" id="is_active" name="is_active" checked>
                            <label for="is_active">{{ __('Activo') }}</label>
                        </div>
                        <small class="form-text text-muted">
                            {{ __('Los tipos inactivos no aparecerán en los formularios de creación de proyectos.') }}
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb"></i> {{ __('Ejemplos de Tipos:') }}</h6>
                        <ul class="mb-0">
                            <li><strong>{{ __('Institucional:') }}</strong> {{ __('Proyectos que involucran múltiples facultades') }}</li>
                            <li><strong>{{ __('Unidad Académica:') }}</strong> {{ __('Proyectos específicos de una facultad') }}</li>
                            <li><strong>{{ __('Servicio Social:') }}</strong> {{ __('Proyectos de responsabilidad social') }}</li>
                            <li><strong>{{ __('Investigación Aplicada:') }}</strong> {{ __('Proyectos con enfoque investigativo') }}</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Crear Tipo') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Tipo de Proyecto -->
<div class="modal fade" id="editProjectTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editProjectTypeForm">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Editar Tipo de Proyecto') }}</h4>
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
                        <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
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
                    <button type="submit" class="btn btn-danger">{{ __('Guardar Cambios') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
$(document).ready(function() {
    // Búsqueda
    $('#searchProjectTypes').on('input', function() {
        filterProjectTypes();
    });

    // Filtro por estado
    $('#statusFilter').on('change', function() {
        filterProjectTypes();
    });

    // Seleccionar todo
    $('#selectAll').on('change', function() {
        $('.item-checkbox').prop('checked', this.checked);
    });

    // Crear tipo de proyecto
    $('#createProjectTypeForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('is_active', $('#is_active').is(':checked') ? 1 : 0);

        $.ajax({
            url: '{{ route("admin.catalogs.institutional-project-types.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#createProjectTypeModal').modal('hide');
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

    // Editar tipo de proyecto
    $('#editProjectTypeForm').on('submit', function(e) {
        e.preventDefault();
        
        const projectId = $(this).data('project-id');
        const formData = new FormData(this);
        formData.append('is_active', $('#edit_is_active').is(':checked') ? 1 : 0);
        formData.append('_method', 'PUT');
        
        const url = `{{ route('admin.catalogs.institutional-project-types.update', ':id') }}`.replace(':id', projectId);
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editProjectTypeModal').modal('hide');
                    toastr.success(response.message);
                    location.reload();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').empty();
                
                Object.keys(errors).forEach(field => {
                    $(`#edit_${field}`).addClass('is-invalid');
                    $(`#edit_${field}`).siblings('.invalid-feedback').text(errors[field][0]);
                });
            }
        });
    });
});

function filterProjectTypes() {
    const searchTerm = $('#searchProjectTypes').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();

    $('.project-type-row').each(function() {
        const $row = $(this);
        const name = $row.data('name');
        const status = $row.data('status');

        let showRow = true;

        if (searchTerm && !name.includes(searchTerm)) {
            showRow = false;
        }

        if (statusFilter && status !== statusFilter) {
            showRow = false;
        }

        $row.toggle(showRow);
    });
}

function editProjectType(id) {
    // Obtener datos de la fila de la tabla
    const row = $(`.project-type-row[data-id="${id}"]`);
    if (row.length === 0) {
        // Buscar por el botón que fue clickeado
        const button = $(`button[onclick*="editProjectType(${id})"]`);
        const tableRow = button.closest('tr');
        
        if (tableRow.length > 0) {
            const cells = tableRow.find('td');
            const name = cells.eq(1).find('strong').text().trim();
            const description = cells.eq(2).find('span').text().trim();
            const isActive = cells.eq(3).find('.badge-success').length > 0;
            
            // Llenar el modal
            $('#edit_name').val(name);
            $('#edit_description').val(description === 'Sin descripción' ? '' : description);
            $('#edit_is_active').prop('checked', isActive);
            
            // Configurar la URL de submit
            $('#editProjectTypeForm').data('project-id', id);
            $('#editProjectTypeModal').modal('show');
        } else {
            toastr.error('No se pudieron cargar los datos');
        }
    }
}

function viewProjectType(id) {
    console.log('Ver tipo de proyecto:', id);
}

function deleteProjectType(id, name) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Eliminarás el tipo de proyecto "${name}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ route('admin.catalogs.institutional-project-types.destroy', ':id') }}`.replace(':id', id),
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Error al eliminar');
                }
            });
        }
    });
}

function exportProjectTypes() {
    window.open('{{ route("admin.catalogs.export", "institutional_project_types") }}?format=xlsx', '_blank');
}

function bulkActions() {
    const selectedIds = $('.item-checkbox:checked').map(function() {
        return this.value;
    }).get();

    if (selectedIds.length === 0) {
        toastr.warning('{{ __("Selecciona al menos un elemento") }}');
        return;
    }

    console.log('Acciones masivas para:', selectedIds);
}
</script>
@endpush