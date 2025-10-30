@extends('layouts.app')

@section('title', __('Logs del Sistema'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-file-alt mr-2"></i>{{ __('Logs del Sistema') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.audit.index') }}">{{ __('Auditoría') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Logs') }}</li>
            </ol>
        </nav>
    </div>
@stop

@section('content')
<div class="row">
    <!-- Estadísticas de Logs -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $logStats['total_files'] ?? 0 }}</h3>
                <p>{{ __('Archivos de Log') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $logStats['total_size'] ?? '0 MB' }}</h3>
                <p>{{ __('Tamaño Total') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-hdd"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $logStats['errors_today'] ?? 0 }}</h3>
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
                <h3>{{ $logStats['warnings_today'] ?? 0 }}</h3>
                <p>{{ __('Advertencias Hoy') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Lista de Archivos de Log -->
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-folder-open mr-2"></i>{{ __('Archivos de Log') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-danger" onclick="clearAllLogs()">
                        <i class="fas fa-trash mr-1"></i>{{ __('Limpiar Todos') }}
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>{{ __('Archivo') }}</th>
                            <th>{{ __('Tamaño') }}</th>
                            <th>{{ __('Última Modificación') }}</th>
                            <th>{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logFiles ?? [] as $logFile)
                        <tr>
                            <td>
                                <i class="fas fa-file-alt text-info mr-2"></i>
                                <strong>{{ $logFile['name'] }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $logFile['size'] }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $logFile['modified'] }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.audit.logs.view', $logFile['name']) }}" 
                                       class="btn btn-outline-info" title="{{ __('Ver') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.audit.logs.view', $logFile['name']) }}?download=1" 
                                       class="btn btn-outline-success" title="{{ __('Descargar') }}">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button class="btn btn-outline-danger" 
                                            onclick="deleteLogFile('{{ $logFile['name'] }}')" 
                                            title="{{ __('Eliminar') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                {{ __('No hay archivos de log') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Visor de Log Seleccionado -->
    <div class="col-md-4">
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-eye mr-2"></i>{{ __('Vista Previa') }}</h3>
                <div class="card-tools">
                    <span id="selectedLogName" class="badge badge-info">{{ __('Ningún archivo seleccionado') }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="logPreview" class="bg-dark text-light p-3" style="height: 400px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                    <div class="text-center text-muted">
                        <i class="fas fa-mouse-pointer fa-2x mb-2"></i><br>
                        {{ __('Haz clic en un archivo para ver su contenido') }}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-secondary" onclick="refreshLogPreview()">
                    <i class="fas fa-sync mr-1"></i>{{ __('Actualizar') }}
                </button>
                <button class="btn btn-sm btn-info" onclick="searchInLog()">
                    <i class="fas fa-search mr-1"></i>{{ __('Buscar') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filtros de Log -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="card card-outline card-info collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-2"></i>{{ __('Filtros Avanzados') }}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="log_level">{{ __('Nivel de Log') }}</label>
                            <select class="form-control" id="log_level">
                                <option value="">{{ __('Todos') }}</option>
                                <option value="emergency">{{ __('Emergency') }}</option>
                                <option value="alert">{{ __('Alert') }}</option>
                                <option value="critical">{{ __('Critical') }}</option>
                                <option value="error">{{ __('Error') }}</option>
                                <option value="warning">{{ __('Warning') }}</option>
                                <option value="notice">{{ __('Notice') }}</option>
                                <option value="info">{{ __('Info') }}</option>
                                <option value="debug">{{ __('Debug') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date_filter">{{ __('Fecha') }}</label>
                            <input type="date" class="form-control" id="date_filter">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search_text">{{ __('Buscar Texto') }}</label>
                            <input type="text" class="form-control" id="search_text" placeholder="{{ __('Buscar en logs...') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-primary btn-block" onclick="applyLogFilters()">
                                <i class="fas fa-search mr-1"></i>{{ __('Filtrar') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
let currentLogFile = null;

function viewLogFile(fileName) {
    currentLogFile = fileName;
    $('#selectedLogName').text(fileName);
    
    // Simulación de carga del contenido del log
    $('#logPreview').html('<div class="text-center text-info"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>');
    
    // Aquí harías una llamada AJAX real para obtener el contenido del log
    setTimeout(() => {
        const sampleLogContent = `
[${new Date().toISOString()}] local.INFO: Usuario autenticado {"user_id":1,"email":"admin@up.ac.pa"}
[${new Date().toISOString()}] local.DEBUG: Ejecutando consulta SQL {"query":"SELECT * FROM users WHERE active = 1"}
[${new Date().toISOString()}] local.WARNING: Memoria alta detectada {"memory_usage":"85%"}
[${new Date().toISOString()}] local.ERROR: Error de conexión a base de datos {"error":"Connection timeout"}
[${new Date().toISOString()}] local.INFO: Trabajo de extensión creado {"work_id":123,"user_id":1}
[${new Date().toISOString()}] local.DEBUG: Cache limpiado exitosamente
[${new Date().toISOString()}] local.INFO: Usuario desconectado {"user_id":1,"session_duration":"45 minutos"}
        `.trim();
        
        $('#logPreview').html('<pre class="text-light mb-0">' + sampleLogContent + '</pre>');
    }, 1000);
}

function deleteLogFile(fileName) {
    if (confirm('¿Estás seguro de que quieres eliminar el archivo ' + fileName + '?')) {
        // Aquí harías la llamada AJAX para eliminar el archivo
        console.log('Eliminando archivo:', fileName);
        location.reload();
    }
}

function clearAllLogs() {
    if (confirm('¿Estás seguro de que quieres eliminar TODOS los archivos de log? Esta acción no se puede deshacer.')) {
        // Aquí harías la llamada AJAX para limpiar todos los logs
        console.log('Limpiando todos los logs');
        location.reload();
    }
}

function refreshLogPreview() {
    if (currentLogFile) {
        viewLogFile(currentLogFile);
    }
}

function searchInLog() {
    const searchTerm = prompt('Ingresa el texto a buscar:');
    if (searchTerm && currentLogFile) {
        // Aquí implementarías la búsqueda en el log actual
        console.log('Buscando:', searchTerm, 'en', currentLogFile);
    }
}

function applyLogFilters() {
    const level = $('#log_level').val();
    const date = $('#date_filter').val();
    const text = $('#search_text').val();
    
    // Aquí aplicarías los filtros
    console.log('Filtros:', {level, date, text});
}

// Event listeners para los enlaces de ver logs
$(document).ready(function() {
    $('a[href*="admin.audit.logs.view"]').click(function(e) {
        e.preventDefault();
        const fileName = $(this).attr('href').split('/').pop().split('?')[0];
        viewLogFile(fileName);
    });
});
</script>
@endpush