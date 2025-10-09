# Auditoría CU17 - Gestionar Catálogos del Sistema

**Fecha:** 8 de octubre de 2025  
**Responsable:** Equipo VIEX  
**Resultado:** ✅ Caso de uso **implementado y verificado**

## Objetivo del Caso de Uso

Permitir a los roles **super_admin** y **viex_admin** administrar los catálogos base del sistema: tipos de trabajo, estados del flujo y tipos de proyectos institucionales. Las operaciones incluyen crear, editar, desactivar y eliminar elementos siempre que no existan dependencias.

## Hallazgos Clave

- Se añadieron campos `is_active` a las tablas `work_type`, `work_statuses` e `institutional_project_types`, habilitando la desactivación de elementos en lugar de eliminarlos.
- Se reescribieron los controladores para emplear **Form Requests** (`Store*Request`, `Update*Request`) y cumplir con el patrón de *controlador delgado*.
- Se incorporaron nuevas vistas de administración (listado, creación, edición y detalle) con confirmaciones de eliminación y métricas básicas por catálogo.
- Los modelos Eloquent ganaron métodos utilitarios (`scopeActive`, `hasAssociations`, `hasAssociatedProjects`) para encapsular la lógica de negocio y proteger dependencias.
- El grupo de rutas de administración se ajustó para permitir que el rol `viex_admin` acceda únicamente a los catálogos, manteniendo otras secciones reservadas para `super_admin`.

## Pruebas Ejecutadas

| Tipo | Descripción | Resultado |
| --- | --- | --- |
| PHPUnit | `php artisan test tests/Feature/Admin/ManageCatalogsTest.php` | ✅ |
| PHPUnit | `php artisan test tests/Feature/Admin/ManageOrganizationalUnitsTest.php` (regresión relacionada) | ✅ |

## Riesgos y Próximos Pasos

- **Datos existentes:** Se requiere actualizar los registros actuales para asignar valores coherentes en `is_active` (por defecto en `1`).
- **Permisos finos:** Evaluar si otros roles (p. ej. coordinadores) necesitan visibilidad de solo lectura sobre los catálogos.
- **Internacionalización:** Centralizar los textos utilizados en vistas dentro de archivos de traducción para facilitar futuras localizaciones.

## Conclusión

El caso de uso CU17 queda **implementado**. Los catálogos críticos del sistema ahora se administran mediante interfaces consistentes, con validación robusta, control de dependencias y cobertura de pruebas automatizadas.
