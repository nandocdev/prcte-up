@extends('layouts.app')

@section('title', 'Documentación - Sistema VIEX')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0"><i class="fas fa-book text-primary"></i> Documentación</h1>
            <small class="text-muted">Sistema de Registro y Certificación de Trabajos de Extensión</small>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
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
                    <form action="{{ route('documentation.search') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control form-control-lg" 
                                   placeholder="Buscar en la documentación..." 
                                   value="{{ request('q') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-lightbulb"></i> 
                        Busca por palabras clave como "crear trabajo", "certificado", "coordinador", etc.
                    </small>
                </div>
            </div>
        </div>

        <!-- Introducción -->
        <div class="col-12 mb-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Bienvenido a la Documentación de VIEX
                    </h3>
                </div>
                <div class="card-body">
                    <p class="lead">
                        Esta documentación te guiará paso a paso en el uso del Sistema VIEX, 
                        desde la creación de trabajos de extensión hasta la obtención de certificaciones oficiales.
                    </p>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Usuarios Activos</span>
                                    <span class="info-box-number">{{ \Spatie\Permission\Models\Role::count() }} Roles</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-book-open"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Secciones</span>
                                    <span class="info-box-number">{{ count($sections) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-life-ring"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Soporte</span>
                                    <span class="info-box-number">24/7</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navegación Rápida por Roles -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tachometer-alt"></i> Acceso Rápido por Rol
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(auth()->user()->hasRole('profesor'))
                            <div class="col-md-6 col-lg-4 mb-3">
                                <a href="{{ route('documentation.show', 'professors') }}" class="text-decoration-none">
                                    <div class="card bg-gradient-success h-100">
                                        <div class="card-body text-center text-white">
                                            <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                                            <h5>Guía para Profesores</h5>
                                            <p class="mb-0">Crear y gestionar trabajos de extensión</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                        
                        @if(auth()->user()->hasRole('coordinador_extension'))
                            <div class="col-md-6 col-lg-4 mb-3">
                                <a href="{{ route('documentation.show', 'coordinators') }}" class="text-decoration-none">
                                    <div class="card bg-gradient-info h-100">
                                        <div class="card-body text-center text-white">
                                            <i class="fas fa-user-tie fa-3x mb-3"></i>
                                            <h5>Guía para Coordinadores</h5>
                                            <p class="mb-0">Revisar y aprobar trabajos</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                        
                        @if(auth()->user()->hasRole('decano_director'))
                            <div class="col-md-6 col-lg-4 mb-3">
                                <a href="{{ route('documentation.show', 'deans') }}" class="text-decoration-none">
                                    <div class="card bg-gradient-warning h-100">
                                        <div class="card-body text-center text-white">
                                            <i class="fas fa-university fa-3x mb-3"></i>
                                            <h5>Guía para Decanos</h5>
                                            <p class="mb-0">Aprobación institucional</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                        
                        @if(auth()->user()->hasRole('viex_admin'))
                            <div class="col-md-6 col-lg-4 mb-3">
                                <a href="{{ route('documentation.show', 'viex-admin') }}" class="text-decoration-none">
                                    <div class="card bg-gradient-danger h-100">
                                        <div class="card-body text-center text-white">
                                            <i class="fas fa-star fa-3x mb-3"></i>
                                            <h5>Guía para VIEX</h5>
                                            <p class="mb-0">Evaluación y certificación</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                        
                        @if(auth()->user()->hasRole('evaluador'))
                            <div class="col-md-6 col-lg-4 mb-3">
                                <a href="{{ route('documentation.show', 'evaluators') }}" class="text-decoration-none">
                                    <div class="card bg-gradient-secondary h-100">
                                        <div class="card-body text-center text-white">
                                            <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                                            <h5>Guía para Evaluadores</h5>
                                            <p class="mb-0">Proceso de evaluación</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Todas las Secciones -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Todas las Secciones
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($sections as $key => $section)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <a href="{{ route('documentation.show', $key) }}" class="text-decoration-none">
                                    <div class="card card-outline card-primary h-100 hover-shadow">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <div class="mr-3">
                                                    <i class="{{ $section['icon'] }} fa-2x text-primary"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-1">{{ $section['title'] }}</h6>
                                                    <p class="card-text text-muted small mb-2">{{ $section['description'] }}</p>
                                                    @if(isset($section['subsections']) && count($section['subsections']) > 0)
                                                        <small class="text-info">
                                                            <i class="fas fa-list-ul"></i> 
                                                            {{ count($section['subsections']) }} temas
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Enlaces de Ayuda Rápida -->
        <div class="col-12 mt-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-question-circle"></i> ¿Necesitas ayuda inmediata?
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('documentation.show', 'getting-started') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-play-circle"></i><br>
                                <small>Primeros Pasos</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('documentation.show', 'troubleshooting') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-life-ring"></i><br>
                                <small>Problemas Comunes</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('documentation.search') }}?q=crear trabajo" class="btn btn-outline-success btn-block">
                                <i class="fas fa-plus-circle"></i><br>
                                <small>Crear Trabajo</small>
                            </a>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <a href="{{ route('documentation.search') }}?q=certificado" class="btn btn-outline-info btn-block">
                                <i class="fas fa-certificate"></i><br>
                                <small>Certificados</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
.hover-shadow {
    transition: box-shadow 0.3s ease;
}

.hover-shadow:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.card.bg-gradient-success:hover,
.card.bg-gradient-info:hover,
.card.bg-gradient-warning:hover,
.card.bg-gradient-danger:hover,
.card.bg-gradient-secondary:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.info-box {
    border-radius: 10px;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Agregar efectos de hover suaves
    $('.hover-shadow').hover(
        function() {
            $(this).addClass('shadow-lg');
        },
        function() {
            $(this).removeClass('shadow-lg');
        }
    );
    
    // Focus en el campo de búsqueda al cargar
    $('input[name="q"]').focus();
});
</script>
@stop