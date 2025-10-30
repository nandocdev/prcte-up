@extends('adminlte::page')

@section('title', 'Dashboard Administrador - VIEX')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1 class="m-0">
                <i class="fas fa-cogs"></i> Dashboard de Administración
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                <li class="breadcrumb-item active">Administración</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    {{-- Resumen de Métricas Principales --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['total_users'] }}</h3>
                    <p>Usuarios Totales</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">
                    Ver todos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['active_users'] }}</h3>
                    <p>Usuarios Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('admin.users.index', ['filter' => 'active']) }}" class="small-box-footer">
                    Ver activos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['professors'] }}</h3>
                    <p>Profesores</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <a href="{{ route('admin.users.index', ['role' => 'profesor']) }}" class="small-box-footer">
                    Ver profesores <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['total_works'] }}</h3>
                    <p>Trabajos de Extensión</p>
                </div>
                <div class="icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <a href="{{ route('works.index') }}" class="small-box-footer">
                    Ver trabajos <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Métricas del Sistema --}}
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-user-shield"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Roles del Sistema</span>
                    <span class="info-box-number">{{ $stats['total_roles'] }}</span>
                    <a href="{{ route('admin.roles.index') }}" class="info-box-more">
                        Gestionar roles <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-key"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Permisos</span>
                    <span class="info-box-number">{{ $stats['total_permissions'] }}</span>
                    <a href="{{ route('admin.permissions.index') }}" class="info-box-more">
                        Gestionar permisos <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-university"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Unidades Organizacionales</span>
                    <span class="info-box-number">{{ $catalogStats['organizational_units']['total'] }}</span>
                    <a href="{{ route('admin.catalogs.organizational-units') }}" class="info-box-more">
                        Gestionar unidades <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-chart-line"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Trabajos Pendientes</span>
                    <span class="info-box-number">{{ $systemActivity['pending_works'] }}</span>
                    <a href="{{ route('works.index') }}" class="info-box-more">
                        Ver pendientes <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Estadísticas de Catálogos --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database mr-1"></i>
                        Estadísticas de Catálogos del Sistema
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.catalogs.index') }}" class="btn btn-tool">
                            <i class="fas fa-cogs"></i> Gestionar Catálogos
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <span class="description-percentage text-primary">
                                    <i class="fas fa-tags"></i> {{ $catalogStats['work_types']['active'] }}/{{ $catalogStats['work_types']['total'] }}
                                </span>
                                <h5 class="description-header">Tipos de Trabajo</h5>
                                <span class="description-text">{{ $catalogStats['work_types']['with_works'] }} CON TRABAJOS</span>
                                <a href="{{ route('admin.catalogs.work-types') }}" class="btn btn-xs btn-outline-primary mt-2">
                                    <i class="fas fa-cog"></i> Gestionar
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <span class="description-percentage text-success">
                                    <i class="fas fa-traffic-light"></i> {{ $catalogStats['work_statuses']['active'] }}/{{ $catalogStats['work_statuses']['total'] }}
                                </span>
                                <h5 class="description-header">Estados de Trabajo</h5>
                                <span class="description-text">{{ $catalogStats['work_statuses']['final_states'] }} ESTADOS FINALES</span>
                                <a href="{{ route('admin.catalogs.work-statuses') }}" class="btn btn-xs btn-outline-success mt-2">
                                    <i class="fas fa-cog"></i> Gestionar
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block border-right">
                                <span class="description-percentage text-warning">
                                    <i class="fas fa-university"></i> {{ $catalogStats['organizational_units']['active'] }}/{{ $catalogStats['organizational_units']['total'] }}
                                </span>
                                <h5 class="description-header">Unidades Organizacionales</h5>
                                <span class="description-text">{{ $catalogStats['organizational_units']['with_users'] }} CON USUARIOS</span>
                                <a href="{{ route('admin.catalogs.organizational-units') }}" class="btn btn-xs btn-outline-warning mt-2">
                                    <i class="fas fa-cog"></i> Gestionar
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="description-block">
                                <span class="description-percentage text-info">
                                    <i class="fas fa-layer-group"></i> {{ $catalogStats['institutional_project_types']['active'] }}/{{ $catalogStats['institutional_project_types']['total'] }}
                                </span>
                                <h5 class="description-header">Tipos de Proyectos</h5>
                                <span class="description-text">{{ $catalogStats['institutional_project_types']['with_projects'] }} CON PROYECTOS</span>
                                <a href="{{ route('admin.catalogs.institutional-project-types') }}" class="btn btn-xs btn-outline-info mt-2">
                                    <i class="fas fa-cog"></i> Gestionar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Distribución de Trabajos por Estado --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Distribución de Trabajos por Estado
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($worksByStatus->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Estado</th>
                                        <th class="text-center">Trabajos</th>
                                        <th class="text-center">%</th>
                                        <th class="text-center">Progreso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($worksByStatus as $status)
                                        @php
                                            $percentage = $stats['total_works'] > 0 ? round(($status->work_of_extensions_count / $stats['total_works']) * 100, 1) : 0;
                                            $colorClass = $status->is_final ? 'success' : ($status->is_active ? 'primary' : 'secondary');
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge badge-{{ $colorClass }}" style="background-color: {{ $status->color ?? '#6c757d' }}">
                                                    {{ $status->name }}
                                                </span>
                                                @if($status->is_final)
                                                    <i class="fas fa-flag text-success ml-1" title="Estado final"></i>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $status->work_of_extensions_count }}</td>
                                            <td class="text-center">{{ $percentage }}%</td>
                                            <td>
                                                <div class="progress progress-xs">
                                                    <div class="progress-bar bg-{{ $colorClass }}" style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No hay estados de trabajo configurados.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Actividad Reciente del Sistema --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-1"></i>
                        Actividad Reciente (7 días)
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="description-block">
                                <span class="description-percentage text-success">
                                    <i class="fas fa-user-plus"></i> {{ $systemActivity['recent_users_count'] }}
                                </span>
                                <h5 class="description-header">Nuevos Usuarios</h5>
                                <span class="description-text">Últimos 7 días</span>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="description-block">
                                <span class="description-percentage text-info">
                                    <i class="fas fa-file-plus"></i> {{ $systemActivity['recent_works_count'] }}
                                </span>
                                <h5 class="description-header">Nuevos Trabajos</h5>
                                <span class="description-text">Últimos 7 días</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="description-block">
                                <span class="description-percentage text-warning">
                                    <i class="fas fa-hourglass-half"></i> {{ $systemActivity['pending_works'] }}
                                </span>
                                <h5 class="description-header">Trabajos Pendientes</h5>
                                <span class="description-text">Requieren acción</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Acciones Rápidas de Administración --}}
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools mr-1"></i>
                        Acciones Rápidas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm btn-block">
                                <i class="fas fa-user-plus"></i> Crear Usuario
                            </a>
                        </div>
                        <div class="col-12 mb-2">
                            <a href="{{ route('admin.catalogs.index') }}" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-database"></i> Gestionar Catálogos
                            </a>
                        </div>
                        <div class="col-12 mb-2">
                            <a href="{{ route('admin.catalogs.organizational-units') }}" class="btn btn-warning btn-sm btn-block">
                                <i class="fas fa-building"></i> Nueva Unidad
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('admin.system.health') }}" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-chart-bar"></i> Estado del Sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Usuarios y Trabajos Recientes --}}
    <div class="row">
        {{-- Distribución de Usuarios por Rol --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Distribución de Usuarios por Rol
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($usersByRole->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Rol</th>
                                        <th class="text-center">Usuarios</th>
                                        <th class="text-center">%</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usersByRole as $role)
                                        @php
                                            $percentage = $stats['total_users'] > 0 ? round(($role->users_count / $stats['total_users']) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge badge-primary">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                            </td>
                                            <td class="text-center">{{ $role->users_count }}</td>
                                            <td class="text-center">{{ $percentage }}%</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.users.index', ['role' => $role->name]) }}" class="btn btn-xs btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">No hay roles configurados en el sistema.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Trabajos de Extensión Recientes --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-1"></i>
                        Trabajos de Extensión Recientes
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('works.index') }}" class="btn btn-tool">
                            <i class="fas fa-eye"></i> Ver todos
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($recentWorks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Autor</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentWorks as $work)
                                        <tr>
                                            <td>
                                                <a href="{{ route('works.show', $work) }}" class="text-primary">
                                                    {{ Str::limit($work->title, 30) }}
                                                </a>
                                            </td>
                                            <td>{{ $work->primaryResponsibleUser->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($work->currentStatus)
                                                    <span class="badge badge-info" style="background-color: {{ $work->currentStatus->color ?? '#17a2b8' }}">
                                                        {{ $work->currentStatus->name }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">Sin estado</span>
                                                @endif
                                            </td>
                                            <td class="text-muted">{{ $work->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="p-3 text-muted text-center">No hay trabajos recientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Enlaces de Gestión de Catálogos --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database mr-1"></i>
                        Acceso Rápido a Catálogos del Sistema
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.catalogs.index') }}" class="btn btn-tool">
                            <i class="fas fa-cogs"></i> Panel Principal de Catálogos
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.catalogs.work-types') }}" class="btn btn-outline-primary btn-block text-center p-3">
                                <i class="fas fa-tags fa-2x d-block mb-2"></i>
                                <strong>Tipos de Trabajo</strong><br>
                                <small class="text-muted">{{ $catalogStats['work_types']['active'] }} activos de {{ $catalogStats['work_types']['total'] }}</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.catalogs.work-statuses') }}" class="btn btn-outline-success btn-block text-center p-3">
                                <i class="fas fa-traffic-light fa-2x d-block mb-2"></i>
                                <strong>Estados de Trabajo</strong><br>
                                <small class="text-muted">{{ $catalogStats['work_statuses']['active'] }} activos de {{ $catalogStats['work_statuses']['total'] }}</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.catalogs.institutional-project-types') }}" class="btn btn-outline-warning btn-block text-center p-3">
                                <i class="fas fa-layer-group fa-2x d-block mb-2"></i>
                                <strong>Tipos de Proyectos</strong><br>
                                <small class="text-muted">{{ $catalogStats['institutional_project_types']['active'] }} activos de {{ $catalogStats['institutional_project_types']['total'] }}</small>
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <a href="{{ route('admin.catalogs.organizational-units') }}" class="btn btn-outline-info btn-block text-center p-3">
                                <i class="fas fa-university fa-2x d-block mb-2"></i>
                                <strong>Unidades Organizacionales</strong><br>
                                <small class="text-muted">{{ $catalogStats['organizational_units']['active'] }} activas de {{ $catalogStats['organizational_units']['total'] }}</small>
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

.small-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease-in-out;
}

.info-box-more {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #007bff;
    text-decoration: none;
    margin-top: 5px;
}

.info-box-more:hover {
    color: #0056b3;
    text-decoration: none;
}

.users-list > li {
    width: 25%;
    float: left;
    padding: 10px;
    text-align: center;
}

.users-list-roles {
    margin-top: 5px;
}

.users-list-roles .badge {
    font-size: 10px;
    margin: 1px;
}

.card-title {
    font-weight: 600;
}

.description-block {
    margin: 0;
    padding: 10px;
    text-align: center;
}

.description-block h5 {
    font-size: 14px;
    font-weight: 600;
    margin: 8px 0 4px 0;
}

.description-percentage {
    font-size: 18px;
    font-weight: 700;
    display: block;
    margin-bottom: 5px;
}

.description-text {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-warning:hover,
.btn-outline-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease-in-out;
}

.progress-xs {
    height: 8px;
}

.card {
    transition: all 0.3s ease-in-out;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Animaciones para las tarjetas de catálogos */
.btn-block.text-center {
    transition: all 0.3s ease-in-out;
    border-radius: 10px;
    padding: 20px;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.btn-block.text-center:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

.btn-block.text-center i {
    transition: all 0.3s ease-in-out;
}

.btn-block.text-center:hover i {
    transform: scale(1.2);
}

/* Badges personalizados para estados */
.badge[style*="background-color"] {
    border: none;
    color: white !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

/* Mejoras responsivas */
@media (max-width: 768px) {
    .small-box {
        margin-bottom: 20px;
    }
    
    .info-box {
        margin-bottom: 15px;
    }
    
    .btn-block.text-center {
        min-height: 100px;
        padding: 15px;
    }
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

    // Tooltip para acciones
    $('[data-toggle="tooltip"]').tooltip();
    
    // Efecto contador animado para números
    $('.info-box-number, .description-header, .small-box h3').each(function() {
        const $this = $(this);
        const countTo = parseInt($this.text().replace(/[^0-9]/g, '')) || 0;
        
        if (countTo > 0) {
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum).toLocaleString());
                },
                complete: function() {
                    $this.text(countTo.toLocaleString());
                }
            });
        }
    });
    
    // Animación de entrada para las tarjetas
    $('.card').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(20px)'
        }).delay(index * 100).animate({
            'opacity': '1'
        }, 600).css('transform', 'translateY(0px)');
    });
    
    // Efecto hover para las tarjetas de catálogos
    $('.btn-block.text-center').hover(
        function() {
            $(this).find('i').addClass('fa-spin');
        },
        function() {
            $(this).find('i').removeClass('fa-spin');
        }
    );
    
    // Actualización automática de estadísticas cada 5 minutos
    setInterval(function() {
        // Aquí podrías agregar AJAX para actualizar estadísticas en tiempo real
        console.log('Actualizando estadísticas...');
    }, 300000); // 5 minutos
    
    // Efecto de progreso para las barras
    $('.progress-bar').each(function() {
        const $bar = $(this);
        const width = $bar.attr('style').match(/width:\s*(\d+(?:\.\d+)?)%/);
        if (width) {
            $bar.css('width', '0%').animate({
                width: width[1] + '%'
            }, 1500);
        }
    });
    
    // Mostrar información adicional al hacer hover en badges de estado
    $('.badge[style*="background-color"]').tooltip({
        title: function() {
            return 'Estado: ' + $(this).text();
        },
        placement: 'top'
    });
});
</script>
@stop