{{-- Sección: Administración del Sistema --}}
<div class="admin-documentation">
    {{-- Dashboard Administrativo --}}
    <div id="admin-dashboard" class="mb-5">
        <h3><i class="fas fa-tachometer-alt text-primary"></i> Dashboard Administrativo</h3>
        <p class="lead">El dashboard administrativo proporciona una vista integral del estado del sistema y acceso rápido a todas las funciones de administración.</p>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Características del Dashboard</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-pie"></i> Estadísticas en Tiempo Real</h6>
                        <ul>
                            <li>Contadores de todos los catálogos del sistema</li>
                            <li>Distribución de trabajos por estado</li>
                            <li>Métricas de actividad del sistema</li>
                            <li>Gráficos interactivos de tendencias</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-bolt"></i> Accesos Rápidos</h6>
                        <ul>
                            <li>Enlaces directos a gestión de catálogos</li>
                            <li>Acceso a reportes del sistema</li>
                            <li>Herramientas de mantenimiento</li>
                            <li>Configuración de usuarios y roles</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i>
            <strong>Acceso:</strong> Disponible para usuarios con rol <code>super_admin</code> o <code>viex_admin</code> en la ruta <code>/admin/dashboard</code>
        </div>
    </div>

    {{-- Gestión de Catálogos --}}
    <div id="catalog-management" class="mb-5">
        <h3><i class="fas fa-database text-success"></i> Gestión de Catálogos</h3>
        <p class="lead">Sistema unificado para administrar todos los catálogos del sistema VIEX desde una sola interfaz.</p>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Catálogos Disponibles</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tipos de Trabajos
                                <span class="badge badge-primary badge-pill">4 tipos</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Estados de Trabajo
                                <span class="badge badge-info badge-pill">7 estados</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Unidades Organizacionales
                                <span class="badge badge-warning badge-pill">Jerárquico</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tipos de Proyectos Institucionales
                                <span class="badge badge-secondary badge-pill">3 tipos</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-tools"></i> Operaciones Disponibles</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li><strong>CRUD Completo:</strong> Crear, leer, actualizar y eliminar registros</li>
                            <li><strong>Ordenamiento:</strong> Drag & drop para reordenar elementos</li>
                            <li><strong>Operaciones Masivas:</strong> Activar/desactivar múltiples elementos</li>
                            <li><strong>Exportación:</strong> Descarga de datos en formato Excel</li>
                            <li><strong>Filtros Avanzados:</strong> Búsqueda y filtrado por múltiples criterios</li>
                            <li><strong>Validación:</strong> Reglas de negocio y validación de integridad</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tipos de Trabajos --}}
    <div id="work-types-management" class="mb-5">
        <h3><i class="fas fa-briefcase text-warning"></i> Gestión de Tipos de Trabajos</h3>
        <p>Administra los cuatro tipos principales de trabajos de extensión universitaria.</p>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Tabla de Detalles</th>
                                <th>Campos Específicos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Proyectos</strong></td>
                                <td>Proyectos institucionales, de unidades académicas y servicio social</td>
                                <td><code>project_details</code></td>
                                <td>Tipo de proyecto, duración, beneficiarios</td>
                            </tr>
                            <tr>
                                <td><strong>Actividades</strong></td>
                                <td>Educación continua, capacitaciones, intervenciones</td>
                                <td><code>activity_details</code></td>
                                <td>Modalidad, duración, participantes</td>
                            </tr>
                            <tr>
                                <td><strong>Publicaciones</strong></td>
                                <td>Artículos, libros, material que genera conocimiento</td>
                                <td><code>publication_details</code></td>
                                <td>Tipo de publicación, editorial, ISBN/ISSN</td>
                            </tr>
                            <tr>
                                <td><strong>Asistencias Técnicas</strong></td>
                                <td>Asesorías, consultorías, servicios especializados</td>
                                <td><code>technical_assistance_details</code></td>
                                <td>Tipo de asistencia, modalidad, institución beneficiaria</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Importante:</strong> Los tipos de trabajo están predefinidos según el manual de procedimientos y no deben modificarse sin autorización de VIEX.
        </div>
    </div>

    {{-- Estados de Trabajo --}}
    <div id="work-statuses-management" class="mb-5">
        <h3><i class="fas fa-stream text-info"></i> Gestión de Estados de Trabajo</h3>
        <p>Administra el flujo de estados por los que pasan los trabajos de extensión durante su proceso de aprobación.</p>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-sitemap"></i> Flujo de Estados</h5>
                    </div>
                    <div class="card-body">
                        <div class="process-flow">
                            <div class="step">
                                <div class="step-icon bg-secondary">1</div>
                                <div class="step-content">
                                    <h6>Borrador</h6>
                                    <small>Trabajo en proceso de creación</small>
                                </div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step">
                                <div class="step-icon bg-primary">2</div>
                                <div class="step-content">
                                    <h6>En Coordinador</h6>
                                    <small>Revisión por coordinador de extensión</small>
                                </div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step">
                                <div class="step-icon bg-warning">3</div>
                                <div class="step-content">
                                    <h6>En Decano/Director</h6>
                                    <small>Aprobación institucional</small>
                                </div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step">
                                <div class="step-icon bg-info">4</div>
                                <div class="step-content">
                                    <h6>En VIEX</h6>
                                    <small>Evaluación final</small>
                                </div>
                            </div>
                            <div class="step-arrow">→</div>
                            <div class="step">
                                <div class="step-icon bg-success">5</div>
                                <div class="step-content">
                                    <h6>Certificado</h6>
                                    <small>Trabajo aprobado y certificado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-cog"></i> Configuración</h5>
                    </div>
                    <div class="card-body">
                        <h6>Campos Configurables:</h6>
                        <ul>
                            <li><strong>Color:</strong> Código hexadecimal para la interfaz</li>
                            <li><strong>Orden:</strong> Posición en el flujo</li>
                            <li><strong>Estado Final:</strong> Marca si es un estado terminal</li>
                            <li><strong>Descripción:</strong> Texto explicativo del estado</li>
                        </ul>
                        
                        <h6 class="mt-3">Funciones Especiales:</h6>
                        <ul>
                            <li>Reordenamiento con drag & drop</li>
                            <li>Transiciones automáticas</li>
                            <li>Historial de cambios</li>
                            <li>Notificaciones automáticas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Unidades Organizacionales --}}
    <div id="organizational-units-management" class="mb-5">
        <h3><i class="fas fa-sitemap text-danger"></i> Gestión de Unidades Organizacionales</h3>
        <p>Administra la estructura jerárquica de la universidad: facultades, departamentos, centros y otras unidades.</p>
        
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-tree"></i> Estructura Jerárquica</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-university"></i> Tipos de Unidades</h6>
                        <ul>
                            <li><strong>Universidad:</strong> Nivel superior</li>
                            <li><strong>Facultad:</strong> División académica principal</li>
                            <li><strong>Departamento:</strong> Subdivisión de facultades</li>
                            <li><strong>Escuela:</strong> Unidad académica especializada</li>
                            <li><strong>Centro:</strong> Unidad de investigación o extensión</li>
                            <li><strong>Instituto:</strong> Unidad especializada</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-users"></i> Gestión de Personal</h6>
                        <ul>
                            <li>Asignación de coordinadores</li>
                            <li>Vinculación de profesores</li>
                            <li>Definición de jerarquías</li>
                            <li>Control de permisos por unidad</li>
                            <li>Reportes por estructura</li>
                        </ul>
                    </div>
                </div>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Nota:</strong> La estructura organizacional debe reflejar la organización real de la universidad. 
                    Los cambios pueden afectar permisos y flujos de trabajo.
                </div>
            </div>
        </div>
    </div>

    {{-- Tipos de Proyectos Institucionales --}}
    <div id="institutional-project-types" class="mb-5">
        <h3><i class="fas fa-flag text-purple"></i> Tipos de Proyectos Institucionales</h3>
        <p>Gestiona las categorías específicas de proyectos institucionales disponibles en el sistema.</p>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-box bg-primary">
                            <span class="info-box-icon"><i class="fas fa-university"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Institucionales</span>
                                <span class="info-box-number">Nivel UP</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-building"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Unidades Académicas</span>
                                <span class="info-box-number">Facultades</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-hands-helping"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Servicio Social</span>
                                <span class="info-box-number">Estudiantil</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h6 class="mt-4"><i class="fas fa-edit"></i> Operaciones Disponibles</h6>
                <ul>
                    <li>Crear nuevos tipos de proyectos</li>
                    <li>Editar descripción y características</li>
                    <li>Activar/desactivar tipos</li>
                    <li>Establecer orden de presentación</li>
                    <li>Configurar validaciones específicas</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Operaciones Masivas --}}
    <div id="bulk-operations" class="mb-5">
        <h3><i class="fas fa-tasks text-success"></i> Operaciones Masivas</h3>
        <p>Realiza acciones en múltiples registros simultáneamente para optimizar la administración.</p>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-check-double"></i> Operaciones de Selección</h6>
                        <ul>
                            <li>Selección individual de registros</li>
                            <li>Selección masiva (todos los elementos)</li>
                            <li>Selección por criterios de filtro</li>
                            <li>Deselección rápida</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-bolt"></i> Acciones Disponibles</h6>
                        <ul>
                            <li>Activar/desactivar múltiples elementos</li>
                            <li>Cambio de estado masivo</li>
                            <li>Eliminación múltiple (con confirmación)</li>
                            <li>Exportación de seleccionados</li>
                        </ul>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Precaución:</strong> Las operaciones masivas son irreversibles. 
                    Siempre verifica la selección antes de ejecutar la acción.
                </div>
            </div>
        </div>
    </div>

    {{-- Exportación de Datos --}}
    <div id="data-export" class="mb-5">
        <h3><i class="fas fa-download text-info"></i> Exportación de Datos</h3>
        <p>Descarga información de los catálogos en formatos estándar para análisis externo.</p>
        
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-file-excel"></i> Formato Excel</h6>
                        <ul>
                            <li>Exportación completa de catálogos</li>
                            <li>Filtros aplicados incluidos</li>
                            <li>Formato de columnas optimizado</li>
                            <li>Encabezados descriptivos</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-filter"></i> Opciones de Filtrado</h6>
                        <ul>
                            <li>Exportar solo elementos activos</li>
                            <li>Incluir/excluir campos específicos</li>
                            <li>Aplicar filtros de fecha</li>
                            <li>Selección personalizada</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mantenimiento del Sistema --}}
    <div id="system-maintenance" class="mb-5">
        <h3><i class="fas fa-wrench text-dark"></i> Mantenimiento del Sistema</h3>
        <p>Herramientas para el mantenimiento y optimización del sistema de catálogos.</p>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-shield-alt"></i> Integridad de Datos</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Verificación de relaciones entre tablas</li>
                            <li>Detección de registros huérfanos</li>
                            <li>Validación de consistencia</li>
                            <li>Reparación automática de inconsistencias</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-database"></i> Optimización</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <li>Limpieza de registros obsoletos</li>
                            <li>Optimización de índices</li>
                            <li>Compactación de tablas</li>
                            <li>Análisis de rendimiento</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-danger mt-3">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Advertencia:</strong> Las operaciones de mantenimiento deben realizarse durante ventanas de mantenimiento programadas 
            y con respaldo completo del sistema.
        </div>
    </div>
</div>

<style>
.process-flow {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-width: 120px;
}

.step-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-bottom: 10px;
}

.step-content h6 {
    margin: 0;
    font-size: 0.9rem;
}

.step-content small {
    font-size: 0.75rem;
    color: #666;
}

.step-arrow {
    font-size: 1.5rem;
    color: #007bff;
    margin: 0 5px;
}

@media (max-width: 768px) {
    .process-flow {
        flex-direction: column;
    }
    
    .step-arrow {
        transform: rotate(90deg);
        margin: 5px 0;
    }
}

.admin-documentation h3 {
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.info-box {
    border-radius: 10px;
    margin-bottom: 20px;
}
</style>