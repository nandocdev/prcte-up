@extends('adminlte::page')

@section('title', 'Trabajos de Extensión - VIEX')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fas fa-clipboard-list text-primary"></i>
            Trabajos de Extensión
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Trabajos</li>
        </ol>
    </div>
</div>
@stop

@section('content')
{{-- Estadísticas Generales --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $statistics['total'] ?? 0 }}</h3>
                <p>Total de Trabajos</p>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $statistics['draft'] ?? 0 }}</h3>
                <p>En Borrador</p>
            </div>
            <div class="icon">
                <i class="fas fa-edit"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $statistics['in_review'] ?? 0 }}</h3>
                <p>En Revisión</p>
            </div>
            <div class="icon">
                <i class="fas fa-search"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $statistics['certified'] ?? 0 }}</h3>
                <p>Certificados</p>
            </div>
            <div class="icon">
                <i class="fas fa-certificate"></i>
            </div>
        </div>
    </div>
</div>

{{-- Botón de Acción Principal --}}
@can('create', App\Models\WorkOfExtension::class)
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('works.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-plus"></i>
                Registrar Nuevo Trabajo de Extensión
            </a>
        </div>
    </div>
@endcan

{{-- Filtros --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i>
                    Filtros
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('works.index') }}" id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Estado</label>
                                <select name="status" class="form-control"
                                    onchange="document.getElementById('filterForm').submit();">
                                    <option value="">Todos los estados</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Borrador
                                    </option>
                                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>
                                        Enviados</option>
                                    <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>En
                                        Revisión</option>
                                    <option value="certified" {{ request('status') == 'certified' ? 'selected' : '' }}>
                                        Certificados</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Tipo de Trabajo</label>
                                <select name="work_type" class="form-control"
                                    onchange="document.getElementById('filterForm').submit();">
                                    <option value="">Todos los tipos</option>
                                    <option value="1" {{ request('work_type') == '1' ? 'selected' : '' }}>Proyecto de
                                        Investigación</option>
                                    <option value="2" {{ request('work_type') == '2' ? 'selected' : '' }}>Educación
                                        Continua</option>
                                    <option value="3" {{ request('work_type') == '3' ? 'selected' : '' }}>Servicio Social
                                    </option>
                                    <option value="4" {{ request('work_type') == '4' ? 'selected' : '' }}>Asistencia
                                        Técnica</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Período Académico</label>
                                <select name="academic_period" class="form-control"
                                    onchange="document.getElementById('filterForm').submit();">
                                    <option value="">Todos los períodos</option>
                                    <option value="2024-I" {{ request('academic_period') == '2024-I' ? 'selected' : '' }}>
                                        2024-I</option>
                                    <option value="2024-II" {{ request('academic_period') == '2024-II' ? 'selected' : '' }}>2024-II</option>
                                    <option value="2025-I" {{ request('academic_period') == '2025-I' ? 'selected' : '' }}>
                                        2025-I</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Buscar</label>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Título o descripción..." value="{{ request('search') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('works.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Limpiar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Lista de Trabajos --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Lista de Trabajos de Extensión
                    <span class="badge badge-info ml-2">{{ $works->count() }}
                        {{ $works->count() === 1 ? 'trabajo' : 'trabajos' }}</span>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                @if($works->isEmpty())
                    <div class="text-center p-4">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay trabajos registrados</h4>
                        <p class="text-muted">
                            @can('create', App\Models\WorkOfExtension::class)
                                ¡Comienza registrando tu primer trabajo de extensión!
                            @else
                                No se encontraron trabajos con los filtros aplicados.
                            @endcan
                        </p>
                        @can('create', App\Models\WorkOfExtension::class)
                            <a href="{{ route('works.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Registrar Primer Trabajo
                            </a>
                        @endcan
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Período</th>
                                    <th>Fecha Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($works as $work)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div>
                                                    <strong>{{ Str::limit($work->title, 50) }}</strong>
                                                    @if($work->description)
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ Str::limit($work->description, 80) }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                {{ $work->workType->name ?? 'No definido' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'Borrador' => 'warning',
                                                    'Enviado a Coordinador' => 'info',
                                                    'En Revisión Coordinador' => 'primary',
                                                    'En Revisión Decano/Director' => 'primary',
                                                    'En Evaluación VIEX' => 'primary',
                                                    'Certificado' => 'success',
                                                    'Rechazado por Coordinador' => 'danger',
                                                    'Rechazado por Decano/Director' => 'danger',
                                                    'Rechazado por VIEX' => 'danger',
                                                    'Devuelto para Corrección' => 'warning'
                                                ];
                                                $statusName = $work->currentStatus->name ?? 'Sin estado';
                                                $statusColor = $statusColors[$statusName] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $statusColor }}">
                                                {{ $statusName }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $work->academic_period ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $work->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                @can('view', $work)
                                                    <a href="{{ route('works.show', $work) }}" class="btn btn-sm btn-info"
                                                        title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan

                                                @can('update', $work)
                                                    @if($work->is_draft === '1')
                                                        <a href="{{ route('works.edit', $work) }}" class="btn btn-sm btn-warning"
                                                            title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                @endcan

                                                @can('delete', $work)
                                                    @if($work->is_draft === '1')
                                                        <form action="{{ route('works.destroy', $work) }}" method="POST"                                            class="d-inline"
                                                            onsubmit="return confirm('¿Estás seguro de eliminar este trabajo?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan

                                                {{-- Botón de envío para trabajos en borrador --}}
                                                @can('update', $work)
                                                    @if($work->is_draft === '1')
                                                        <form action="{{ route('works.submit', $work) }}" method="POST" class="d-inline"
                                                            onsubmit="return confirm('¿Enviar este trabajo a revisión?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-success"
                                                                title="Enviar a revisión">
                                                                <i class="fas fa-paper-plane"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Paginación --}}
            @if(method_exists($works, 'links') && $works->hasPages())
                <div class="card-footer">
                    {{ $works->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .small-box .icon {
        transition: transform 0.3s ease-in-out;
    }
    .small-box:hover .icon {
        transform: scale(1.1);
    }
    .table td {
        vertical-align: middle;
    }
    .btn-group .btn {
        margin-right: 2px;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }

    .card-header .badge {
        font-size: 0.85em;
    }

    .table-responsive {
        border-radius: 0.25rem;
    }

    @media (max-width: 768px) {
        .btn-group {
            display: flex;
            flex-direction: column;
        }
        .btn-group .btn {
            margin-bottom: 2px;
            margin-right: 0;
        }
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // Inicializar tooltips
        $('[title]').tooltip();

        // Confirmación para eliminación
        $('form[onsubmit*="eliminar"]').off('submit').on('submit', function (e) {
            return confirm('¿Estás seguro de eliminar este trabajo?');
        });

        // Confirmación para envío
        $('form[onsubmit*="revisión"]').off('submit').on('submit', function (e) {
            return confirm('¿Enviar este trabajo a revisión?');
        });
    });
</script>
@stop
