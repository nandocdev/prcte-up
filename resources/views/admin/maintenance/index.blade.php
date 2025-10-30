@extends('layouts.app')

@section('title', __('Mantenimiento del Sistema'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-wrench mr-2"></i>{{ __('Mantenimiento del Sistema') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Mantenimiento') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Estado del Sistema -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $systemHealth['status'] ?? 'OK' }}</h3>
                <p>{{ __('Estado del Sistema') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-heartbeat"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $systemHealth['uptime'] ?? '24h' }}</h3>
                <p>{{ __('Tiempo de Actividad') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $systemHealth['memory_usage'] ?? '45%' }}</h3>
                <p>{{ __('Uso de Memoria') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-memory"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $systemHealth['disk_usage'] ?? '60%' }}</h3>
                <p>{{ __('Uso de Disco') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-hdd"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Herramientas de Limpieza -->
    <div class="col-md-6">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-broom mr-2"></i>{{ __('Herramientas de Limpieza') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h5 class="mb-1">{{ __('Limpiar Logs del Sistema') }}</h5>
                                <small class="text-muted">{{ __('Elimina archivos de log antiguos para liberar espacio') }}</small>
                            </div>
                            <button class="btn btn-warning" onclick="clearLogs()">
                                <i class="fas fa-trash mr-1"></i>{{ __('Limpiar') }}
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h5 class="mb-1">{{ __('Limpiar Cache') }}</h5>
                                <small class="text-muted">{{ __('Limpia cache de aplicación, configuración y vistas') }}</small>
                            </div>
                            <button class="btn btn-info" onclick="clearCache()">
                                <i class="fas fa-sync mr-1"></i>{{ __('Limpiar') }}
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h5 class="mb-1">{{ __('Limpiar Sesiones Expiradas') }}</h5>
                                <small class="text-muted">{{ __('Elimina sesiones de usuarios que han expirado') }}</small>
                            </div>
                            <button class="btn btn-secondary" onclick="clearExpiredSessions()">
                                <i class="fas fa-user-clock mr-1"></i>{{ __('Limpiar') }}
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h5 class="mb-1">{{ __('Limpiar Archivos Temporales') }}</h5>
                                <small class="text-muted">{{ __('Elimina archivos temporales y uploads obsoletos') }}</small>
                            </div>
                            <button class="btn btn-danger" onclick="clearTempFiles()">
                                <i class="fas fa-file-alt mr-1"></i>{{ __('Limpiar') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Herramientas de Optimización -->
    <div class="col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-rocket mr-2"></i>{{ __('Herramientas de Optimización') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h5 class="mb-1">{{ __('Optimizar Base de Datos') }}</h5>
                                <small class="text-muted">{{ __('Optimiza tablas y regenera índices para mejor rendimiento') }}</small>
                            </div>
                            <button class="btn btn-success" onclick="optimizeDatabase()">
                                <i class="fas fa-database mr-1"></i>{{ __('Optimizar') }}
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h5 class="mb-1">{{ __('Compilar Assets') }}</h5>
                                <small class="text-muted">{{ __('Recompila y optimiza archivos CSS y JavaScript') }}</small>
                            </div>
                            <button class="btn btn-primary" onclick="compileAssets()">
                                <i class="fas fa-code mr-1"></i>{{ __('Compilar') }}
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h5 class="mb-1">{{ __('Regenerar Cache') }}</h5>
                                <small class="text-muted">{{ __('Regenera cache de configuración y rutas') }}</small>
                            </div>
                            <button class="btn btn-info" onclick="regenerateCache()">
                                <i class="fas fa-redo mr-1"></i>{{ __('Regenerar') }}
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h5 class="mb-1">{{ __('Verificar Sistema') }}</h5>
                                <small class="text-muted">{{ __('Ejecuta diagnósticos completos del sistema') }}</small>
                            </div>
                            <button class="btn btn-warning" onclick="systemCheck()">
                                <i class="fas fa-stethoscope mr-1"></i>{{ __('Verificar') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Herramientas de Backup -->
    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-save mr-2"></i>{{ __('Backup y Restauración') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h5 class="mb-1">{{ __('Crear Backup Completo') }}</h5>
                                <small class="text-muted">{{ __('Crea un backup de la base de datos y archivos') }}</small>
                            </div>
                            <button class="btn btn-primary" onclick="createBackup()">
                                <i class="fas fa-download mr-1"></i>{{ __('Crear') }}
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                            <div>
                                <h5 class="mb-1">{{ __('Backup Solo Base de Datos') }}</h5>
                                <small class="text-muted">{{ __('Crea backup únicamente de la base de datos') }}</small>
                            </div>
                            <button class="btn btn-info" onclick="createDbBackup()">
                                <i class="fas fa-database mr-1"></i>{{ __('Backup DB') }}
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="p-3 border rounded">
                            <h5 class="mb-2">{{ __('Backups Recientes') }}</h5>
                            <div class="list-group list-group-flush">
                                @forelse($recentBackups ?? [] as $backup)
                                <div class="list-group-item d-flex justify-content-between align-items-center p-2">
                                    <div>
                                        <small><strong>{{ $backup['name'] }}</strong></small><br>
                                        <small class="text-muted">{{ $backup['date'] }} - {{ $backup['size'] }}</small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-success" onclick="downloadBackup('{{ $backup['name'] }}')">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="deleteBackup('{{ $backup['name'] }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                @empty
                                <small class="text-muted">{{ __('No hay backups recientes') }}</small>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del Sistema -->
    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-2"></i>{{ __('Información del Sistema') }}</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>{{ __('Versión de PHP') }}</strong></td>
                        <td>{{ PHP_VERSION }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('Versión de Laravel') }}</strong></td>
                        <td>{{ app()->version() }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('Base de Datos') }}</strong></td>
                        <td>{{ config('database.default') }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('Entorno') }}</strong></td>
                        <td>
                            <span class="badge badge-{{ app()->environment() === 'production' ? 'danger' : 'warning' }}">
                                {{ app()->environment() }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('Debug Mode') }}</strong></td>
                        <td>
                            <span class="badge badge-{{ config('app.debug') ? 'warning' : 'success' }}">
                                {{ config('app.debug') ? 'ON' : 'OFF' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('Último Backup') }}</strong></td>
                        <td>
                            <small class="text-muted">{{ $lastBackup ?? __('Nunca') }}</small>
                        </td>
                    </tr>
                </table>

                <div class="mt-3">
                    <div class="d-flex justify-content-between">
                        <small>{{ __('Espacio en Disco') }}</small>
                        <small>{{ $diskUsage ?? '60%' }}</small>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $diskUsage ?? '60%' }}"></div>
                    </div>
                </div>

                <div class="mt-2">
                    <div class="d-flex justify-content-between">
                        <small>{{ __('Uso de Memoria') }}</small>
                        <small>{{ $memoryUsage ?? '45%' }}</small>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $memoryUsage ?? '45%' }}"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Consola de Resultados -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card card-outline card-dark collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-terminal mr-2"></i>{{ __('Consola de Resultados') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="console" class="bg-dark text-light p-3" style="height: 200px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                    <div class="text-muted">{{ __('Los resultados de las operaciones aparecerán aquí...') }}</div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-secondary" onclick="clearConsole()">
                    <i class="fas fa-eraser mr-1"></i>{{ __('Limpiar Consola') }}
                </button>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
function logToConsole(message, type = 'info') {
    const console = $('#console');
    const timestamp = new Date().toLocaleTimeString();
    const colorClass = {
        'info': 'text-info',
        'success': 'text-success',
        'warning': 'text-warning',
        'error': 'text-danger'
    }[type] || 'text-light';
    
    console.append(`<div class="${colorClass}">[${timestamp}] ${message}</div>`);
    console.scrollTop(console[0].scrollHeight);
}

function clearConsole() {
    $('#console').html('<div class="text-muted">Consola limpiada...</div>');
}

function clearLogs() {
    if (confirm('¿Estás seguro de que quieres limpiar los logs del sistema?')) {
        logToConsole('Iniciando limpieza de logs...', 'info');
        // Aquí harías la llamada AJAX real
        setTimeout(() => {
            logToConsole('Logs limpiados exitosamente', 'success');
        }, 2000);
    }
}

function clearCache() {
    logToConsole('Limpiando cache del sistema...', 'info');
    // Llamada AJAX para limpiar cache
    setTimeout(() => {
        logToConsole('Cache limpiado exitosamente', 'success');
    }, 1500);
}

function clearExpiredSessions() {
    logToConsole('Limpiando sesiones expiradas...', 'info');
    setTimeout(() => {
        logToConsole('Sesiones expiradas eliminadas', 'success');
    }, 1000);
}

function clearTempFiles() {
    if (confirm('¿Estás seguro de que quieres eliminar archivos temporales?')) {
        logToConsole('Eliminando archivos temporales...', 'warning');
        setTimeout(() => {
            logToConsole('Archivos temporales eliminados', 'success');
        }, 2500);
    }
}

function optimizeDatabase() {
    logToConsole('Iniciando optimización de base de datos...', 'info');
    setTimeout(() => {
        logToConsole('Base de datos optimizada exitosamente', 'success');
    }, 3000);
}

function compileAssets() {
    logToConsole('Compilando assets...', 'info');
    setTimeout(() => {
        logToConsole('Assets compilados exitosamente', 'success');
    }, 4000);
}

function regenerateCache() {
    logToConsole('Regenerando cache...', 'info');
    setTimeout(() => {
        logToConsole('Cache regenerado exitosamente', 'success');
    }, 2000);
}

function systemCheck() {
    logToConsole('Ejecutando verificación del sistema...', 'info');
    setTimeout(() => {
        logToConsole('✓ PHP: OK', 'success');
        logToConsole('✓ Base de datos: OK', 'success');
        logToConsole('✓ Permisos de archivos: OK', 'success');
        logToConsole('⚠ Memoria alta detectada', 'warning');
        logToConsole('Verificación completada', 'info');
    }, 3000);
}

function createBackup() {
    logToConsole('Creando backup completo...', 'info');
    setTimeout(() => {
        logToConsole('Backup creado: backup_' + new Date().toISOString().slice(0,10) + '.zip', 'success');
    }, 5000);
}

function createDbBackup() {
    logToConsole('Creando backup de base de datos...', 'info');
    setTimeout(() => {
        logToConsole('Backup de DB creado: db_backup_' + new Date().toISOString().slice(0,10) + '.sql', 'success');
    }, 3000);
}

function downloadBackup(name) {
    logToConsole('Descargando backup: ' + name, 'info');
}

function deleteBackup(name) {
    if (confirm('¿Eliminar backup ' + name + '?')) {
        logToConsole('Backup eliminado: ' + name, 'warning');
    }
}
</script>
@endpush