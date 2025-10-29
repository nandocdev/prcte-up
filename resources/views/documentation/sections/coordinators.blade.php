{{-- Sección: Coordinadores de Extensión --}}
<div id="coordinators">
    <h2 class="text-primary mb-4">
        <i class="fas fa-user-tie"></i> Guía para Coordinadores de Extensión
    </h2>
    
    <div class="doc-alert info">
        <i class="fas fa-info-circle"></i>
        <strong>Rol del Coordinador:</strong> Como coordinador de extensión, eres responsable de revisar y aprobar los trabajos de extensión de tu unidad organizacional antes de enviarlos al decano/director.
    </div>

    <section id="coordinator-dashboard" class="mb-5">
        <h3><i class="fas fa-tachometer-alt text-info"></i> Panel de Control</h3>
        
        <p>Tu panel de control te proporciona una vista general de todos los trabajos pendientes de revisión en tu unidad:</p>
        
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar"></i> Métricas Principales
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <div class="border-right">
                                    <h4 class="text-warning mb-1">{{ $pendingCount ?? '5' }}</h4>
                                    <small class="text-muted">Pendientes de Revisión</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="border-right">
                                    <h4 class="text-success mb-1">{{ $approvedThisMonth ?? '12' }}</h4>
                                    <small class="text-muted">Aprobados este Mes</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="border-right">
                                    <h4 class="text-info mb-1">{{ $totalReviewed ?? '45' }}</h4>
                                    <small class="text-muted">Total Revisados</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <h4 class="text-primary mb-1">{{ $averageTime ?? '3.2' }} días</h4>
                                <small class="text-muted">Tiempo Promedio</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-clock"></i> Acciones Rápidas
                        </h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('coordinator.dashboard') }}" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-list"></i> Ver Trabajos Pendientes
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-info btn-block mb-2">
                            <i class="fas fa-chart-line"></i> Reportes de mi Unidad
                        </a>
                        <a href="{{ route('works.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-search"></i> Buscar Trabajos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="review-process" class="mb-5">
        <h3><i class="fas fa-tasks text-warning"></i> Proceso de Revisión</h3>
        
        <p>Como coordinador, tienes tres opciones principales al revisar un trabajo:</p>
        
        <div class="row">
            <div class="col-lg-4 mb-3">
                <div class="card border-success h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-check-circle"></i> Aprobar
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Cuándo usar:</strong> El trabajo cumple todos los criterios y está listo para la siguiente etapa.
                        </p>
                        <h6>Resultado:</h6>
                        <ul class="small">
                            <li>Se envía automáticamente al Decano/Director</li>
                            <li>El profesor recibe notificación de aprobación</li>
                            <li>Se registra en el historial del trabajo</li>
                        </ul>
                        <h6>Información requerida:</h6>
                        <ul class="small">
                            <li>Comentarios de aprobación (opcional)</li>
                            <li>Confirmación de revisión completa</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-3">
                <div class="card border-warning h-100">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-edit"></i> Solicitar Cambios
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Cuándo usar:</strong> El trabajo tiene potencial pero necesita correcciones o mejoras.
                        </p>
                        <h6>Resultado:</h6>
                        <ul class="small">
                            <li>Regresa al profesor para correcciones</li>
                            <li>Se envían observaciones detalladas</li>
                            <li>El profesor puede reenviar después de corregir</li>
                        </ul>
                        <h6>Información requerida:</h6>
                        <ul class="small">
                            <li><strong>Observaciones específicas</strong> (obligatorio)</li>
                            <li>Sugerencias de mejora</li>
                            <li>Referencias o recursos útiles</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-3">
                <div class="card border-danger h-100">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-times-circle"></i> Rechazar
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Cuándo usar:</strong> El trabajo no cumple los criterios mínimos y no procede para certificación.
                        </p>
                        <h6>Resultado:</h6>
                        <ul class="small">
                            <li>El trabajo se marca como rechazado definitivamente</li>
                            <li>Se envía justificación al profesor</li>
                            <li>No puede continuar el proceso de certificación</li>
                        </ul>
                        <h6>Información requerida:</h6>
                        <ul class="small">
                            <li><strong>Justificación detallada</strong> (obligatorio)</li>
                            <li>Criterios específicos no cumplidos</li>
                            <li>Recomendaciones para futuros trabajos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="review-criteria" class="mb-5">
        <h3><i class="fas fa-clipboard-check text-primary"></i> Criterios de Revisión</h3>
        
        <p>Al revisar un trabajo de extensión, verifica los siguientes aspectos según el tipo:</p>
        
        <div class="accordion" id="criteriaAccordion">
            <div class="card">
                <div class="card-header" id="headingProjects">
                    <h6 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseProjects">
                            <i class="fas fa-project-diagram"></i> Proyectos de Extensión
                        </button>
                    </h6>
                </div>
                <div id="collapseProjects" class="collapse show" data-parent="#criteriaAccordion">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Criterios Obligatorios:</h6>
                                <ul>
                                    <li>✅ Objetivos claramente definidos</li>
                                    <li>✅ Metodología apropiada</li>
                                    <li>✅ Beneficiarios identificados</li>
                                    <li>✅ Cronograma realista</li>
                                    <li>✅ Presupuesto detallado</li>
                                    <li>✅ Evidencias de actividades</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Documentos Requeridos:</h6>
                                <ul>
                                    <li>📄 Formulario de proyecto completo</li>
                                    <li>📄 Plan de trabajo detallado</li>
                                    <li>📄 Evidencias fotográficas</li>
                                    <li>📄 Listados de participación</li>
                                    <li>📄 Evaluaciones de impacto</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header" id="headingActivities">
                    <h6 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseActivities">
                            <i class="fas fa-calendar-alt"></i> Actividades de Extensión
                        </button>
                    </h6>
                </div>
                <div id="collapseActivities" class="collapse" data-parent="#criteriaAccordion">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Criterios Obligatorios:</h6>
                                <ul>
                                    <li>✅ Temática educativa clara</li>
                                    <li>✅ Número mínimo de participantes</li>
                                    <li>✅ Duración apropiada</li>
                                    <li>✅ Evaluación de satisfacción</li>
                                    <li>✅ Certificados de participación</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Documentos Requeridos:</h6>
                                <ul>
                                    <li>📄 Programa de la actividad</li>
                                    <li>📄 Lista de asistencia</li>
                                    <li>📄 Material didáctico usado</li>
                                    <li>📄 Evaluaciones de los participantes</li>
                                    <li>📄 Registro fotográfico</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header" id="headingPublications">
                    <h6 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapsePublications">
                            <i class="fas fa-book"></i> Publicaciones
                        </button>
                    </h6>
                </div>
                <div id="collapsePublications" class="collapse" data-parent="#criteriaAccordion">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Criterios Obligatorios:</h6>
                                <ul>
                                    <li>✅ Relevancia académica</li>
                                    <li>✅ Originalidad del contenido</li>
                                    <li>✅ Rigor metodológico</li>
                                    <li>✅ Impacto en la comunidad</li>
                                    <li>✅ Referencias apropiadas</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Documentos Requeridos:</h6>
                                <ul>
                                    <li>📄 Artículo o libro completo</li>
                                    <li>📄 Constancia de publicación</li>
                                    <li>📄 Datos de indexación (si aplica)</li>
                                    <li>📄 Métricas de impacto</li>
                                    <li>📄 Autorización de derechos</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header" id="headingTechnical">
                    <h6 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTechnical">
                            <i class="fas fa-tools"></i> Asistencias Técnicas
                        </button>
                    </h6>
                </div>
                <div id="collapseTechnical" class="collapse" data-parent="#criteriaAccordion">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Criterios Obligatorios:</h6>
                                <ul>
                                    <li>✅ Problema claramente definido</li>
                                    <li>✅ Solución técnica apropiada</li>
                                    <li>✅ Beneficiario identificado</li>
                                    <li>✅ Resultados medibles</li>
                                    <li>✅ Transferencia de conocimiento</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Documentos Requeridos:</h6>
                                <ul>
                                    <li>📄 Solicitud de asistencia</li>
                                    <li>📄 Diagnóstico técnico</li>
                                    <li>📄 Plan de intervención</li>
                                    <li>📄 Informe de resultados</li>
                                    <li>📄 Testimonios del beneficiario</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="step-by-step" class="mb-5">
        <h3><i class="fas fa-list-ol text-success"></i> Proceso Paso a Paso</h3>
        
        <div class="card card-outline card-success">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-route"></i> Cómo Revisar un Trabajo de Extensión
                </h6>
            </div>
            <div class="card-body">
                <ol class="larger-steps">
                    <li class="mb-3">
                        <strong>Acceder al Dashboard de Coordinador</strong>
                        <ul>
                            <li>Ve a "Coordinador" en el menú principal</li>
                            <li>Verás una lista de trabajos pendientes de tu unidad</li>
                        </ul>
                    </li>
                    
                    <li class="mb-3">
                        <strong>Seleccionar el Trabajo a Revisar</strong>
                        <ul>
                            <li>Haz clic en el título del trabajo o en "Ver Detalles"</li>
                            <li>Se abrirá la vista completa del trabajo</li>
                        </ul>
                    </li>
                    
                    <li class="mb-3">
                        <strong>Revisar la Información Completa</strong>
                        <ul>
                            <li>Lee toda la información del formulario</li>
                            <li>Descarga y revisa todos los archivos adjuntos</li>
                            <li>Verifica que cumple los criterios de tu área</li>
                        </ul>
                    </li>
                    
                    <li class="mb-3">
                        <strong>Tomar una Decisión</strong>
                        <ul>
                            <li>Usa los botones de acción en la parte inferior</li>
                            <li>Completa los comentarios requeridos</li>
                            <li>Confirma tu decisión</li>
                        </ul>
                    </li>
                    
                    <li class="mb-3">
                        <strong>Seguimiento</strong>
                        <ul>
                            <li>El sistema envía notificaciones automáticamente</li>
                            <li>Puedes hacer seguimiento desde tu dashboard</li>
                            <li>Generar reportes de tu unidad cuando sea necesario</li>
                        </ul>
                    </li>
                </ol>
            </div>
        </div>
    </section>

    <section id="tips-best-practices" class="mb-5">
        <h3><i class="fas fa-lightbulb text-warning"></i> Consejos y Mejores Prácticas</h3>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-thumbs-up"></i> Buenas Prácticas
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><strong>Revisa completamente</strong> antes de tomar una decisión</li>
                            <li><strong>Proporciona comentarios constructivos</strong> en solicitudes de cambios</li>
                            <li><strong>Sé específico</strong> en las observaciones y sugerencias</li>
                            <li><strong>Responde en tiempo razonable</strong> para no retrasar el proceso</li>
                            <li><strong>Mantén comunicación</strong> con los profesores cuando sea necesario</li>
                            <li><strong>Documenta</strong> criterios específicos de tu unidad</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card card-outline card-warning">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Aspectos a Evitar
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><strong>No rechaces sin justificación</strong> clara y detallada</li>
                            <li><strong>Evita comentarios vagos</strong> como "mejorar la calidad"</li>
                            <li><strong>No retornes trabajos</strong> por problemas menores fácilmente solucionables</li>
                            <li><strong>No apruebes sin verificar</strong> toda la documentación</li>
                            <li><strong>Evita delays innecesarios</strong> en tu revisión</li>
                            <li><strong>No uses criterios personales</strong> no establecidos institucionalmente</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="reporting" class="mb-5">
        <h3><i class="fas fa-chart-bar text-info"></i> Reportes y Estadísticas</h3>
        
        <p>Como coordinador, tienes acceso a varios reportes para gestionar eficientemente tu unidad:</p>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6><i class="fas fa-list"></i> Trabajos por Estado</h6>
                        <p class="small text-muted mb-3">
                            Resumen de todos los trabajos en diferentes etapas del proceso en tu unidad.
                        </p>
                        <a href="{{ route('admin.reports.index') }}?type=status&unit={{ auth()->user()->organizational_unit_id ?? 1 }}" class="btn btn-sm btn-info">
                            <i class="fas fa-chart-pie"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6><i class="fas fa-calendar"></i> Productividad Mensual</h6>
                        <p class="small text-muted mb-3">
                            Número de trabajos procesados cada mes y tiempos promedio de revisión.
                        </p>
                        <a href="{{ route('admin.reports.index') }}?type=monthly&unit={{ auth()->user()->organizational_unit_id ?? 1 }}" class="btn btn-sm btn-success">
                            <i class="fas fa-chart-line"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6><i class="fas fa-users"></i> Profesores Activos</h6>
                        <p class="small text-muted mb-3">
                            Lista de profesores de tu unidad con trabajos de extensión y su productividad.
                        </p>
                        <a href="{{ route('admin.reports.index') }}?type=professors&unit={{ auth()->user()->organizational_unit_id ?? 1 }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-chart-bar"></i> Ver Reporte
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6><i class="fas fa-file-export"></i> Exportar Datos</h6>
                        <p class="small text-muted mb-3">
                            Descarga datos en formato Excel para análisis adicional o reportes institucionales.
                        </p>
                        <a href="{{ route('admin.reports.index') }}?type=export&unit={{ auth()->user()->organizational_unit_id ?? 1 }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-download"></i> Exportar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="doc-alert success">
        <h6><i class="fas fa-user-tie"></i> Eres un Actor Clave en el Proceso</h6>
        <p class="mb-2">
            Tu rol como coordinador es fundamental para mantener la calidad y eficiencia del proceso de certificación de trabajos de extensión. Tu revisión minuciosa asegura que solo trabajos de alta calidad lleguen a las siguientes etapas.
        </p>
        <div class="mt-3">
            <a href="{{ route('coordinator.dashboard') }}" class="btn btn-success mr-2">
                <i class="fas fa-tachometer-alt"></i> Ir a mi Dashboard
            </a>
            <a href="{{ route('documentation.show', 'roles-permissions') }}" class="btn btn-outline-info mr-2">
                <i class="fas fa-users-cog"></i> Ver Roles y Permisos
            </a>
            <a href="{{ route('documentation.show', 'notifications') }}" class="btn btn-outline-primary">
                <i class="fas fa-bell"></i> Sistema de Notificaciones
            </a>
        </div>
    </div>
</div>