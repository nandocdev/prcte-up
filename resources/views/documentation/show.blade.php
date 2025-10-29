@extends('layouts.app')

@section('title', $section['title'] . ' - Documentación VIEX')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0">
                <i class="{{ $section['icon'] }} text-primary"></i> {{ $section['title'] }}
            </h1>
            <small class="text-muted">{{ $section['description'] }}</small>
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
        <!-- Navegación Lateral -->
        <div class="col-md-3">
            <div class="card card-primary card-outline sticky-top">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Navegación
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @foreach($navigationSections as $navSection)
                            <li class="nav-item">
                                <a href="{{ route('documentation.show', $navSection['key']) }}" 
                                   class="nav-link {{ $currentSection === $navSection['key'] ? 'active' : '' }}">
                                    <i class="{{ $navSection['icon'] }} mr-2"></i>
                                    {{ $navSection['title'] }}
                                </a>
                                @if($currentSection === $navSection['key'] && isset($navSection['subsections']) && count($navSection['subsections']) > 0)
                                    <ul class="nav nav-treeview ml-3">
                                        @foreach($navSection['subsections'] as $subKey => $subTitle)
                                            <li class="nav-item">
                                                <a href="#{{ $subKey }}" class="nav-link nav-link-sm subsection-link">
                                                    <i class="fas fa-angle-right mr-2"></i>
                                                    {{ $subTitle }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    @include('documentation.sections.' . $currentSection)
                </div>
            </div>

            <!-- Navegación entre secciones -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $currentOrder = $section['order'];
                                $prevSection = collect($sections)->where('order', $currentOrder - 1)->first();
                                $prevKey = $prevSection ? array_search($prevSection, $sections) : null;
                            @endphp
                            @if($prevKey)
                                <a href="{{ route('documentation.show', $prevKey) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-chevron-left"></i> 
                                    {{ $sections[$prevKey]['title'] }}
                                </a>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('documentation.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-home"></i> Índice
                            </a>
                        </div>
                        <div>
                            @php
                                $nextSection = collect($sections)->where('order', $currentOrder + 1)->first();
                                $nextKey = $nextSection ? array_search($nextSection, $sections) : null;
                            @endphp
                            @if($nextKey)
                                <a href="{{ route('documentation.show', $nextKey) }}" class="btn btn-outline-primary">
                                    {{ $sections[$nextKey]['title'] }} 
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enlaces de Ayuda -->
            <div class="card mt-3 card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-question-circle"></i> ¿Necesitas más ayuda?
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-search text-primary"></i> Buscar más información</h6>
                            <form action="{{ route('documentation.search') }}" method="GET" class="mb-3">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="q" class="form-control" 
                                           placeholder="Buscar en documentación...">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-life-ring text-warning"></i> Soporte técnico</h6>
                            <p class="small text-muted mb-1">
                                <i class="fas fa-envelope"></i> soporte.viex@up.ac.pa
                            </p>
                            <p class="small text-muted mb-1">
                                <i class="fas fa-phone"></i> Ext. 2450 (Horario laboral)
                            </p>
                            <a href="{{ route('documentation.show', 'troubleshooting') }}" class="btn btn-sm btn-outline-warning">
                                Ver Resolución de Problemas
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
.sticky-top {
    top: 70px; /* Ajustar según la altura del header */
}

.subsection-link {
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
}

.subsection-link:hover {
    background-color: rgba(0,123,255,0.1);
}

/* Smooth scrolling para anclas */
html {
    scroll-behavior: smooth;
}

/* Highlighting para secciones activas */
.section-highlight {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    background-color: rgba(0,123,255,0.05);
}

/* Estilo para código inline */
code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 87.5%;
    color: #e83e8c;
}

/* Estilo para bloques de código */
pre {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1rem;
    overflow-x: auto;
}

/* Estilo para alertas */
.doc-alert {
    border-left: 4px solid;
    padding: 0.75rem 1rem;
    margin: 1rem 0;
    border-radius: 0 0.375rem 0.375rem 0;
}

.doc-alert.info {
    border-color: #17a2b8;
    background-color: rgba(23, 162, 184, 0.1);
    color: #0c5460;
}

.doc-alert.warning {
    border-color: #ffc107;
    background-color: rgba(255, 193, 7, 0.1);
    color: #856404;
}

.doc-alert.success {
    border-color: #28a745;
    background-color: rgba(40, 167, 69, 0.1);
    color: #155724;
}

.doc-alert.danger {
    border-color: #dc3545;
    background-color: rgba(220, 53, 69, 0.1);
    color: #721c24;
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Smooth scrolling para enlaces de subsecciones
    $('.subsection-link').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 500);
            
            // Highlight temporal de la sección
            target.addClass('section-highlight');
            setTimeout(function() {
                target.removeClass('section-highlight');
            }, 3000);
        }
    });
    
    // Actualizar navegación activa al hacer scroll
    $(window).on('scroll', function() {
        var scrollTop = $(window).scrollTop();
        
        $('.subsection-link').each(function() {
            var href = $(this).attr('href');
            if (href.startsWith('#')) {
                var target = $(href);
                if (target.length) {
                    var targetTop = target.offset().top - 100;
                    var targetBottom = targetTop + target.outerHeight();
                    
                    if (scrollTop >= targetTop && scrollTop < targetBottom) {
                        $('.subsection-link').removeClass('active');
                        $(this).addClass('active');
                    }
                }
            }
        });
    });
});
</script>
@stop