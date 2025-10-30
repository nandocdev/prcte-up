@extends('layouts.app')

@section('title', __('Estados de Trabajo'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-traffic-light mr-2"></i>{{ __('Estados de Trabajo') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.catalogs.index') }}">{{ __('Catálogos') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Estados de Trabajo') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-2"></i>{{ __('Flujo de Estados de Trabajo') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#createWorkStatusModal">
                        <i class="fas fa-plus mr-1"></i>{{ __('Nuevo Estado') }}
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Estadísticas de Estados -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-traffic-light"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Total Estados') }}</span>
                                <span class="info-box-number">{{ $workStatuses->total() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Estados Activos') }}</span>
                                <span class="info-box-number">{{ $workStatuses->where('is_active', true)->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-flag-checkered"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Estados Finales') }}</span>
                                <span class="info-box-number">{{ $workStatuses->where('is_final', true)->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-arrows-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Con Transiciones') }}</span>
                                <span class="info-box-number">{{ $workStatuses->filter(fn($s) => $s->transitionsFrom->count() > 0)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advertencia sobre Flujo -->
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> {{ __('Información del Flujo de Estados') }}</h5>
                    <p>{{ __('Los estados de trabajo definen el flujo de aprobación de los trabajos de extensión. El orden es crítico para el funcionamiento correcto del sistema.') }}</p>
                    <ul class="mb-0">
                        <li>{{ __('Los estados finales no pueden tener transiciones salientes') }}</li>
                        <li>{{ __('El orden determina la secuencia lógica del flujo') }}</li>
                        <li>{{ __('Los colores ayudan a identificar visualmente cada estado') }}</li>
                    </ul>
                </div>

                <!-- Flujo Visual de Estados -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">{{ __('Flujo Visual de Estados') }}</h3>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    @foreach($workStatuses->sortBy('sort_order')->take(10) as $status)
                                        <div class="time-label">
                                            <span class="badge" style="background-color: {{ $status->color }}; color: white;">
                                                {{ $status->sort_order }}. {{ $status->name }}
                                            </span>
                                        </div>
                                        <div>
                                            <i class="fas fa-circle" style="color: {{ $status->color }}"></i>
                                            <div class="timeline-item">
                                                <h3 class="timeline-header">
                                                    {{ $status->name }}
                                                    @if($status->is_final)
                                                        <span class="badge badge-warning">{{ __('Final') }}</span>
                                                    @endif
                                                    @if(!$status->is_active)
                                                        <span class="badge badge-secondary">{{ __('Inactivo') }}</span>
                                                    @endif
                                                </h3>
                                                <div class="timeline-body">
                                                    {{ $status->description ?: __('Sin descripción') }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" id="searchStatuses" class="form-control" placeholder="{{ __('Buscar estados...') }}">
                    </div>
                    <div class="col-md-3">
                        <select id="statusFilter" class="form-control">
                            <option value="">{{ __('Todos los estados') }}</option>
                            <option value="active">{{ __('Solo activos') }}</option>
                            <option value="inactive">{{ __('Solo inactivos') }}</option>
                            <option value="final">{{ __('Solo finales') }}</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="reorderStates()">
                                <i class="fas fa-sort"></i> {{ __('Reordenar') }}
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="viewWorkflow()">
                                <i class="fas fa-sitemap"></i> {{ __('Ver Flujo') }}
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="exportStates()">
                                <i class="fas fa-download"></i> {{ __('Exportar') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Estados -->
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
                                <th>{{ __('Orden') }}</th>
                                <th>{{ __('Nombre') }}</th>
                                <th>{{ __('Descripción') }}</th>
                                <th>{{ __('Color') }}</th>
                                <th>{{ __('Tipo') }}</th>
                                <th>{{ __('Estado') }}</th>
                                <th>{{ __('Transiciones') }}</th>
                                <th>{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody id="sortable">
                            @forelse($workStatuses as $status)
                                <tr class="status-row" data-id="{{ $status->id }}" data-name="{{ strtolower($status->name) }}" data-status="{{ $status->is_active ? 'active' : 'inactive' }}" data-final="{{ $status->is_final ? 'final' : 'normal' }}">
                                    <td>
                                        <div class="icheck-primary">
                                            <input type="checkbox" id="check_{{ $status->id }}" class="item-checkbox" value="{{ $status->id }}">
                                            <label for="check_{{ $status->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light">{{ $status->sort_order }}</span>
                                        <i class="fas fa-grip-vertical text-muted ml-2" style="cursor: move;" title="{{ __('Arrastrar para reordenar') }}"></i>
                                    </td>
                                    <td>
                                        <strong>{{ $status->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ $status->description ? Str::limit($status->description, 50) : __('Sin descripción') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $status->color }}; color: white;">
                                            {{ $status->color }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($status->is_final)
                                            <span class="badge badge-warning">{{ __('Final') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ __('Intermedio') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($status->is_active)
                                            <span class="badge badge-success">{{ __('Activo') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('Inactivo') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-arrow-right"></i> {{ $status->transitionsFrom->count() }} {{ __('salientes') }}<br>
                                            <i class="fas fa-arrow-left"></i> {{ $status->transitionsTo->count() }} {{ __('entrantes') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" onclick="editWorkStatus({{ $status->id }})" title="{{ __('Editar') }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-info" onclick="viewTransitions({{ $status->id }})" title="{{ __('Ver transiciones') }}">
                                                <i class="fas fa-sitemap"></i>
                                            </button>
                                            @if($status->transitionsFrom->count() === 0 && $status->transitionsTo->count() === 0)
                                                <button class="btn btn-outline-danger" onclick="deleteWorkStatus({{ $status->id }}, '{{ $status->name }}')" title="{{ __('Eliminar') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button class="btn btn-outline-secondary" disabled title="{{ __('No se puede eliminar: tiene transiciones') }}">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-traffic-light fa-3x mb-3"></i>
                                        <p>{{ __('No hay estados configurados.') }}</p>
                                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createWorkStatusModal">
                                            {{ __('Crear el primer estado') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($workStatuses->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $workStatuses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Estado -->
<div class="modal fade" id="createWorkStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="createWorkStatusForm">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('Crear Nuevo Estado de Trabajo') }}</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required
                               placeholder="{{ __('ej: En Revisión') }}">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('Descripción') }}</label>
                        <textarea class="form-control" id="description" name="description" rows="3"
                                  placeholder="{{ __('Descripción detallada del estado...') }}"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="color">{{ __('Color') }} <span class="text-danger">*</span></label>
                                <input type="color" class="form-control" id="color" name="color" value="#007bff" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="order">{{ __('Orden') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="order" name="order" min="0" required
                                       value="{{ $workStatuses->max('order') + 1 }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="is_active" name="is_active" checked>
                                    <label for="is_active">{{ __('Activo') }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="icheck-warning">
                                    <input type="checkbox" id="is_final" name="is_final">
                                    <label for="is_final">{{ __('Estado Final') }}</label>
                                </div>
                                <small class="form-text text-muted">
                                    {{ __('Los estados finales terminan el flujo') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
                    <button type="submit" class="btn btn-success">{{ __('Crear Estado') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Hacer la tabla sortable
    new Sortable(document.getElementById('sortable'), {
        animation: 150,
        handle: '.fa-grip-vertical',
        onEnd: function (evt) {
            updateOrder();
        }
    });

    // Búsqueda
    $('#searchStatuses').on('input', function() {
        filterStatuses();
    });

    // Filtros
    $('#statusFilter').on('change', function() {
        filterStatuses();
    });

    // Crear estado
    $('#createWorkStatusForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('is_active', $('#is_active').is(':checked') ? 1 : 0);
        formData.append('is_final', $('#is_final').is(':checked') ? 1 : 0);

        $.ajax({
            url: '{{ route("admin.catalogs.work-statuses.store") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#createWorkStatusModal').modal('hide');
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

function filterStatuses() {
    const searchTerm = $('#searchStatuses').val().toLowerCase();
    const statusFilter = $('#statusFilter').val();

    $('.status-row').each(function() {
        const $row = $(this);
        const name = $row.data('name');
        const status = $row.data('status');
        const final = $row.data('final');

        let showRow = true;

        if (searchTerm && !name.includes(searchTerm)) {
            showRow = false;
        }

        if (statusFilter) {
            if (statusFilter === 'final' && final !== 'final') showRow = false;
            else if (statusFilter !== 'final' && status !== statusFilter) showRow = false;
        }

        $row.toggle(showRow);
    });
}

function updateOrder() {
    const order = [];
    $('#sortable tr').each(function(index) {
        const id = $(this).data('id');
        if (id) {
            order.push({ id: id, order: index + 1 });
        }
    });

    $.ajax({
        url: '{{ route("admin.catalogs.work-statuses.update-order") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            order: order
        },
        success: function(response) {
            if (response.success) {
                toastr.success('{{ __("Orden actualizado exitosamente") }}');
            }
        }
    });
}

function editWorkStatus(id) {
    // Implementar edición
    console.log('Editar estado:', id);
}

function viewTransitions(id) {
    // Implementar vista de transiciones
    console.log('Ver transiciones:', id);
}

function deleteWorkStatus(id, name) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Eliminarás el estado "${name}"`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/admin/catalogs/work-statuses/${id}`,
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