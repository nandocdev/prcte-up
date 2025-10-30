@extends('layouts.app')

@section('title', __('Unidades Organizacionales'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
<style>
.org-unit-tree {
    margin-left: 0;
}
.org-unit-tree .tree-item {
    margin-left: 20px;
    border-left: 2px solid #dee2e6;
    padding-left: 15px;
    margin-bottom: 5px;
}
.org-unit-tree .tree-item:last-child {
    border-left-color: transparent;
}
.tree-toggle {
    cursor: pointer;
    user-select: none;
}
</style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-university mr-2"></i>{{ __('Unidades Organizacionales') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.index') }}">{{ __('Catálogos') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Unidades Organizacionales') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sitemap mr-2"></i>{{ __('Estructura Organizacional') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#createUnitModal">
                        <i class="fas fa-plus mr-1"></i>{{ __('Nueva Unidad') }}
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-university"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Total Unidades') }}</span>
                                <span class="info-box-number">{{ $units->total() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-building"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Facultades') }}</span>
                                <span class="info-box-number">{{ $units->where('type', 'facultad')->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-school"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Departamentos') }}</span>
                                <span class="info-box-number">{{ $units->where('type', 'departamento')->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Con Usuarios') }}</span>
                                <span class="info-box-number">{{ $units->filter(fn($u) => $u->users->count() > 0)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros y Vista -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="searchUnits" class="form-control" placeholder="{{ __('Buscar unidades...') }}">
                    </div>
                    <div class="col-md-3">
                        <select id="typeFilter" class="form-control">
                            <option value="">{{ __('Todos los tipos') }}</option>
                            <option value="universidad">{{ __('Universidad') }}</option>
                            <option value="facultad">{{ __('Facultad') }}</option>
                            <option value="centro">{{ __('Centro') }}</option>
                            <option value="departamento">{{ __('Departamento') }}</option>
                            <option value="escuela">{{ __('Escuela') }}</option>
                            <option value="instituto">{{ __('Instituto') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="statusFilter" class="form-control">
                            <option value="">{{ __('Todos') }}</option>
                            <option value="active">{{ __('Activos') }}</option>
                            <option value="inactive">{{ __('Inactivos') }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="toggleView()">
                                <i class="fas fa-list" id="viewIcon"></i> <span id="viewText">{{ __('Vista Tabla') }}</span>
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="exportUnits()">
                                <i class="fas fa-download"></i> {{ __('Exportar') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Vista de Árbol (por defecto) -->
                <div id="treeView">
                    <div class="org-unit-tree">
                        @foreach($units->where('parent_id', null) as $rootUnit)
                            @include('admin.catalogs.organizational-units.tree-item', ['unit' => $rootUnit, 'level' => 0])
                        @endforeach
                    </div>
                </div>

                <!-- Vista de Tabla (oculta por defecto) -->
                <div id="tableView" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('Código') }}</th>
                                    <th>{{ __('Nombre') }}</th>
                                    <th>{{ __('Tipo') }}</th>
                                    <th>{{ __('Padre') }}</th>
                                    <th>{{ __('Usuarios') }}</th>
                                    <th>{{ __('Estado') }}</th>
                                    <th>{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($units as $unit)
                                    <tr class="unit-row" data-name="{{ strtolower($unit->name) }}" data-type="{{ $unit->type }}" data-status="{{ $unit->is_active ? 'active' : 'inactive' }}">
                                        <td><code>{{ $unit->code }}</code></td>
                                        <td>
                                            <strong>{{ $unit->name }}</strong>
                                            @if($unit->children->count() > 0)
                                                <small class="text-muted">({{ $unit->children->count() }} {{ __('subunidades') }})</small>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $typeColors = [
                                                    'universidad' => 'primary',
                                                    'facultad' => 'success',
                                                    'centro' => 'info',
                                                    'departamento' => 'warning',
                                                    'escuela' => 'secondary',
                                                    'instituto' => 'dark'
                                                ];
                                                $color = $typeColors[$unit->type] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $color }}">
                                                {{ ucfirst($unit->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($unit->parent)
                                                <small>{{ $unit->parent->name }}</small>
                                            @else
                                                <span class="text-muted">{{ __('Raíz') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $unit->users->count() }}</span>
                                        </td>
                                        <td>
                                            @if($unit->is_active)
                                                <span class="badge badge-success">{{ __('Activo') }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ __('Inactivo') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary" onclick="editUnit({{ $unit->id }})" title="{{ __('Editar') }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-info" onclick="viewUnit({{ $unit->id }})" title="{{ __('Ver detalles') }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($unit->children->count() === 0 && $unit->users->count() === 0)
                                                    <button class="btn btn-outline-danger" onclick="deleteUnit({{ $unit->id }}, '{{ $unit->name }}')" title="{{ __('Eliminar') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-secondary" disabled title="{{ __('No se puede eliminar: tiene dependencias') }}">
                                                        <i class="fas fa-lock"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Paginación -->
                @if($units->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $units->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Panel de Información -->
    <div class="col-md-4">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">{{ __('Información del Sistema') }}</h3>
            </div>
            <div class="card-body">
                <h6><strong>{{ __('Tipos de Unidades:') }}</strong></h6>
                <ul class="list-unstyled">
                    @php
                        $typeColors = [
                            'universidad' => 'primary',
                            'facultad' => 'success',
                            'centro' => 'info',
                            'departamento' => 'warning',
                            'escuela' => 'secondary',
                            'instituto' => 'dark'
                        ];
                    @endphp
                    @foreach($typeColors as $type => $color)
                        <li class="mb-1">
                            <span class="badge badge-{{ $color }}">{{ ucfirst($type) }}</span>
                            - {{ $units->where('type', $type)->count() }} {{ __('unidades') }}
                        </li>
                    @endforeach
                </ul>

                <hr>

                <h6><strong>{{ __('Distribución por Estado:') }}</strong></h6>
                <div class="progress mb-2">
                    @php
                        $activeCount = $units->where('is_active', true)->count();
                        $totalCount = $units->count();
                        $activePercentage = $totalCount > 0 ? ($activeCount / $totalCount) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" style="width: {{ $activePercentage }}%">
                        {{ round($activePercentage, 1) }}% {{ __('Activas') }}
                    </div>
                </div>
                <small class="text-muted">
                    {{ $activeCount }} {{ __('activas') }} / {{ $units->where('is_active', false)->count() }} {{ __('inactivas') }}
                </small>
            </div>
        </div>

        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">{{ __('Guía de Gestión') }}</h3>
            </div>
            <div class="card-body">
                <h6><strong>{{ __('Mejores Prácticas:') }}</strong></h6>
                <ul>
                    <li>{{ __('Mantén una jerarquía clara') }}</li>
                    <li>{{ __('Usa códigos descriptivos') }}</li>
                    <li>{{ __('Revisa dependencias antes de eliminar') }}</li>
                    <li>{{ __('Asigna usuarios a la unidad correcta') }}</li>
                </ul>

                <hr>

                <h6><strong>{{ __('Restricciones:') }}</strong></h6>
                <ul class="text-sm">
                    <li>{{ __('No se puede eliminar unidades con subunidades') }}</li>
                    <li>{{ __('No se puede eliminar unidades con usuarios') }}</li>
                    <li>{{ __('Los códigos deben ser únicos') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Unidad -->
<div class="modal fade" id="createUnitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="createUnitForm">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Crear Nueva Unidad Organizacional') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required
                                       placeholder="{{ __('ej: Facultad de Ingeniería') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">{{ __('Código') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" required
                                       placeholder="{{ __('ej: FAC-ING') }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">{{ __('Tipo') }} <span class="text-danger">*</span></label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">{{ __('Seleccionar tipo') }}</option>
                                    <option value="universidad">{{ __('Universidad') }}</option>
                                    <option value="facultad">{{ __('Facultad') }}</option>
                                    <option value="centro">{{ __('Centro') }}</option>
                                    <option value="departamento">{{ __('Departamento') }}</option>
                                    <option value="escuela">{{ __('Escuela') }}</option>
                                    <option value="instituto">{{ __('Instituto') }}</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="parent_id">{{ __('Unidad Padre') }}</label>
                                <select class="form-control" id="parent_id" name="parent_id">
                                    <option value="">{{ __('Ninguna (Raíz)') }}</option>
                                    @foreach($parentOptions as $option)
                                        <option value="{{ $option['id'] }}">{{ $option['display'] }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('Descripción') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="{{ __('Descripción de la unidad organizacional...') }}"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <div class="icheck-primary">
                            <input type="checkbox" id="is_active" name="is_active" checked>
                            <label for="is_active">{{ __('Activa') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-warning">{{ __('Crear Unidad') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
let currentView = 'tree';

$(document).ready(function() {
    // Filtros
    $('#searchUnits, #typeFilter, #statusFilter').on('input change', function() {
        if (currentView === 'table') {
            filterUnits();
        } else {
            filterTreeUnits();
        }
    });

    // Crear unidad
    $('#createUnitForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('is_active', $('#is_active').is(':checked') ? 1 : 0);

        $.ajax({
            url: '{{ route("admin.catalogs.organizational-units.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#createUnitModal').modal('hide');
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
});

function toggleView() {
    if (currentView === 'tree') {
        $('#treeView').hide();
        $('#tableView').show();
        $('#viewIcon').removeClass('fa-list').addClass('fa-sitemap');
        $('#viewText').text('{{ __("Vista Árbol") }}');
        currentView = 'table';
    } else {
        $('#treeView').show();
        $('#tableView').hide();
        $('#viewIcon').removeClass('fa-sitemap').addClass('fa-list');
        $('#viewText').text('{{ __("Vista Tabla") }}');
        currentView = 'tree';
    }
}

function filterUnits() {
    const searchTerm = $('#searchUnits').val().toLowerCase();
    const typeFilter = $('#typeFilter').val();
    const statusFilter = $('#statusFilter').val();

    $('.unit-row').each(function() {
        const $row = $(this);
        const name = $row.data('name');
        const type = $row.data('type');
        const status = $row.data('status');

        let showRow = true;

        if (searchTerm && !name.includes(searchTerm)) showRow = false;
        if (typeFilter && type !== typeFilter) showRow = false;
        if (statusFilter && status !== statusFilter) showRow = false;

        $row.toggle(showRow);
    });
}

function filterTreeUnits() {
    // Implementar filtrado para vista de árbol
    const searchTerm = $('#searchUnits').val().toLowerCase();
    $('.tree-item').each(function() {
        const name = $(this).find('.unit-name').text().toLowerCase();
        $(this).toggle(!searchTerm || name.includes(searchTerm));
    });
}

function editUnit(id) {
    console.log('Editar unidad:', id);
}

function viewUnit(id) {
    console.log('Ver unidad:', id);
}

function deleteUnit(id, name) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Eliminarás la unidad "${name}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/catalogs/organizational-units/${id}`,
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

function exportUnits() {
    window.open('{{ route("admin.catalogs.export", "organizational_units") }}?format=xlsx', '_blank');
}
</script>
@endpush