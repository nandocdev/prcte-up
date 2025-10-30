@extends('layouts.app')

@section('title', __('Gestión de Catálogos'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-database mr-2"></i>{{ __('Gestión de Catálogos del Sistema') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Catálogos') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="container-fluid">
    {{-- Resumen de Catálogos --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $statistics['work_types'] }}</h3>
                    <p>Tipos de Trabajo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
                <a href="{{ route('admin.catalogs.work-types') }}" class="small-box-footer">
                    {{ __('Gestionar') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $statistics['work_statuses'] }}</h3>
                    <p>Estados de Trabajo</p>
                </div>
                <div class="icon">
                    <i class="fas fa-traffic-light"></i>
                </div>
                <a href="{{ route('admin.catalogs.work-statuses') }}" class="small-box-footer">
                    {{ __('Gestionar') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $statistics['organizational_units'] }}</h3>
                    <p>Unidades Organizacionales</p>
                </div>
                <div class="icon">
                    <i class="fas fa-university"></i>
                </div>
                <a href="{{ route('admin.catalogs.organizational-units') }}" class="small-box-footer">
                    {{ __('Gestionar') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $statistics['institutional_project_types'] }}</h3>
                    <p>Tipos de Proyectos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <a href="{{ route('admin.catalogs.institutional-project-types') }}" class="small-box-footer">
                    {{ __('Gestionar') }} <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Herramientas Globales --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Herramientas de Gestión Global
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <button type="button" class="btn btn-success btn-block" id="btn-bulk-activate">
                                <i class="fas fa-toggle-on"></i><br>
                                Activación Masiva
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="button" class="btn btn-warning btn-block" id="btn-bulk-deactivate">
                                <i class="fas fa-toggle-off"></i><br>
                                Desactivación Masiva
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="button" class="btn btn-info btn-block" id="btn-export-all">
                                <i class="fas fa-download"></i><br>
                                Exportar Todo
                            </button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <button type="button" class="btn btn-outline-danger btn-block" id="btn-clear-cache">
                                <i class="fas fa-sync"></i><br>
                                Limpiar Caché
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Tipos de Trabajo Recientes --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tags mr-1"></i>
                        Tipos de Trabajo Recientes
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.catalogs.work-types') }}" class="btn btn-tool">
                            <i class="fas fa-eye"></i> Ver todos
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentWorkTypes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Estado</th>
                                        <th>Uso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentWorkTypes as $workType)
                                        <tr>
                                            <td>{{ $workType->name }}</td>
                                            <td>
                                                @if($workType->is_active)
                                                    <span class="badge badge-success badge-sm">Activo</span>
                                                @else
                                                    <span class="badge badge-secondary badge-sm">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $workType->work_of_extensions_count ?? 0 }} trabajos
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-xs btn-outline-primary btn-edit-catalog" 
                                                        data-type="work_types" data-id="{{ $workType->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="p-3 text-muted text-center">No hay tipos de trabajo configurados.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Estados de Trabajo Recientes --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-traffic-light mr-1"></i>
                        Estados de Trabajo Recientes
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.catalogs.work-statuses') }}" class="btn btn-tool">
                            <i class="fas fa-eye"></i> Ver todos
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentWorkStatuses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Color</th>
                                        <th>Orden</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentWorkStatuses as $status)
                                        <tr>
                                            <td>{{ $status->name }}</td>
                                            <td>
                                                <span class="badge" style="background-color: {{ $status->color }}">
                                                    {{ $status->color }}
                                                </span>
                                            </td>
                                            <td>{{ $status->order }}</td>
                                            <td>
                                                @if($status->is_active)
                                                    <span class="badge badge-success badge-sm">Activo</span>
                                                @else
                                                    <span class="badge badge-secondary badge-sm">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-xs btn-outline-primary btn-edit-catalog" 
                                                        data-type="work_statuses" data-id="{{ $status->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="p-3 text-muted text-center">No hay estados configurados.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Unidades Organizacionales Recientes --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-university mr-1"></i>
                        Unidades Organizacionales Recientes
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.catalogs.organizational-units') }}" class="btn btn-tool">
                            <i class="fas fa-eye"></i> Ver todas
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentOrgUnits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Padre</th>
                                        <th>Usuarios</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrgUnits as $unit)
                                        <tr>
                                            <td>{{ $unit->name }}</td>
                                            <td>
                                                <span class="badge badge-info badge-sm">
                                                    {{ ucfirst($unit->type) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($unit->parent)
                                                    <small class="text-muted">{{ $unit->parent->name }}</small>
                                                @else
                                                    <small class="text-muted">Raíz</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    {{ $unit->users_count ?? 0 }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-xs btn-outline-primary btn-edit-catalog" 
                                                        data-type="organizational_units" data-id="{{ $unit->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="p-3 text-muted text-center">No hay unidades configuradas.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tipos de Proyectos Institucionales Recientes --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-layer-group mr-1"></i>
                        Tipos de Proyectos Recientes
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.catalogs.institutional-project-types') }}" class="btn btn-tool">
                            <i class="fas fa-eye"></i> Ver todos
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentProjectTypes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Estado</th>
                                        <th>Uso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentProjectTypes as $projectType)
                                        <tr>
                                            <td>{{ $projectType->name }}</td>
                                            <td>
                                                @if($projectType->is_active)
                                                    <span class="badge badge-success badge-sm">Activo</span>
                                                @else
                                                    <span class="badge badge-secondary badge-sm">Inactivo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $projectType->project_details_count ?? 0 }} proyectos
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-xs btn-outline-primary btn-edit-catalog" 
                                                        data-type="institutional_project_types" data-id="{{ $projectType->id }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="p-3 text-muted text-center">No hay tipos de proyecto configurados.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Acceso Directo a Catálogos Específicos --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i>
                        Acceso Directo a Catálogos
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.catalogs.work-types') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-tags"></i><br>
                                <strong>Tipos de Trabajo</strong><br>
                                <small>Gestión completa</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.catalogs.work-statuses') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-traffic-light"></i><br>
                                <strong>Estados de Trabajo</strong><br>
                                <small>Flujo de aprobación</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.catalogs.organizational-units') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-university"></i><br>
                                <strong>Unidades Organizacionales</strong><br>
                                <small>Estructura universitaria</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.catalogs.institutional-project-types') }}" class="btn btn-outline-danger btn-block">
                                <i class="fas fa-layer-group"></i><br>
                                <strong>Tipos de Proyectos</strong><br>
                                <small>Clasificación institucional</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.small-box .icon {
    transition: all 0.3s ease-in-out;
}

.small-box:hover .icon {
    transform: scale(1.1);
}

.btn-block {
    height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
}

.card-title {
    font-weight: 600;
}

.badge-sm {
    font-size: 0.7em;
}

.table td {
    vertical-align: middle;
}

.btn-xs {
    padding: 0.25rem 0.4rem;
    font-size: 0.75rem;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Animación para las cajas pequeñas
    $('.small-box').hover(
        function() {
            $(this).addClass('elevation-3');
        },
        function() {
            $(this).removeClass('elevation-3');
        }
    );

    // Limpiar caché del sistema
    $('#btn-clear-cache').click(function() {
        Swal.fire({
            title: '¿Limpiar caché del sistema?',
            text: 'Esta acción limpiará todos los cachés de los catálogos',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, limpiar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i><br>Limpiando...');
                
                $.ajax({
                    url: '{{ route("admin.system.clear-cache") }}',
                    method: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Éxito', response.message, 'success');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Ocurrió un error inesperado', 'error');
                    },
                    complete: function() {
                        $('#btn-clear-cache').prop('disabled', false).html('<i class="fas fa-sync"></i><br>Limpiar Caché');
                    }
                });
            }
        });
    });

    // Tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@stop