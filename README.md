
---

# VIEX · Sistema de Registro y Certificación de Trabajos de Extensión

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11+-FF2D20?style=for-the-badge\&logo=laravel\&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge\&logo=php\&logoColor=white)
![Oracle](https://img.shields.io/badge/Oracle-12c+-F80000?style=for-the-badge\&logo=oracle\&logoColor=white)
![AdminLTE](https://img.shields.io/badge/AdminLTE-3.x-007BFF?style=for-the-badge\&logo=bootstrap\&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

**Plataforma oficial de la Universidad de Panamá para la gestión digital de trabajos de extensión universitaria.**

</div>

---

## 📘 Índice

- [VIEX · Sistema de Registro y Certificación de Trabajos de Extensión](#viex--sistema-de-registro-y-certificación-de-trabajos-de-extensión)
  - [📘 Índice](#-índice)
  - [🎯 Panorama general](#-panorama-general)
    - [Objetivos clave](#objetivos-clave)
  - [⚙️ Alcance funcional](#️-alcance-funcional)
  - [🧭 Roles y visibilidad](#-roles-y-visibilidad)
  - [🛠️ Sistema de Administración](#️-sistema-de-administración)
    - [📊 Dashboard Administrativo](#-dashboard-administrativo)
    - [🗂️ Gestión de Catálogos](#️-gestión-de-catálogos)
    - [⚡ Características Principales](#-características-principales)
    - [🎨 Experiencia de Usuario](#-experiencia-de-usuario)
  - [🏗️ Arquitectura del sistema](#️-arquitectura-del-sistema)
  - [🧩 Stack tecnológico](#-stack-tecnológico)
  - [⚙️ Instalación y configuración](#️-instalación-y-configuración)
    - [Prerrequisitos](#prerrequisitos)
    - [Pasos básicos](#pasos-básicos)
    - [Configuración rápida (.env)](#configuración-rápida-env)
    - [Migraciones y seeders](#migraciones-y-seeders)
  - [🧪 Pruebas y aseguramiento de calidad](#-pruebas-y-aseguramiento-de-calidad)
  - [🧭 Convenciones del proyecto](#-convenciones-del-proyecto)
  - [📚 Documentación complementaria](#-documentación-complementaria)
  - [🧰 Soporte y contacto](#-soporte-y-contacto)
  - [🚀 Despliegue y mantenimiento](#-despliegue-y-mantenimiento)
    - [🔧 Entorno de Staging](#-entorno-de-staging)
    - [🌐 Entorno de Producción](#-entorno-de-producción)
    - [🩺 Monitoreo continuo](#-monitoreo-continuo)

---

## 🎯 Panorama general

**VIEX** digitaliza el proceso completo de registro, revisión, aprobación y certificación de los trabajos de extensión universitaria, de acuerdo con el *Manual de Procedimientos de la Universidad de Panamá*.

### Objetivos clave

* Reemplazar formularios físicos y firmas manuales por expedientes electrónicos auditables.
* Coordinar revisiones entre profesores, coordinaciones, decanatos y VIEX.
* Proveer trazabilidad, estadísticas e informes institucionales en tiempo real.

---

## ⚙️ Alcance funcional

| Módulo                      | Descripción                                                                                |
| --------------------------- | ------------------------------------------------------------------------------------------ |
| **Registro de trabajos**    | Formularios específicos para proyectos, actividades, publicaciones y asistencias técnicas. |
| **Gestión documental**      | Carga de evidencias, resoluciones y certificados mediante `MediaLibrary`.                  |
| **Flujo de aprobación**     | Secuencia: *Borrador → Coordinador → Decano/Director → VIEX → Certificado / Rechazado*.    |
| **Tableros por rol**        | KPIs, acciones rápidas y vistas personalizadas según el tipo de usuario.                   |
| **Reportes y estadísticas** | Filtros por unidad organizacional, estado y período académico.                             |
| **Bitácora y auditoría**    | Historial completo de cambios, observaciones y responsables.                               |

---

## 🧭 Roles y visibilidad

| Rol                    | Alcance organizacional     | Acciones principales                         |
| ---------------------- | -------------------------- | -------------------------------------------- |
| **Profesor**           | Solo trabajos propios      | Crear, editar, reenviar, adjuntar evidencias |
| **Coordinador**        | Su unidad + unidades hijas | Revisar, aprobar o devolver trabajos         |
| **Decano/Director**    | Facultad o centro completo | Aprobar o devolver trabajos                  |
| **Administrador VIEX** | Toda la institución        | Evaluar, certificar, emitir reportes         |
| **Super Admin**        | Acceso total               | Gobernanza de usuarios y catálogos           |

---

## 🛠️ Sistema de Administración

VIEX incluye un **sistema de administración completo** para la gestión centralizada de catálogos y configuración del sistema.

### 📊 Dashboard Administrativo
- **Estadísticas en tiempo real** de todos los módulos del sistema
- **Distribución visual** de trabajos por estado con gráficos interactivos
- **Métricas de actividad** de usuarios y trabajos (últimos 7 días)
- **Accesos rápidos** a todas las funcionalidades administrativas

### 🗂️ Gestión de Catálogos
El sistema permite administrar todos los catálogos de forma unificada:

| Catálogo | Descripción | Funcionalidades |
|----------|-------------|-----------------|
| **Tipos de Trabajo** | Clasificación de trabajos de extensión | CRUD completo, activación, conteo de trabajos |
| **Estados de Trabajo** | Flujo de aprobación con colores | Reordenamiento visual, estados finales, timeline |
| **Unidades Organizacionales** | Estructura universitaria jerárquica | Gestión padre-hijo, códigos únicos, tipos |
| **Tipos de Proyectos** | Clasificación de proyectos institucionales | CRUD, validaciones, conteo de proyectos |

### ⚡ Características Principales
- **Interfaz Unificada**: Todos los catálogos gestionados desde `/admin/catalogs`
- **Operaciones AJAX**: Sin recarga de página para mejor experiencia
- **Búsqueda en Tiempo Real**: Filtrado dinámico de todos los elementos
- **Operaciones Masivas**: Activar, desactivar, eliminar elementos en lote
- **Exportación de Datos**: Descarga de catálogos en Excel/CSV
- **Validaciones Robustas**: Form Requests para integridad de datos
- **Cache Inteligente**: Optimización automática de consultas frecuentes

### 🎨 Experiencia de Usuario
- **Diseño Moderno**: AdminLTE 3 con animaciones CSS y efectos interactivos
- **Drag & Drop**: Reordenamiento visual de estados de trabajo
- **Colores Dinámicos**: Estados con colores personalizables desde base de datos
- **Responsive Design**: Optimizado para escritorio, tablet y móvil
- **ODS Visuales**: Formularios de trabajos muestran iconografía propia (`public/assets/img/icons/ods`) para cada Objetivo de Desarrollo Sostenible seleccionado

**Acceso**: Usuarios con rol `super_admin` o `viex_admin` → `/admin/catalogs`

---

## 🏗️ Arquitectura del sistema

```
┌───────────────────────────────────────────────────────────────┐
│ Frontend (Vite + AdminLTE + Blade)                            │
│  - Formularios dinámicos y dashboards por rol                 │
│  - Componentes reutilizables y gráficos interactivos          │
└───────────────▲───────────────────────────────────────────────┘
                     │ HTTP/JSON (Laravel routes + controllers)
┌───────────────┴───────────────────────────────────────────────┐
│ Backend (Laravel 11+)                                         │
│  - Controladores delgados + Policies                          │
│  - Modelos con lógica de negocio y eventos                    │
│  - Jobs/Listeners para tareas diferidas                       │
│  - Spatie Permission & MediaLibrary                           │
└───────────────▲───────────────────────────────────────────────┘
                     │ Eloquent + yajra/laravel-oci8
┌───────────────┴───────────────────────────────────────────────┐
│ Bases de datos                                                │
│  - Oracle (producción)                                        │
│  - SQLite (testing/desarrollo)                                │
└───────────────────────────────────────────────────────────────┘
```

---

## 🧩 Stack tecnológico

| Categoría         | Herramientas                                       |
| ----------------- | -------------------------------------------------- |
| Backend           | Laravel 11+, PHP 8.2, Eloquent, yajra/laravel-oci8 |
| Frontend          | Vite, AdminLTE 3, Blade, jQuery, Select2           |
| Autenticación     | Laravel Breeze (personalizado)                     |
| Permisos          | Spatie/laravel-permission                          |
| Archivos          | Spatie/laravel-medialibrary                        |
| Testing           | PHPUnit, Laravel Testbench                         |
| Calidad de código | Laravel Pint, PHPStan nivel 6                      |

---

## ⚙️ Instalación y configuración

### Prerrequisitos

* PHP 8.2+ con extensiones `oci8`, `mbstring`, `xml`, `curl`, `zip`, `gd`
* Composer 2.x
* Node.js 18.x / npm 9.x
* Oracle Instant Client o SQLite

### Pasos básicos

```bash
git clone git@gitlab.com:viex/extension-platform.git
cd extension-platform
composer install
npm ci
cp .env.example .env
php artisan key:generate
```

### Configuración rápida (.env)

```dotenv
APP_NAME="VIEX"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://viex.local

DB_CONNECTION=sqlite
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
```

Para entornos Oracle, configurar las variables `DB_HOST`, `DB_SERVICE_NAME`, `DB_USERNAME`, `DB_PASSWORD`.

### Migraciones y seeders

```bash
php artisan migrate --seed
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"
```

---

## 🧪 Pruebas y aseguramiento de calidad

| Tipo              | Comando                        |
| ----------------- | ------------------------------ |
| Suite completa    | `php artisan test`             |
| Cobertura         | `php artisan test --coverage`  |
| Linter            | `./vendor/bin/pint`            |
| Análisis estático | `./vendor/bin/phpstan analyse` |

> Ejecutar pruebas antes de cada *merge* hacia `develop` o `main`.

---

## 🧭 Convenciones del proyecto

* **Controladores delgados** → validación en *Form Requests*, lógica en *Models* o *Services*.
* **Eventos y listeners** para acciones costosas o asíncronas.
* **Policies** obligatorias para control de acceso.
* **Internacionalización** en `resources/lang/es/*.php`.
* **Commits convencionales** (`feat`, `fix`, `docs`, `refactor`, `test`, etc.).
* **Ramas**:

  * `develop`: rama de integración
  * `feature/*`: nuevas funcionalidades
  * `fix/*`: correcciones
  * `hotfix/*`: parches críticos

---

## 📚 Documentación complementaria

| Archivo                                      | Contenido                             |
| -------------------------------------------- | ------------------------------------- |
| `doc/tecnica/Manual_Procedimientos.md`       | Flujo normativo aprobado por VIEX     |
| `doc/tecnica/Documento_Tecnico_Funcional.md` | Requerimientos funcionales y técnicos |
| `doc/tecnica/Sistema_Administracion_VIEX.md` | Documentación técnica del sistema de administración |
| `doc/usuario/Manual_Administrador_VIEX.md`   | Manual del usuario administrador     |
| `doc/sega/DIST.md`                           | Alcance y jerarquía de roles/unidades |
| `doc/usuario/`                               | Guías operativas por rol              |

---

## 🧰 Soporte y contacto

* **Soporte TI VIEX**: [soporte.viex@up.ac.pa](mailto:soporte.viex@up.ac.pa)
* **Mesa de ayuda**: ext. 2450 (horario laboral)
* **Incidencias**: Service Desk UP → categoría *Plataforma VIEX*

---

## 🚀 Despliegue y mantenimiento

### 🔧 Entorno de Staging

1. **Sincronizar rama de desarrollo**

   ```bash
   git pull origin develop
   ```
2. **Instalar dependencias**

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   ```
3. **Actualizar base de datos**

   ```bash
   php artisan migrate --seed
   ```
4. **Optimizar configuración**

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. **Ejecutar colas y scheduler**

   ```bash
   php artisan queue:work --tries=3 &
   php artisan schedule:run
   ```

---

### 🌐 Entorno de Producción

**Paso 1 — Preparar el servidor**

* PHP 8.2+, Nginx o Apache 2.4
* Extensiones requeridas (`oci8`, `pdo`, `gd`, etc.)
* Configurar permisos de escritura:

  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data .
  ```

**Paso 2 — Despliegue automatizado (recomendado)**
Usar `Git pull` o herramientas CI/CD:

```bash
git fetch origin main
git checkout main
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
```

**Paso 3 — Optimización post-deploy**

```bash
php artisan optimize
php artisan queue:restart
```

**Paso 4 — Backups**

* Base de datos Oracle: `expdp viex_app/viex_app@VIEXEXT ...`
* Archivos: sincronizar `storage/app/public/` a S3 o NAS institucional

---

### 🩺 Monitoreo continuo

* **Logs del sistema**: `storage/logs/laravel.log`
* **Colas fallidas**: `php artisan queue:failed`
* **Jobs pendientes**: `php artisan queue:listen`
* **Estado del sistema**: `/health` endpoint interno para supervisión

---

<div align="center">

**VIEX · Vicerrectoría de Extensión · Universidad de Panamá**
🏛️ [www.up.ac.pa](https://www.up.ac.pa) | 📧 [extension@up.ac.pa](mailto:extension@up.ac.pa)

*Digitalizando la extensión universitaria.*

</div>

