# Auditoria CU18 - Configurar Criterios de Evaluacion

## Objetivo

Habilitar al personal VIEX para administrar los criterios de evaluacion utilizados durante la revision de trabajos, permitiendo crear, editar, desactivar y ajustar pesos y orden de presentacion asegurando una ponderacion total de 100% para los criterios activos.

## Cambios realizados

- Se creo el controlador `app/Http/Controllers/Admin/EvaluationCriteriaController.php` con acciones CRUD y sincronizacion masiva de pesos y orden.
- Se añadieron los FormRequests `StoreEvaluationCriteriaRequest`, `UpdateEvaluationCriteriaRequest` y `SyncEvaluationCriteriaRequest` para validar entradas y conversiones de booleanos.
- El modelo `App/Models/EvaluationCriteria` incorpora metodos de soporte `hasEvaluationDetails()` y `totalActiveWeight()`.
- Se actualizaron las vistas en `resources/views/admin/evaluation-criteria/` con formulario de gestion, resumenes y alerta de pesos.
- Se ajusto el seeder `database/seeders/EvaluationCriteriaSeeder.php` para que los pesos iniciales sumen 100.
- Se registraron las rutas en `routes/web.php` bajo el grupo `admin` con middleware `super_admin|viex_admin`.
- Se agrego la prueba de caracteristicas `tests/Feature/Admin/ManageEvaluationCriteriaTest.php` cubriendo creacion, actualizacion y sincronizacion de pesos.

## Base de datos

No se agregaron nuevas tablas. Se normalizaron los pesos iniciales de `evaluation_criteria` para que la suma de criterios activos sea 100.

## Pruebas

```bash
php artisan test --filter=ManageEvaluationCriteriaTest
```

## Riesgos y consideraciones

- Si los pesos no suman 100, el sistema advierte al usuario y bloquea la sincronizacion masiva.
- La eliminacion de un criterio permanece bloqueada cuando existen evaluaciones asociadas.
- Los cambios de peso pueden afectar el calculo de puntajes ponderados en evaluaciones ya registradas.
