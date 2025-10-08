## **Roadmap de Desarrollo del Proyecto VIEX**

Este roadmap describe el plan de desarrollo incremental para la plataforma. Cada fase representa un hito clave con un conjunto de funcionalidades coherentes. Los sprints son una estimación de 2 semanas de trabajo, pero pueden ajustarse.

### **Principios Guía del Roadmap**
- **Entrega de Valor Temprana:** Priorizamos tener el flujo principal funcionando lo antes posible.
- **Incremental y Iterativo:** Cada fase añade una capa funcional sobre la anterior.
- **Calidad Integrada:** Los tests y la seguridad no son una fase final, sino parte de cada sprint.
- **Feedback Continuo:** Al final de cada fase, se debe realizar una demo para validar la funcionalidad con los stakeholders.

---

## **Fase 0: Fundación y Configuración (Sprint 0)**
**Objetivo:** Establecer una base de proyecto sólida, segura y lista para el desarrollo.
- **Hitos:**
    1.  **Configuración del Proyecto:**
        -   Inicializar el repositorio de Git y aplicar la estrategia Git Flow.
        -   Configurar el proyecto Laravel 12+.
        -   Establecer la conexión con la base de datos (Oracle y SQLite para testing).
    2.  **Dependencias Clave:**
        -   Instalar y configurar `yajra/laravel-oci8`.
        -   Instalar y publicar los assets de `spatie/laravel-permission` y `spatie/laravel-medialibrary`.
    3.  **Autenticación y Roles Base:**
        -   Implementar el scaffolding de autenticación (login, logout, registro).
        -   Crear la migración de la tabla `users` con los campos personalizados (`professor_code`, etc.).
        -   Crear un `Seeder` para los roles principales (profesor, coordinador, decano, viex_admin, super_admin).
    4.  **Entorno de CI/CD Básico:**
        -   Configurar GitHub Actions para ejecutar tests (`php artisan test`) en cada push a `develop`.

**Resultado al Final de la Fase:** Un esqueleto de aplicación donde los usuarios pueden registrarse y ser asignados a un rol. La base para construir está lista.

---

## **Fase 1: MVP - Registro y Envío del Trabajo (Sprints 1-2)**
**Objetivo:** Permitir que un profesor complete el ciclo de creación y envío de un trabajo de extensión.
- **Hitos:**
    1.  **Modelado del Núcleo:**
        -   Crear migraciones y modelos para las tablas de catálogo (`WorkTypes`, `WorkStatuses`, `OrganizationalUnits`).
        -   Crear migración y modelo para la tabla principal `WorkOfExtension`.
        -   Definir las relaciones Eloquent básicas.
    2.  **Formulario de Creación (Tipo Simple):**
        -   Implementar el ciclo CRUD para el tipo de trabajo más simple (ej: "Actividad de Extensión").
        -   Crear el `WorkOfExtensionController` y `ActivityDetailController`.
        -   Desarrollar los `Form Requests` para la validación.
        -   Construir la vista Blade con el formulario.
    3.  **Lógica de "Borrador" y "Envío":**
        -   Implementar el método `saveAsDraft()` en el modelo `WorkOfExtension`.
        -   Implementar el método `submitForApproval()`, que cambiará el estado del trabajo y creará el primer registro en `work_status_history`.
    4.  **Dashboard Básico del Profesor:**
        -   Crear una vista donde el profesor pueda ver sus trabajos (borradores y enviados) y su estado actual.

**Resultado al Final de la Fase:** Un profesor puede iniciar sesión, crear una "Actividad de Extensión", guardarla como borrador, y enviarla oficialmente al flujo de aprobación.

---

## **Fase 2: El Flujo de Aprobación (Sprints 3-4)**
**Objetivo:** Construir la maquinaria de revisión y aprobación multinivel.
- **Hitos:**
    1.  **Dashboards de Revisores:**
        -   Crear un dashboard para el rol `coordinador_extension` que liste los trabajos pendientes de su unidad (`scopePendingForCoordinator`).
        -   Crear dashboards similares para los roles `decano_director` y `viex_admin`.
    2.  **Lógica de Aprobación/Rechazo:**
        -   Implementar los métodos de negocio en el modelo `WorkOfExtension`: `approveByCoordinator()`, `rejectWithComments()`, `approveByDean()`, etc.
        -   Asegurarse de que cada acción se registre correctamente en la tabla `work_status_history`.
        -   Implementar las `Policies` de autorización para restringir estas acciones al rol correcto.
    3.  **Ciclo de Subsanación:**
        -   Implementar el flujo para que un trabajo rechazado vuelva al dashboard del profesor con el estado "Requiere Subsanación" y los comentarios visibles.

**Resultado al Final de la Fase:** Un trabajo de extensión puede viajar por todo el flujo de aprobación, desde el coordinador hasta la VIEX, incluyendo rechazos y correcciones.

---

## **Fase 3: Funcionalidad Completa y Experiencia de Usuario (Sprints 5-6)**
**Objetivo:** Completar todos los tipos de trabajo, la gestión de archivos y la comunicación con el usuario.
- **Hitos:**
    1.  **Implementación de Todos los Tipos de Trabajo:**
        -   Crear los modelos, vistas y lógica para los tipos de trabajo restantes ("Proyecto", "Publicación", "Asistencia Técnica"), incluyendo sus campos `_json`.
    2.  **Gestión de Archivos:**
        -   Integrar `spatie/laravel-medialibrary` en los formularios para permitir la subida de evidencias.
        -   Implementar la visualización y descarga de archivos adjuntos.
    3.  **Notificaciones:**
        -   Crear `Events` (ej: `WorkStatusChanged`) y `Listeners` (ej: `SendStatusChangeNotification`).
        -   Configurar las notificaciones por correo electrónico para cada cambio de estado relevante.
    4.  **Certificación Final:**
        -   Implementar la lógica para que la VIEX emita el certificado final (generación de PDF con un código único).
        -   Permitir la descarga del certificado desde el dashboard del profesor.

**Resultado al Final de la Fase:** La plataforma es funcionalmente completa desde la perspectiva del usuario final. Todos los tipos de trabajo son soportados y la comunicación es automática.

---

## **Fase 4: Administración, Reportes y Despliegue (Sprints 7-8)**
**Objetivo:** Dotar a los administradores de herramientas de gestión y preparar la plataforma para producción.
- **Hitos:**
    1.  **Módulo de Reportes (RF08):**
        -   Crear una interfaz para generar reportes con filtros (por fecha, facultad, estado, tipo).
        -   Implementar la exportación a Excel/PDF.
        -   Crear el reporte de monitoreo de SLA (tiempos de tramitación).
    2.  **Paneles de Administración:**
        -   Crear un CRUD para la gestión de `Users`, `OrganizationalUnits` y otros catálogos.
        -   Asignar roles y permisos a los usuarios.
    3.  **Finalización y Hardening:**
        -   Realizar una revisión de seguridad completa (SQL Injection, XSS, CSRF, políticas de acceso).
        -   Optimizar consultas lentas y rendimiento general.
        -   Completar la cobertura de tests para las funcionalidades críticas.
    4.  **Preparación para Producción:**
        -   Configurar el entorno de producción (servidor, base de datos Oracle, variables de entorno).
        -   Documentar el proceso de despliegue.

**Resultado al Final de la Fase:** La plataforma está lista para ser desplegada, con herramientas robustas para la administración y la generación de inteligencia de negocio.

---

## **Fase 5: Post-Lanzamiento (Continuo)**
**Objetivo:** Dar soporte, recoger feedback y planificar futuras iteraciones.
- **Hitos:**
    -   Lanzamiento y Monitoreo.
    -   Soporte y corrección de bugs post-producción.
    -   Recopilación de feedback de los usuarios para la versión 2.0.
    -   Planificación de funcionalidades futuras (ej: importación de datos históricos - RF17).
