{{-- Sección: Para Profesores --}}
<div id="professors">
    <h2 class="text-primary mb-4">
        <i class="fas fa-chalkboard-teacher"></i> Guía para Profesores
    </h2>
    
    <div class="doc-alert info">
        <i class="fas fa-info-circle"></i>
        <strong>Guía Completa:</strong> Esta sección cubre todo lo que necesitas saber para crear, gestionar y enviar trabajos de extensión.
    </div>

    <section id="dashboard" class="mb-5">
        <h3><i class="fas fa-tachometer-alt text-info"></i> Dashboard del Profesor</h3>
        
        <p>El dashboard del profesor es tu centro de control personal. Aquí encontrarás:</p>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card card-outline card-info h-100">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-pie"></i> Panel de Estadísticas
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>Total de trabajos registrados</li>
                            <li>Trabajos en borrador</li>
                            <li>Trabajos en revisión</li>
                            <li>Trabajos certificados</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card card-outline card-success h-100">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-clock"></i> Trabajos Recientes
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>Lista de los últimos 5 trabajos</li>
                            <li>Estado actual de cada trabajo</li>
                            <li>Acciones disponibles</li>
                            <li>Enlaces directos para editar</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card card-outline card-warning h-100">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-bell"></i> Notificaciones Pendientes
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>Mensajes sin leer</li>
                            <li>Solicitudes de corrección</li>
                            <li>Actualizaciones de estado</li>
                            <li>Recordatorios importantes</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card card-outline card-primary h-100">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-rocket"></i> Acciones Rápidas
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>Botón "Nuevo Trabajo"</li>
                            <li>Acceso a trabajos en borrador</li>
                            <li>Enlaces a documentación</li>
                            <li>Atajos de navegación</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-external-link-alt"></i> Ver Mi Dashboard
            </a>
        </div>
    </section>

    <section id="create-work" class="mb-5">
        <h3><i class="fas fa-plus-circle text-success"></i> Crear Nuevo Trabajo de Extensión</h3>
        
        <div class="doc-alert warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Preparación:</strong> Antes de crear un trabajo, asegúrate de tener toda la documentación necesaria lista.
        </div>
        
        <h5>Pasos para crear un nuevo trabajo:</h5>
        
        <div class="timeline">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                <span class="badge badge-primary">1</span>
                                Iniciar Creación
                            </h6>
                            <p class="card-text small">
                                Hacer clic en <code>"Nuevo Trabajo"</code> desde el dashboard o 
                                el menú principal.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                <span class="badge badge-success">2</span>
                                Seleccionar Tipo
                            </h6>
                            <p class="card-text small">
                                Elegir entre: Proyecto, Actividad, Publicación o 
                                Asistencia Técnica.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                <span class="badge badge-info">3</span>
                                Información Básica
                            </h6>
                            <p class="card-text small">
                                Completar título, descripción, fechas y período académico.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">
                                <span class="badge badge-warning">4</span>
                                Detalles Específicos
                            </h6>
                            <p class="card-text small">
                                Completar información específica según el tipo de trabajo.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <a href="{{ route('works.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Crear Nuevo Trabajo
            </a>
            <a href="{{ route('documentation.show', 'professors') }}#work-types" class="btn btn-outline-info ml-2">
                <i class="fas fa-question-circle"></i> Ver Tipos de Trabajos
            </a>
        </div>
    </section>

    <section id="work-types" class="mb-5">
        <h3><i class="fas fa-list-alt text-primary"></i> Tipos de Trabajos de Extensión</h3>
        
        <div class="accordion" id="workTypesAccordion">
            <!-- Proyectos de Extensión -->
            <div class="card">
                <div class="card-header" id="headingProjects">
                    <h6 class="mb-0">
                        <button class="btn btn-link text-left w-100" type="button" data-toggle="collapse" data-target="#collapseProjects">
                            <i class="fas fa-project-diagram text-primary"></i>
                            <strong>Proyectos de Extensión</strong>
                            <i class="fas fa-chevron-down float-right"></i>
                        </button>
                    </h6>
                </div>
                <div id="collapseProjects" class="collapse show" data-parent="#workTypesAccordion">
                    <div class="card-body">
                        <p class="text-muted small">Iniciativas planificadas de intervención social o académica con objetivos específicos.</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-info">Información Requerida:</h6>
                                <ul class="small">
                                    <li>Objetivos del proyecto</li>
                                    <li>Metodología a utilizar</li>
                                    <li>Beneficiarios directos e indirectos</li>
                                    <li>Área geográfica de impacto</li>
                                    <li>Cronograma de actividades</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-warning">Documentos Necesarios:</h6>
                                <ul class="small">
                                    <li>Propuesta del proyecto</li>
                                    <li>Cronograma detallado</li>
                                    <li>Presupuesto (si aplica)</li>
                                    <li>Cartas de apoyo institucional</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actividades de Extensión -->
            <div class="card">
                <div class="card-header" id="headingActivities">
                    <h6 class="mb-0">
                        <button class="btn btn-link text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#collapseActivities">
                            <i class="fas fa-calendar-alt text-success"></i>
                            <strong>Actividades de Extensión</strong>
                            <i class="fas fa-chevron-down float-right"></i>
                        </button>
                    </h6>
                </div>
                <div id="collapseActivities" class="collapse" data-parent="#workTypesAccordion">
                    <div class="card-body">
                        <p class="text-muted small">Eventos educativos dirigidos a la comunidad externa como cursos, talleres, seminarios.</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-info">Información Requerida:</h6>
                                <ul class="small">
                                    <li>Tipo de actividad (curso, taller, seminario, etc.)</li>
                                    <li>Modalidad (presencial, virtual, híbrida)</li>
                                    <li>Duración en horas</li>
                                    <li>Perfil de participantes</li>
                                    <li>Si otorga certificado de participación</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-warning">Documentos Necesarios:</h6>
                                <ul class="small">
                                    <li>Programa de la actividad</li>
                                    <li>Currículo de facilitadores</li>
                                    <li>Material promocional</li>
                                    <li>Lista de participantes (al finalizar)</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Publicaciones -->
            <div class="card">
                <div class="card-header" id="headingPublications">
                    <h6 class="mb-0">
                        <button class="btn btn-link text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#collapsePublications">
                            <i class="fas fa-book text-info"></i>
                            <strong>Publicaciones</strong>
                            <i class="fas fa-chevron-down float-right"></i>
                        </button>
                    </h6>
                </div>
                <div id="collapsePublications" class="collapse" data-parent="#workTypesAccordion">
                    <div class="card-body">
                        <p class="text-muted small">Material bibliográfico o audiovisual que genera conocimiento (artículos, libros, manuales).</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-info">Información Requerida:</h6>
                                <ul class="small">
                                    <li>Tipo de publicación (artículo, libro, manual, etc.)</li>
                                    <li>Editorial o revista</li>
                                    <li>ISBN/ISSN</li>
                                    <li>Audiencia objetivo</li>
                                    <li>Idioma de publicación</li>
                                    <li>Justificación de relevancia</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-warning">Documentos Necesarios:</h6>
                                <ul class="small">
                                    <li>Manuscrito o publicación final</li>
                                    <li>Carta de aceptación (revistas)</li>
                                    <li>Pruebas de impresión</li>
                                    <li>Reseñas o evaluaciones</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Asistencias Técnicas -->
            <div class="card">
                <div class="card-header" id="headingTechnical">
                    <h6 class="mb-0">
                        <button class="btn btn-link text-left w-100 collapsed" type="button" data-toggle="collapse" data-target="#collapseTechnical">
                            <i class="fas fa-tools text-warning"></i>
                            <strong>Asistencias Técnicas</strong>
                            <i class="fas fa-chevron-down float-right"></i>
                        </button>
                    </h6>
                </div>
                <div id="collapseTechnical" class="collapse" data-parent="#workTypesAccordion">
                    <div class="card-body">
                        <p class="text-muted small">Servicios especializados prestados a instituciones externas (consultorías, asesorías).</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-info">Información Requerida:</h6>
                                <ul class="small">
                                    <li>Tipo de asistencia (consultoría, asesoría, etc.)</li>
                                    <li>Institución colaboradora</li>
                                    <li>Área de especialización</li>
                                    <li>Productos esperados</li>
                                    <li>Modalidad de trabajo</li>
                                    <li>Horas estimadas</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-warning">Documentos Necesarios:</h6>
                                <ul class="small">
                                    <li>Carta de solicitud institucional</li>
                                    <li>Propuesta técnica</li>
                                    <li>Cronograma de trabajo</li>
                                    <li>Informes de avance</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="edit-draft" class="mb-5">
        <h3><i class="fas fa-edit text-info"></i> Editar Trabajo en Borrador</h3>
        
        <div class="doc-alert info">
            <i class="fas fa-info-circle"></i>
            <strong>Flexibilidad:</strong> Los trabajos en estado "Borrador" pueden editarse libremente hasta que los envíes para revisión.
        </div>
        
        <p>Para editar un trabajo en borrador:</p>
        
        <ol>
            <li><strong>Ir a "Mis Trabajos"</strong> desde el menú principal</li>
            <li><strong>Filtrar por "Borradores"</strong> usando el filtro de estado</li>
            <li><strong>Localizar el trabajo</strong> que deseas editar</li>
            <li><strong>Hacer clic en "Editar"</strong> en la columna de acciones</li>
            <li><strong>Modificar la información</strong> necesaria</li>
            <li><strong>Guardar cambios</strong> usando el botón correspondiente</li>
        </ol>
        
        <div class="doc-alert warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Importante:</strong> Una vez enviado para revisión, el trabajo no podrá editarse hasta que sea devuelto para corrección.
        </div>
    </section>

    <section id="submit-work" class="mb-5">
        <h3><i class="fas fa-paper-plane text-success"></i> Enviar Trabajo para Revisión</h3>
        
        <p>Cuando tu trabajo esté completo y listo, puedes enviarlo para revisión:</p>
        
        <div class="card card-outline card-success">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-check-circle"></i> Lista de Verificación Antes del Envío
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Información Completa:</h6>
                        <ul class="small">
                            <li><i class="fas fa-check text-success"></i> Título descriptivo y claro</li>
                            <li><i class="fas fa-check text-success"></i> Descripción detallada</li>
                            <li><i class="fas fa-check text-success"></i> Fechas de inicio y fin</li>
                            <li><i class="fas fa-check text-success"></i> Período académico</li>
                            <li><i class="fas fa-check text-success"></i> Detalles específicos del tipo</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Documentos Adjuntos:</h6>
                        <ul class="small">
                            <li><i class="fas fa-check text-success"></i> Propuesta principal</li>
                            <li><i class="fas fa-check text-success"></i> Documentos de respaldo</li>
                            <li><i class="fas fa-check text-success"></i> Evidencias (si corresponde)</li>
                            <li><i class="fas fa-check text-success"></i> Cronogramas y presupuestos</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <h5 class="mt-4">Proceso de Envío:</h5>
        <ol>
            <li><strong>Verificar completitud</strong> usando la lista de verificación</li>
            <li><strong>Revisar información</strong> en la vista previa</li>
            <li><strong>Hacer clic en "Enviar para Revisión"</strong></li>
            <li><strong>Confirmar el envío</strong> en el diálogo de confirmación</li>
        </ol>
        
        <div class="doc-alert success">
            <i class="fas fa-info-circle"></i>
            <strong>Validación Automática:</strong> El sistema verificará automáticamente que todos los campos obligatorios estén completos antes del envío.
        </div>
    </section>

    <div class="doc-alert success">
        <h6><i class="fas fa-graduation-cap"></i> ¿Listo para crear tu primer trabajo?</h6>
        <p class="mb-2">
            Con esta información ya tienes todo lo necesario para comenzar.
        </p>
        <div class="mt-3">
            <a href="{{ route('works.create') }}" class="btn btn-success mr-2">
                <i class="fas fa-plus-circle"></i> Crear Trabajo Ahora
            </a>
            <a href="{{ route('works.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list"></i> Ver Mis Trabajos
            </a>
        </div>
    </div>
</div>