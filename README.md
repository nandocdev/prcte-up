# VIEX - Plataforma de Registro y Certificación de Trabajos de Extensión

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Oracle](https://img.shields.io/badge/Oracle-F80000?style=for-the-badge&logo=oracle&logoColor=white)
![AdminLTE](https://img.shields.io/badge/AdminLTE-3.x-007BFF?style=for-the-badge&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

*Sistema de gestión digital para trabajos de extensión universitarios de la Universidad de Panamá*

</div>

## 📋 Tabla de Contenidos

- [Descripción](#-descripción)
- [Características Principales](#-características-principales)
- [Arquitectura del Sistema](#-arquitectura-del-sistema)
- [Tecnologías](#-tecnologías)
- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Uso del Sistema](#-uso-del-sistema)
- [Roles y Permisos](#-roles-y-permisos)
- [API y Endpoints](#-api-y-endpoints)
- [Testing](#-testing)
- [Contribución](#-contribución)
- [Licencia](#-licencia)

## 🎯 Descripción

VIEX es una plataforma web desarrollada para la **Universidad de Panamá** que digitaliza completamente el proceso de registro, gestión y certificación de trabajos de extensión universitarios. El sistema implementa el flujo establecido en el **"Manual de Procedimientos Para Presentar Trabajos de Extensión"** y automatiza todo el proceso desde la solicitud inicial hasta la certificación final.

### 🎖️ Propósito

La plataforma reemplaza el proceso manual tradicional (formularios físicos, firmas manuales, trámites presenciales) con un sistema digital integral que mejora la eficiencia, transparencia y trazabilidad de todos los trabajos de extensión universitarios.

## ✨ Características Principales

### 📝 Gestión de Trabajos de Extensión
- **4 Tipos de Trabajos**: Proyectos, Actividades, Publicaciones y Asistencias Técnicas
- **Formularios Digitales**: Replicación fiel del formulario oficial con validaciones inteligentes
- **Carga de Evidencias**: Sistema polimórfico de archivos con Spatie MediaLibrary
- **Estados Dinámicos**: Flujo de aprobación con auditoría completa

### 🔄 Flujo de Aprobación Automatizado
```
Borrador → Coordinador Extensión → Decano/Director → VIEX → Certificado
```

### 👥 Sistema de Roles y Permisos (RBAC)
- **Profesores**: Registro y gestión de trabajos propios
- **Coordinadores de Extensión**: Revisión y validación por unidad académica
- **Decanos/Directores**: Aprobación y elevación a VIEX
- **Administradores VIEX**: Evaluación final y certificación
- **Super Administradores**: Gestión completa del sistema

### 📊 Dashboards Específicos por Rol
- **Métricas en Tiempo Real**: Estadísticas actualizadas automáticamente
- **Vistas Personalizadas**: Información relevante según el rol del usuario
- **Acciones Rápidas**: Botones de acción contextual según permisos

### 🔔 Sistema de Notificaciones
- **Notificaciones por Email**: Avisos automáticos en cada cambio de estado
- **Notificaciones en Tiempo Real**: Sistema interno de alertas
- **Recordatorios**: Alertas de plazos y pendientes

### 📈 Reportes y Análisis
- **Estadísticas por Unidad**: Métricas específicas por facultad/departamento
- **Reportes de Certificación**: Documentos oficiales generados automáticamente
- **Análisis de Tendencias**: Dashboards con información histórica

## 🏗️ Arquitectura del Sistema

### 🗂️ Modelo de Dominio

```
┌─────────────────────┬──────────────────────────────────────────┐
│ ENTIDAD PRINCIPAL   │ DESCRIPCIÓN                              │
├─────────────────────┼──────────────────────────────────────────┤
│ work_of_extensions  │ Tabla principal - Cada trabajo registrado │
│ users               │ Profesores con código y unidad académica  │
│ organizational_units│ Jerarquía universitaria (Facultades→Dept.)│
│ work_statuses       │ Estados del flujo + auditoría completa    │
│ project_details     │ Detalles específicos de proyectos         │
│ activity_details    │ Detalles específicos de actividades       │
│ publication_details │ Detalles específicos de publicaciones     │
│ technical_assistance│ Detalles específicos de asist. técnicas   │
└─────────────────────┴──────────────────────────────────────────┘
```

### 🎭 Patrones de Diseño Implementados

#### **Skinny Controller Pattern**
```php
// Controladores delgados - Solo orquestación
public function store(StoreWorkRequest $request)
{
    $work = WorkOfExtension::createFromRequest($request->validated(), $request->user());
    return redirect()->route('works.show', $work)->with('success', 'Trabajo creado exitosamente.');
}
```

#### **Fat Model Pattern**
```php
// Modelos con lógica de negocio
public function approveByCoordinator(User $approver, ?string $comments): void
{
    if ($this->statusIsNot(Status::PENDING_COORDINATOR)) {
        throw new InvalidStateException('El trabajo no está en el estado correcto.');
    }
    $this->changeStatus(Status::PENDING_DEAN, $approver, $comments);
    WorkApprovedByCoordinator::dispatch($this);
}
```

## 🛠️ Tecnologías

### **Backend**
- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Base de Datos**: Oracle Database (producción) + SQLite (desarrollo/testing)
- **ORM**: Eloquent con soporte Oracle (`yajra/laravel-oci8`)

### **Frontend**
- **Build Tool**: Vite 6.x
- **UI Framework**: AdminLTE 3.x
- **Templates**: Blade (Laravel)
- **CSS**: TailwindCSS + Bootstrap (AdminLTE)
- **JavaScript**: Alpine.js + jQuery

### **Autenticación y Permisos**
- **RBAC**: Spatie Laravel Permission
- **Autenticación**: Laravel Breeze (personalizado)
- **Middleware**: Protección por roles y permisos

### **Gestión de Archivos**
- **Librería**: Spatie Laravel MediaLibrary
- **Tipo**: Relaciones polimórficas
- **Soporte**: Múltiples tipos de archivo por trabajo

### **Herramientas de Desarrollo**
- **Testing**: PHPUnit
- **Code Quality**: Laravel Pint (PSR-12)
- **Debugging**: Laravel Telescope
- **Logging**: Monolog

## 📋 Requisitos del Sistema

### **Servidor**
- **PHP**: ≥ 8.2
- **Composer**: ≥ 2.0
- **Node.js**: ≥ 18.x
- **NPM**: ≥ 9.x

### **Base de Datos**
- **Oracle Database**: 12c+ (Producción)
- **SQLite**: 3.x (Desarrollo/Testing)

### **Extensiones PHP Requeridas**
```bash
php-oci8      # Para conexión Oracle
php-pdo       # Para abstracción de base de datos
php-mbstring  # Manejo de cadenas multibyte
php-xml       # Procesamiento XML
php-curl      # Peticiones HTTP
php-zip       # Compresión de archivos
php-gd        # Procesamiento de imágenes
```

## 🚀 Instalación

### **1. Clonar el Repositorio**
```bash
git clone https://github.com/tu-organizacion/extension-up.git
cd extension-up
```

### **2. Instalar Dependencias**
```bash
# Dependencias PHP
composer install

# Dependencias Node.js
npm install
```

### **3. Configuración del Entorno**
```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### **4. Configurar Base de Datos**
Editar `.env` con tus credenciales:

#### **Para Oracle (Producción)**
```env
DB_CONNECTION=oracle
DB_HOST=tu-servidor-oracle
DB_PORT=1521
DB_DATABASE=tu-base-datos
DB_USERNAME=tu-usuario
DB_PASSWORD=tu-contraseña
```

#### **Para SQLite (Desarrollo)**
```env
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/absoluta/database/database.sqlite
```

### **5. Ejecutar Migraciones y Seeders**
```bash
# Crear tablas y datos iniciales
php artisan migrate --seed

# Publicar assets de paquetes
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"
```

### **6. Compilar Assets Frontend**
```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### **7. Configurar Permisos de Archivos**
```bash
# Permisos de escritura
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## ⚙️ Configuración

### **Variables de Entorno Principales**

```env
# Aplicación
APP_NAME="VIEX - Plataforma de Extensión UP"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Base de Datos Oracle
DB_CONNECTION=oracle
DB_HOST=localhost
DB_PORT=1521
DB_DATABASE=EXTENSION_VIEX
DB_USERNAME=viex_user
DB_PASSWORD=tu_password

# Email (para notificaciones)
MAIL_MAILER=smtp
MAIL_HOST=smtp.up.ac.pa
MAIL_PORT=587
MAIL_USERNAME=viex@up.ac.pa
MAIL_PASSWORD=tu_password_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=viex@up.ac.pa
MAIL_FROM_NAME="VIEX - Universidad de Panamá"

# Configuración de Archivos
FILESYSTEM_DISK=local
MEDIA_DISK=public

# Configuración de Cola (para procesamiento asíncrono)
QUEUE_CONNECTION=database
```

### **Configuración Oracle Específica**

Para configuraciones avanzadas de Oracle, editar `config/database.php`:

```php
'oracle' => [
    'driver' => 'oracle',
    'host' => env('DB_HOST', 'localhost'),
    'port' => env('DB_PORT', '1521'),
    'database' => env('DB_DATABASE', ''),
    'service_name' => env('DB_SERVICE_NAME', ''),
    'username' => env('DB_USERNAME', ''),
    'password' => env('DB_PASSWORD', ''),
    'charset' => env('DB_CHARSET', 'AL32UTF8'),
    'prefix' => env('DB_PREFIX', ''),
    'prefix_schema' => env('DB_SCHEMA_PREFIX', ''),
    'edition' => env('DB_EDITION', 'ora$base'),
    'server_version' => env('DB_SERVER_VERSION', '11g'),
],
```

## 📖 Uso del Sistema

### **🔐 Credenciales de Acceso por Defecto**

El sistema incluye usuarios de prueba después de ejecutar los seeders:

| Rol | Email | Contraseña | Descripción |
|-----|-------|------------|-------------|
| Super Admin | admin@up.ac.pa | admin123 | Administrador del sistema |
| VIEX Admin | maria.vasquez@up.ac.pa | viex2025 | Administrador VIEX |
| Decano | ana.herrera@up.ac.pa | dean2025 | Decano de Ingeniería |
| Coordinador | luis.garcia@up.ac.pa | coord2025 | Coordinador de Extensión |
| Profesor | alejandra.morales@up.ac.pa | prof2025 | Profesor regular |

### **🎯 Flujo de Trabajo Principal**

#### **1. Registro de Trabajo (Profesor)**
```
1. Iniciar sesión como profesor
2. Ir a "Mis Trabajos" → "Crear Nuevo"
3. Seleccionar tipo de trabajo (Proyecto/Actividad/Publicación/Asistencia)
4. Completar formulario específico
5. Adjuntar evidencias requeridas
6. Enviar para revisión
```

#### **2. Revisión por Coordinador**
```
1. Recibir notificación de nuevo trabajo
2. Revisar formulario y evidencias
3. Opciones:
   - Aprobar y enviar a Decano
   - Solicitar correcciones
   - Rechazar con justificación
```

#### **3. Aprobación por Decano/Director**
```
1. Revisar trabajos aprobados por coordinador
2. Validar cumplimiento de requisitos
3. Enviar a VIEX para certificación final
```

#### **4. Certificación Final (VIEX)**
```
1. Asignar evaluadores de la comisión
2. Evaluar propuesta según criterios
3. Emitir dictamen y certificación
4. Notificar resultado a todas las partes
```

## 👥 Roles y Permisos

### **Matriz de Permisos**

| Acción | Profesor | Coordinador | Decano | VIEX Admin | Super Admin |
|--------|----------|-------------|--------|-------------|-------------|
| Crear trabajos | ✅ | ❌ | ❌ | ❌ | ✅ |
| Editar trabajos propios | ✅ | ❌ | ❌ | ❌ | ✅ |
| Revisar trabajos unidad | ❌ | ✅ | ❌ | ❌ | ✅ |
| Aprobar para VIEX | ❌ | ❌ | ✅ | ❌ | ✅ |
| Certificar trabajos | ❌ | ❌ | ❌ | ✅ | ✅ |
| Gestionar usuarios | ❌ | ❌ | ❌ | ❌ | ✅ |
| Configurar sistema | ❌ | ❌ | ❌ | ❌ | ✅ |

### **Permisos Específicos**

```php
// Permisos definidos en el sistema
'works.create'         // Crear trabajos de extensión
'works.edit'           // Editar trabajos propios
'works.coordinate'     // Coordinar trabajos de extensión
'works.manage.dean'    // Gestionar trabajos como Decano/Director
'works.manage.viex'    // Gestionar trabajos en VIEX
'users.manage'         // Gestionar usuarios del sistema
'roles.manage'         // Gestionar roles y permisos
```

## 🔌 API y Endpoints

### **Rutas Principales del Sistema**

#### **Autenticación**
```php
POST   /login              # Iniciar sesión
POST   /logout             # Cerrar sesión
GET    /dashboard          # Dashboard principal
```

#### **Gestión de Trabajos**
```php
GET    /works              # Listar trabajos del usuario
POST   /works              # Crear nuevo trabajo
GET    /works/{work}       # Ver detalles de trabajo
PUT    /works/{work}       # Actualizar trabajo
DELETE /works/{work}       # Eliminar trabajo (solo borradores)
```

#### **Flujo de Aprobación**
```php
POST   /coordinator/works/{work}/approve    # Aprobar por coordinador
POST   /coordinator/works/{work}/reject     # Rechazar por coordinador
POST   /dean/works/{work}/approve           # Aprobar por decano
POST   /dean/works/{work}/reject            # Rechazar por decano
POST   /viex/works/{work}/certify           # Certificar por VIEX
```

#### **Administración**
```php
GET    /admin/users                         # Gestión de usuarios
GET    /admin/roles                         # Gestión de roles
GET    /admin/reports                       # Reportes del sistema
```

## 🧪 Testing

### **Ejecutar Tests**
```bash
# Todos los tests
php artisan test

# Tests específicos
php artisan test --filter=WorkOfExtensionTest

# Con coverage
php artisan test --coverage
```

### **Tests Incluidos**
- **Unit Tests**: Modelos y lógica de negocio
- **Feature Tests**: Endpoints y flujos de trabajo
- **Integration Tests**: Interacciones entre componentes

### **Configuración de Testing**
```env
# .env.testing
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

## 🤝 Contribución

### **Estándares de Código**

El proyecto sigue estrictamente **PSR-12** y convenciones de Laravel:

```bash
# Verificar estilo de código
./vendor/bin/pint --test

# Corregir automáticamente
./vendor/bin/pint
```

### **Conventional Commits**

Todos los commits deben seguir el formato:
```
<tipo>[ámbito opcional]: <descripción>

[cuerpo opcional]

[footer opcional]
```

**Tipos permitidos:**
- `feat`: Nueva característica
- `fix`: Corrección de bug
- `docs`: Cambios en documentación
- `style`: Cambios de formato
- `refactor`: Refactoring de código
- `test`: Añadir o corregir tests
- `chore`: Tareas de mantenimiento

### **Flujo de Git**
```bash
# Crear rama de característica
git checkout -b feature/nueva-funcionalidad

# Hacer commits
git commit -m "feat(works): implementar validación automática de formularios"

# Push y Pull Request
git push origin feature/nueva-funcionalidad
```

## 📄 Licencia

Este proyecto está licenciado bajo la **Licencia MIT**. Ver el archivo [LICENSE](LICENSE) para más detalles.

```
MIT License

Copyright (c) 2025 Universidad de Panamá - Vicerrectoría de Extensión

Se permite el uso, copia, modificación y distribución de este software
bajo los términos de la Licencia MIT.
```

## 👨‍💻 Equipo de Desarrollo

- **Desarrollo Principal**: Equipo de Desarrollo VIEX
- **Arquitectura**: Basada en Laravel Framework
- **Diseño UX/UI**: AdminLTE 3.x
- **Consultoría Funcional**: Vicerrectoría de Extensión UP

## 📞 Soporte

Para soporte técnico o consultas sobre el sistema:

- **Email**: soporte.viex@up.ac.pa
- **Documentación**: [Manual de Usuario](doc/manual-usuario.md)
- **Issues**: [GitHub Issues](https://github.com/tu-organizacion/extension-up/issues)

---

<div align="center">

**VIEX - Universidad de Panamá**
*Digitalizando la extensión universitaria*

🏛️ [Universidad de Panamá](https://www.up.ac.pa) | 📧 [VIEX](mailto:extension@up.ac.pa) | 📱 [Portal UP](https://portal.up.ac.pa)

</div>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
