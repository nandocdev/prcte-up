{{-- Sección: Roles y Permisos --}}
<div id="roles-permissions">
    <h2 class="text-primary mb-4">
        <i class="fas fa-users-cog"></i> Roles y Permisos
    </h2>
    
    <div class="doc-alert info">
        <i class="fas fa-info-circle"></i>
        <strong>Sistema de Roles:</strong> VIEX utiliza un sistema de control de acceso basado en roles (RBAC) para garantizar que cada usuario vea únicamente la información relevante para sus funciones.
    </div>

    <section id="user-types" class="mb-5">
        <h3><i class="fas fa-users text-info"></i> Tipos de Usuarios</h3>
        
        <p>El sistema VIEX maneja cinco tipos principales de usuarios, cada uno con funciones específicas en el proceso de extensión universitaria:</p>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100 {{ auth()->user()->hasRole('profesor') ? 'border-success' : 'border-secondary' }}">
                    <div class="card-header {{ auth()->user()->hasRole('profesor') ? 'bg-success text-white' : 'bg-light' }}">
                        <h5 class="mb-0">
                            <i class="fas fa-chalkboard-teacher"></i> Profesor
                            @if(auth()->user()->hasRole('profesor'))
                                <span class="badge badge-light text-success ml-2">Tu Rol</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Función Principal:</strong> Crear y gestionar trabajos de extensión universitaria.
                        </p>
                        <h6>Responsabilidades:</h6>
                        <ul class="small">
                            <li>Crear nuevos trabajos de extensión</li>
                            <li>Completar información y documentación</li>
                            <li>Enviar trabajos para revisión</li>
                            <li>Responder a solicitudes de corrección</li>
                            <li>Gestionar evidencias y archivos</li>
                        </ul>
                        <h6>Alcance:</h6>
                        <p class="small text-muted">Ve únicamente sus propios trabajos de extensión.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100 {{ auth()->user()->hasRole('coordinador_extension') ? 'border-info' : 'border-secondary' }}">
                    <div class="card-header {{ auth()->user()->hasRole('coordinador_extension') ? 'bg-info text-white' : 'bg-light' }}">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tie"></i> Coordinador de Extensión
                            @if(auth()->user()->hasRole('coordinador_extension'))
                                <span class="badge badge-light text-info ml-2">Tu Rol</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Función Principal:</strong> Revisar y aprobar trabajos de su unidad organizacional.
                        </p>
                        <h6>Responsabilidades:</h6>
                        <ul class="small">
                            <li>Revisar trabajos enviados por profesores</li>
                            <li>Aprobar, solicitar correcciones o rechazar</li>
                            <li>Remitir trabajos aprobados al Decano</li>
                            <li>Generar reportes de su unidad</li>
                            <li>Supervisar profesores de su área</li>
                        </ul>
                        <h6>Alcance:</h6>
                        <p class="small text-muted">Ve trabajos de su unidad y unidades subordinadas.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100 {{ auth()->user()->hasRole('decano_director') ? 'border-warning' : 'border-secondary' }}">
                    <div class="card-header {{ auth()->user()->hasRole('decano_director') ? 'bg-warning text-white' : 'bg-light' }}">
                        <h5 class="mb-0">
                            <i class="fas fa-university"></i> Decano/Director
                            @if(auth()->user()->hasRole('decano_director'))
                                <span class="badge badge-light text-warning ml-2">Tu Rol</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Función Principal:</strong> Aprobar trabajos a nivel de facultad/centro.
                        </p>
                        <h6>Responsabilidades:</h6>
                        <ul class="small">
                            <li>Revisar trabajos aprobados por coordinadores</li>
                            <li>Aprobar y tramitar trabajos hacia VIEX</li>
                            <li>Devolver trabajos con observaciones</li>
                            <li>Generar reportes institucionales</li>
                            <li>Supervisar coordinadores de extensión</li>
                        </ul>
                        <h6>Alcance:</h6>
                        <p class="small text-muted">Ve trabajos de toda su facultad o centro.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100 {{ auth()->user()->hasRole('viex_admin') ? 'border-danger' : 'border-secondary' }}">
                    <div class="card-header {{ auth()->user()->hasRole('viex_admin') ? 'bg-danger text-white' : 'bg-light' }}">
                        <h5 class="mb-0">
                            <i class="fas fa-star"></i> Administrador VIEX
                            @if(auth()->user()->hasRole('viex_admin'))
                                <span class="badge badge-light text-danger ml-2">Tu Rol</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Función Principal:</strong> Evaluación final y certificación oficial.
                        </p>
                        <h6>Responsabilidades:</h6>
                        <ul class="small">
                            <li>Realizar evaluación final de trabajos</li>
                            <li>Asignar evaluadores especializados</li>
                            <li>Generar certificaciones oficiales</li>
                            <li>Producir reportes institucionales</li>
                            <li>Gestionar el sistema de evaluación</li>
                        </ul>
                        <h6>Alcance:</h6>
                        <p class="small text-muted">Ve todos los trabajos que llegan a VIEX para evaluación final.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100 {{ auth()->user()->hasRole('evaluador') ? 'border-secondary' : 'border-secondary' }}">
                    <div class="card-header {{ auth()->user()->hasRole('evaluador') ? 'bg-secondary text-white' : 'bg-light' }}">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check"></i> Evaluador
                            @if(auth()->user()->hasRole('evaluador'))
                                <span class="badge badge-light text-secondary ml-2">Tu Rol</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Función Principal:</strong> Evaluar trabajos asignados por VIEX.
                        </p>
                        <h6>Responsabilidades:</h6>
                        <ul class="small">
                            <li>Aceptar o declinar asignaciones</li>
                            <li>Evaluar trabajos según criterios establecidos</li>
                            <li>Proporcionar retroalimentación detallada</li>
                            <li>Emitir recomendaciones de aprobación</li>
                            <li>Mantener confidencialidad del proceso</li>
                        </ul>
                        <h6>Alcance:</h6>
                        <p class="small text-muted">Ve únicamente los trabajos asignados para evaluación.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100 {{ auth()->user()->hasRole('super_admin') ? 'border-dark' : 'border-secondary' }}">
                    <div class="card-header {{ auth()->user()->hasRole('super_admin') ? 'bg-dark text-white' : 'bg-light' }}">
                        <h5 class="mb-0">
                            <i class="fas fa-cog"></i> Super Administrador
                            @if(auth()->user()->hasRole('super_admin'))
                                <span class="badge badge-light text-dark ml-2">Tu Rol</span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                            <strong>Función Principal:</strong> Gestión completa del sistema.
                        </p>
                        <h6>Responsabilidades:</h6>
                        <ul class="small">
                            <li>Crear y gestionar usuarios</li>
                            <li>Asignar roles y permisos</li>
                            <li>Configurar parámetros del sistema</li>
                            <li>Gestionar unidades organizacionales</li>
                            <li>Monitorear funcionamiento del sistema</li>
                        </ul>
                        <h6>Alcance:</h6>
                        <p class="small text-muted">Acceso completo a todas las funciones del sistema.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="permissions" class="mb-5">
        <h3><i class="fas fa-key text-warning"></i> Permisos por Rol</h3>
        
        <p>La siguiente tabla muestra las funcionalidades específicas que cada rol puede realizar:</p>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Funcionalidad</th>
                        <th class="text-center">Profesor</th>
                        <th class="text-center">Coordinador</th>
                        <th class="text-center">Decano</th>
                        <th class="text-center">VIEX</th>
                        <th class="text-center">Evaluador</th>
                        <th class="text-center">Super Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Crear trabajos</strong></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td><strong>Editar propios trabajos</strong></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td><strong>Revisar trabajos de unidad</strong></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-minus text-muted"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td><strong>Aprobar trabajos</strong></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-minus text-muted"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td><strong>Certificar trabajos</strong></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td><strong>Evaluar trabajos asignados</strong></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td><strong>Gestionar usuarios</strong></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                    <tr>
                        <td><strong>Reportes institucionales</strong></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-minus text-warning"></i></td>
                        <td class="text-center"><i class="fas fa-minus text-warning"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                        <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                        <td class="text-center"><i class="fas fa-check text-success"></i></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <p class="small text-muted">
                <i class="fas fa-check text-success"></i> = Permitido &nbsp;&nbsp;
                <i class="fas fa-times text-danger"></i> = No permitido &nbsp;&nbsp;
                <i class="fas fa-minus text-warning"></i> = Limitado (solo su alcance)
            </p>
        </div>
    </section>

    <section id="context-switching" class="mb-5">
        <h3><i class="fas fa-exchange-alt text-primary"></i> Cambio de Contexto</h3>
        
        <div class="doc-alert info">
            <i class="fas fa-info-circle"></i>
            <strong>Múltiples Roles:</strong> Algunos usuarios pueden tener más de un rol asignado. El sistema permite cambiar entre roles fácilmente.
        </div>
        
        @if(auth()->user()->roles->count() > 1)
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user-check"></i> Tu Configuración de Roles
                    </h6>
                </div>
                <div class="card-body">
                    <p>Tienes asignados los siguientes roles:</p>
                    <div class="d-flex flex-wrap">
                        @foreach(auth()->user()->roles as $role)
                            <span class="badge badge-primary badge-lg mr-2 mb-2">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </span>
                        @endforeach
                    </div>
                    <p class="small text-muted mt-2">
                        Puedes cambiar entre roles usando el selector en la parte superior de la pantalla.
                    </p>
                </div>
            </div>
        @else
            <p>Para usuarios con múltiples roles asignados:</p>
        @endif
        
        <h5>¿Cómo cambiar de rol?</h5>
        <ol>
            <li><strong>Buscar el selector de rol</strong> en la parte superior de la pantalla</li>
            <li><strong>Hacer clic en el rol actual</strong> para ver opciones disponibles</li>
            <li><strong>Seleccionar el rol deseado</strong> de la lista desplegable</li>
            <li><strong>La interfaz se actualiza automáticamente</strong> según los permisos del nuevo rol</li>
        </ol>
        
        <div class="doc-alert warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Nota Importante:</strong> Al cambiar de rol, verás únicamente la información y opciones permitidas para ese rol específico.
        </div>
    </section>

    <div class="doc-alert success">
        <h6><i class="fas fa-user-shield"></i> Entender tu rol es fundamental</h6>
        <p class="mb-2">
            Con esta información ya conoces qué puedes hacer en el sistema según tu rol asignado.
        </p>
        <div class="mt-3">
            @if(auth()->user()->hasRole('profesor'))
                <a href="{{ route('documentation.show', 'professors') }}" class="btn btn-success mr-2">
                    <i class="fas fa-chalkboard-teacher"></i> Guía para Profesores
                </a>
            @elseif(auth()->user()->hasRole('coordinador_extension'))
                <a href="{{ route('documentation.show', 'coordinators') }}" class="btn btn-info mr-2">
                    <i class="fas fa-user-tie"></i> Guía para Coordinadores
                </a>
            @elseif(auth()->user()->hasRole('viex_admin'))
                <a href="{{ route('documentation.show', 'viex-admin') }}" class="btn btn-danger mr-2">
                    <i class="fas fa-star"></i> Guía para VIEX
                </a>
            @endif
            <a href="{{ route('documentation.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-list"></i> Ver Toda la Documentación
            </a>
        </div>
    </div>
</div>