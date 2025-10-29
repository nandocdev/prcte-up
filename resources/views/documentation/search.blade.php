@extends('layouts.app')

@section('title', 'Buscar en Documentación - Sistema VIEX')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0">
                <i class="fas fa-search text-primary"></i> 
                Buscar en Documentación
            </h1>
            @if($query)
                <small class="text-muted">Resultados para: "{{ $query }}"</small>
            @endif
        </div>
        <div>
            <a href="{{ route('documentation.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Índice
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Búsqueda -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('documentation.search') }}" method="GET">
                        <div class="input-group input-group-lg">
                            <input type="text" name="q" class="form-control" 
                                   placeholder="Buscar en la documentación..." 
                                   value="{{ $query }}"
                                   autofocus>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Sugerencias de búsqueda -->
                    <div class="mt-3">
                        <small class="text-muted"><strong>Sugerencias de búsqueda:</strong></small>
                        <div class="mt-2">
                            @php
                                $suggestions = [
                                    'crear trabajo' => 'Cómo crear un nuevo trabajo de extensión',
                                    'certificado' => 'Información sobre certificados',
                                    'coordinador' => 'Funciones del coordinador',
                                    'aprobar trabajo' => 'Proceso de aprobación',
                                    'evaluador' => 'Información para evaluadores',
                                    'notificaciones' => 'Sistema de notificaciones',
                                    'reportes' => 'Generar reportes',
                                    'problemas' => 'Resolver problemas comunes'
                                ];
                            @endphp
                            @foreach($suggestions as $term => $description)
                                <a href="{{ route('documentation.search') }}?q={{ urlencode($term) }}" 
                                   class="badge badge-secondary mr-2 mb-2 p-2 text-decoration-none"
                                   title="{{ $description }}">
                                    {{ $term }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resultados -->
        <div class="col-12">
            @if($query)
                @if(count($results) > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list-ul"></i> 
                                Resultados de búsqueda ({{ count($results) }})
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($results as $result)
                                    <a href="{{ $result['url'] }}" 
                                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
                                        <div class="ms-2 me-auto">
                                            <div class="d-flex align-items-center mb-1">
                                                @if($result['type'] === 'section')
                                                    <i class="fas fa-book text-primary mr-2"></i>
                                                    <span class="badge badge-primary badge-sm mr-2">Sección</span>
                                                @else
                                                    <i class="fas fa-bookmark text-secondary mr-2"></i>
                                                    <span class="badge badge-secondary badge-sm mr-2">Tema</span>
                                                @endif
                                                <strong>{{ $result['title'] }}</strong>
                                            </div>
                                            <p class="mb-1 text-muted">{{ $result['description'] }}</p>
                                            <small class="text-info">
                                                <i class="fas fa-link"></i> 
                                                Ir a {{ $result['type'] === 'section' ? 'sección' : 'tema' }}
                                            </small>
                                        </div>
                                        <div class="text-right">
                                            <div class="progress" style="width: 50px; height: 6px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ $result['relevance'] * 100 }}%"
                                                     title="Relevancia: {{ round($result['relevance'] * 100) }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ round($result['relevance'] * 100) }}%</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Sin resultados -->
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4>No se encontraron resultados</h4>
                            <p class="text-muted">No encontramos información relacionada con "<strong>{{ $query }}</strong>"</p>
                            
                            <div class="mt-4">
                                <h6>Sugerencias:</h6>
                                <ul class="list-unstyled text-muted">
                                    <li><i class="fas fa-check text-success"></i> Verifica la ortografía</li>
                                    <li><i class="fas fa-check text-success"></i> Usa términos más generales</li>
                                    <li><i class="fas fa-check text-success"></i> Prueba con sinónimos</li>
                                    <li><i class="fas fa-check text-success"></i> Usa las palabras clave sugeridas arriba</li>
                                </ul>
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('documentation.index') }}" class="btn btn-primary">
                                    <i class="fas fa-home"></i> Volver al Índice
                                </a>
                                <a href="{{ route('documentation.show', 'troubleshooting') }}" class="btn btn-outline-warning ml-2">
                                    <i class="fas fa-life-ring"></i> Ayuda y Soporte
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Sin término de búsqueda -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>Buscar en la Documentación</h4>
                        <p class="text-muted">Ingresa un término de búsqueda para encontrar información específica</p>
                        
                        <div class="row mt-4">
                            <div class="col-md-8 offset-md-2">
                                <h6>Búsquedas populares:</h6>
                                <div class="d-flex flex-wrap justify-content-center">
                                    <a href="{{ route('documentation.search') }}?q=crear+trabajo" class="btn btn-outline-primary btn-sm m-1">
                                        Crear trabajo
                                    </a>
                                    <a href="{{ route('documentation.search') }}?q=certificado" class="btn btn-outline-primary btn-sm m-1">
                                        Certificado
                                    </a>
                                    <a href="{{ route('documentation.search') }}?q=aprobar" class="btn btn-outline-primary btn-sm m-1">
                                        Aprobar
                                    </a>
                                    <a href="{{ route('documentation.search') }}?q=evaluación" class="btn btn-outline-primary btn-sm m-1">
                                        Evaluación
                                    </a>
                                    <a href="{{ route('documentation.search') }}?q=notificaciones" class="btn btn-outline-primary btn-sm m-1">
                                        Notificaciones
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
<style>
.badge-sm {
    font-size: 0.7rem;
    padding: 0.25em 0.5em;
}

.list-group-item-action:hover {
    background-color: rgba(0,123,255,0.05);
}

.progress {
    border-radius: 3px;
}

.search-suggestion {
    transition: all 0.2s ease;
}

.search-suggestion:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Highlight del término de búsqueda en los resultados
    @if($query)
        var searchTerm = '{{ $query }}';
        var regex = new RegExp('(' + searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        
        $('.list-group-item strong, .list-group-item p').each(function() {
            var text = $(this).html();
            var highlightedText = text.replace(regex, '<mark>$1</mark>');
            $(this).html(highlightedText);
        });
    @endif
    
    // Auto-focus en el campo de búsqueda
    $('input[name="q"]').focus().select();
});
</script>
@stop