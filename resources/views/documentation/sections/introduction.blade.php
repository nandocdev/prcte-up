{{-- Sección: Introducción --}}
<div id="introduction">
    <h2 class="text-primary mb-4">
        <i class="fas fa-home"></i> ¿Qué es VIEX?
    </h2>
    
    <div class="doc-alert info">
        <i class="fas fa-info-circle"></i>
        <strong>Sistema Integral:</strong> VIEX es la plataforma oficial de la Universidad de Panamá para gestionar digitalmente todos los trabajos de extensión universitaria.
    </div>

    <section id="what-is-viex" class="mb-5">
        <h3><i class="fas fa-question-circle text-info"></i> ¿Qué es VIEX?</h3>
        <p class="lead">
            VIEX (Vicerrectoría de Extensión) es el sistema oficial de la Universidad de Panamá para la 
            <strong>gestión digital de trabajos de extensión universitaria</strong>. La plataforma digitaliza 
            completamente el proceso establecido en el "Manual de Procedimientos Para Presentar Trabajos de Extensión", 
            eliminando formularios físicos y agilizando los trámites.
        </p>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card card-outline card-success h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-digital-tachograph text-success"></i> 
                            Digitalización Completa
                        </h5>
                        <p class="card-text">
                            Elimina formularios físicos, firmas manuales y documentos en papel. 
                            Todo el proceso es 100% digital y trazable.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-outline card-info h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-clock text-info"></i> 
                            Disponibilidad 24/7
                        </h5>
                        <p class="card-text">
                            Accede al sistema desde cualquier lugar con internet, 
                            las 24 horas del día, los 7 días de la semana.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="benefits" class="mb-5">
        <h3><i class="fas fa-thumbs-up text-success"></i> Beneficios del Sistema</h3>
        
        <div class="row">
            <div class="col-lg-6">
                <h5>Para Profesores:</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0 px-0">
                        <i class="fas fa-check text-success mr-2"></i>
                        Registro rápido y sencillo de trabajos
                    </li>
                    <li class="list-group-item border-0 px-0">
                        <i class="fas fa-check text-success mr-2"></i>
                        Seguimiento en tiempo real del estado
                    </li>
                    <li class="list-group-item border-0 px-0">
                        <i class="fas fa-check text-success mr-2"></i>
                        Notificaciones automáticas por email
                    </li>
                    <li class="list-group-item border-0 px-0">
                        <i class="fas fa-check text-success mr-2"></i>
                        Archivo digital de documentos
                    </li>
                </ul>
            </div>
            <div class="col-lg-6">
                <h5>Para Coordinadores y Administradores:</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item border-0 px-0">
                        <i class="fas fa-check text-success mr-2"></i>
                        Panel de control centralizado
                    </li>
                    <li class="list-group-item border-0 px-0">
                        <i class="fas fa-check text-success mr-2"></i>
                        Reportes y estadísticas automáticas
                    </li>
                    <li class="list-group-item border-0 px-0">
                        <i class="fas fa-check text-success mr-2"></i>
                        Flujo de trabajo optimizado
                    </li>
                    <li class="list-group-item border-0 px-0">
                        <i class="fas fa-check text-success mr-2"></i>
                        Trazabilidad completa del proceso
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <section id="requirements" class="mb-5">
        <h3><i class="fas fa-laptop text-warning"></i> Requisitos del Sistema</h3>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-desktop"></i> Requisitos Técnicos
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Navegador:</strong> Chrome, Firefox, Safari, Edge (versión reciente)</li>
                            <li><strong>Internet:</strong> Conexión estable recomendada</li>
                            <li><strong>JavaScript:</strong> Debe estar habilitado</li>
                            <li><strong>Resolución:</strong> Mínimo 1024x768</li>
                            <li><strong>Cookies:</strong> Habilitadas para el sitio</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-outline card-info">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-check"></i> Cuentas Requeridas
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>Usuario:</strong> Cuenta institucional activa</li>
                            <li><strong>Email:</strong> Correo electrónico institucional (@up.ac.pa)</li>
                            <li><strong>Código:</strong> Código de profesor (para docentes)</li>
                            <li><strong>Unidad:</strong> Asignación a unidad organizacional</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="doc-alert success">
        <h6><i class="fas fa-rocket"></i> ¿Listo para empezar?</h6>
        <p class="mb-2">
            Si cumples con los requisitos, puedes comenzar inmediatamente:
        </p>
        <div class="mt-3">
            <a href="{{ route('documentation.show', 'getting-started') }}" class="btn btn-success mr-2">
                <i class="fas fa-play-circle"></i> Primeros Pasos
            </a>
            @if(auth()->user()->hasRole('profesor'))
                <a href="{{ route('works.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> Crear Mi Primer Trabajo
                </a>
            @endif
        </div>
    </div>
</div>