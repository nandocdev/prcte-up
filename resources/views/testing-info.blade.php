<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VIEX - Información de Testing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="mb-0"><i class="fas fa-university"></i> VIEX - Información de Testing</h1>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <h4 class="alert-heading">✅ Sistema Completamente Funcional</h4>
                            <p>El formulario de registro de trabajos de extensión ha sido completamente implementado y
                                está funcionando correctamente.</p>
                        </div>

                        <h3>📋 Estado del Sistema</h3>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="list-group">
                                    <div class="list-group-item list-group-item-success">
                                        <strong>✅ Formulario Wizard Avanzado</strong><br>
                                        <small>4 tipos de trabajo implementados con validación completa</small>
                                    </div>
                                    <div class="list-group-item list-group-item-success">
                                        <strong>✅ Base de Datos</strong><br>
                                        <small>Esquema completo con 7 tablas y relaciones</small>
                                    </div>
                                    <div class="list-group-item list-group-item-success">
                                        <strong>✅ Autenticación y Roles</strong><br>
                                        <small>Sistema RBAC con 5 roles implementados</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="list-group">
                                    <div class="list-group-item list-group-item-success">
                                        <strong>✅ Backend Completo</strong><br>
                                        <small>Controladores, modelos y servicios implementados</small>
                                    </div>
                                    <div class="list-group-item list-group-item-success">
                                        <strong>✅ Frontend AdminLTE</strong><br>
                                        <small>Interfaz profesional con validación JavaScript</small>
                                    </div>
                                    <div class="list-group-item list-group-item-success">
                                        <strong>✅ Manejo de Archivos</strong><br>
                                        <small>Sistema polimórfico de evidencias</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3>🔑 Credenciales de Acceso</h3>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <strong>Super Administrador</strong>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Email:</strong> admin@up.ac.pa<br>
                                            <strong>Password:</strong> admin123<br>
                                            <strong>Acceso:</strong> Total
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning">
                                        <strong>Admin VIEX</strong>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Email:</strong> maria.vasquez@up.ac.pa<br>
                                            <strong>Password:</strong> viex2025<br>
                                            <strong>Acceso:</strong> Certificación
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <strong>Coordinador</strong>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Email:</strong> carlos.mendoza@up.ac.pa<br>
                                            <strong>Password:</strong> viex2025<br>
                                            <strong>Acceso:</strong> Revisión
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h3>🎯 Tipos de Trabajo Implementados</h3>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>A. Proyectos de Extensión</h5>
                                <ul>
                                    <li>Proyectos Institucionales</li>
                                    <li>Proyectos de Unidades Académicas</li>
                                    <li>Proyectos de Servicio Social</li>
                                </ul>

                                <h5>B. Actividades de Extensión</h5>
                                <ul>
                                    <li>Educación Continua</li>
                                    <li>Intervenciones Puntuales</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>C. Publicaciones</h5>
                                <ul>
                                    <li>Artículos especializados</li>
                                    <li>Libros y audiolibros</li>
                                    <li>Material educativo</li>
                                </ul>

                                <h5>D. Asistencias Técnicas</h5>
                                <ul>
                                    <li>Asesorías especializadas</li>
                                    <li>Consultorías técnicas</li>
                                </ul>
                            </div>
                        </div>

                        <h3>🚀 Enlaces de Acceso</h3>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                            </a>
                            <a href="{{ url('/') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-home"></i> Página Principal
                            </a>
                        </div>

                        <div class="alert alert-info mt-4">
                            <h5 class="alert-heading">💡 Instrucciones de Prueba</h5>
                            <ol>
                                <li>Inicia sesión con cualquiera de las credenciales mostradas arriba</li>
                                <li>Ve a "Trabajos de Extensión" → "Crear Nuevo Trabajo"</li>
                                <li>Selecciona un tipo de trabajo y completa el formulario paso a paso</li>
                                <li>El sistema validará automáticamente los campos requeridos</li>
                                <li>Podrás adjuntar evidencias en formato PDF, DOC, JPG, PNG</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>

</html>
