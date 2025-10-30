@extends('layouts.app')

@section('title', __('Auditoría del Sistema'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-search mr-2"></i>{{ __('Auditoría del Sistema') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Auditoría') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Estadísticas Rápidas de Auditoría -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $auditStats['total_logs'] ?? 0 }}</h3>
                <p>{{ __('Registros de Auditoría') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-list-alt"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $auditStats['users_active_today'] ?? 0 }}</h3>
                <p>{{ __('Usuarios Activos Hoy') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $auditStats['errors_today'] ?? 0 }}</h3>
                <p>{{ __('Errores Hoy') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $auditStats['failed_logins_today'] ?? 0 }}</h3>
                <p>{{ __('Intentos de Login Fallidos') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Filtros de Auditoría -->
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-2"></i>{{ __('Filtros de Auditoría') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.audit.index') }}" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_from">{{ __('Fecha Desde') }}</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_to">{{ __('Fecha Hasta') }}</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="event_type">{{ __('Tipo de Evento') }}</label>
                            <select class="form-control" id="event_type" name="event_type">
                                <option value="">{{ __('Todos') }}</option>
                                <option value="login" {{ request('event_type') == 'login' ? 'selected' : '' }}>{{ __('Login') }}</option>
                                <option value="logout" {{ request('event_type') == 'logout' ? 'selected' : '' }}>{{ __('Logout') }}</option>
                                <option value="create" {{ request('event_type') == 'create' ? 'selected' : '' }}>{{ __('Crear') }}</option>
                                <option value="update" {{ request('event_type') == 'update' ? 'selected' : '' }}>{{ __('Actualizar') }}</option>
                                <option value="delete" {{ request('event_type') == 'delete' ? 'selected' : '' }}>{{ __('Eliminar') }}</option>
                                <option value="error" {{ request('event_type') == 'error' ? 'selected' : '' }}>{{ __('Error') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="user_id">{{ __('Usuario') }}</label>
                            <select class="form-control" id="user_id" name="user_id">
                                <option value="">{{ __('Todos') }}</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>{{ __('Filtrar') }}
                        </button>
                        <a href="{{ route('admin.audit.index') }}" class="btn btn-secondary">
                            <i class="fas fa-eraser mr-1"></i>{{ __('Limpiar') }}
                        </a>
                        <a href="{{ route('admin.audit.export') }}" class="btn btn-success">
                            <i class="fas fa-download mr-1"></i>{{ __('Exportar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Tabla de Auditoría -->
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list mr-2"></i>{{ __('Registro de Auditoría') }}</h3>
                <div class="card-tools">
                    <span class="badge badge-primary">{{ $auditLogs->total() ?? 0 }} {{ __('registros') }}</span>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>{{ __('Fecha/Hora') }}</th>
                            <th>{{ __('Usuario') }}</th>
                            <th>{{ __('Evento') }}</th>
                            <th>{{ __('Descripción') }}</th>
                            <th>{{ __('IP') }}</th>
                            <th>{{ __('User Agent') }}</th>
                            <th>{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($auditLogs ?? [] as $log)
                        <tr>
                            <td>
                                <span class="text-muted">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                            </td>
                            <td>
                                @if($log->user)
                                    <span class="badge badge-info">{{ $log->user->name }}</span>
                                @else
                                    <span class="text-muted">{{ __('Sistema') }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $eventClass = [
                                        'login' => 'success',
                                        'logout' => 'info',
                                        'create' => 'primary',
                                        'update' => 'warning',
                                        'delete' => 'danger',
                                        'error' => 'danger'
                                    ][$log->event_type] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $eventClass }}">{{ $log->event_type }}</span>
                            </td>
                            <td>
                                <small>{{ Str::limit($log->description, 50) }}</small>
                            </td>
                            <td>
                                <code class="text-sm">{{ $log->ip_address }}</code>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($log->user_agent, 30) }}</small>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" onclick="showLogDetails({{ $log->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-3">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                {{ __('No hay registros de auditoría') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(isset($auditLogs) && $auditLogs->hasPages())
            <div class="card-footer">
                {{ $auditLogs->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Detalles del Log -->
<div class="modal fade" id="logDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Detalles del Registro de Auditoría') }}</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="logDetailsContent">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cerrar') }}</button>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
function showLogDetails(logId) {
    // Aquí implementarías la llamada AJAX para obtener los detalles
    $('#logDetailsModal').modal('show');
    $('#logDetailsContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>');
    
    // Simulación de carga (reemplazar con AJAX real)
    setTimeout(() => {
        $('#logDetailsContent').html(`
            <div class="row">
                <div class="col-md-6">
                    <strong>ID:</strong> ${logId}<br>
                    <strong>Evento:</strong> Login<br>
                    <strong>Usuario:</strong> Juan Pérez<br>
                    <strong>Fecha:</strong> ${new Date().toLocaleString()}
                </div>
                <div class="col-md-6">
                    <strong>IP:</strong> 192.168.1.100<br>
                    <strong>User Agent:</strong> Mozilla/5.0...<br>
                    <strong>Datos Adicionales:</strong> JSON...
                </div>
            </div>
        `);
    }, 500);
}
</script>
@endpush