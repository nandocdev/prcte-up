# Sistema de Notificaciones - Trabajos de Extensión VIEX

## Resumen

Se ha implementado con éxito un sistema completo de notificaciones para alertar a los coordinadores de extensión cuando los profesores envían trabajos para revisión (Caso de Uso CU04).

## Arquitectura Implementada

### 1. Componentes Principales

**Evento (Event)**: `App\Events\WorkSubmitted`
- Se dispara cuando un trabajo es enviado para revisión
- Transporta información del trabajo y del usuario que lo envía

**Listener**: `App\Listeners\SendWorkSubmittedNotification`
- Procesa el evento de manera asíncrona usando colas
- Encuentra el coordinador correspondiente a la unidad organizacional
- Envía la notificación al coordinador encontrado

**Notificación**: `App\Notifications\WorkSubmittedForReview`
- Soporta canales de correo electrónico y base de datos
- Contiene información detallada del trabajo enviado
- Incluye enlaces de acción para revisar el trabajo

### 2. Flujo de Trabajo

1. **Envío del trabajo**: Profesor usa `WorkOfExtension::submitForReview()`
2. **Disparo del evento**: Se ejecuta `WorkSubmitted::dispatch($work, $user)`
3. **Procesamiento asíncrono**: El listener se encola y procesa en background
4. **Búsqueda de coordinador**: Se localiza el coordinador de la unidad organizacional
5. **Envío de notificación**: Se envía por correo y se guarda en base de datos

## Archivos Implementados/Modificados

### Nuevos Archivos Creados

```
app/Events/WorkSubmitted.php
app/Listeners/SendWorkSubmittedNotification.php
app/Notifications/WorkSubmittedForReview.php
app/Providers/EventServiceProvider.php
app/Console/Commands/TestNotificationSystem.php
```

### Archivos Modificados

```
app/Models/WorkOfExtension.php - Agregado disparo de evento en submitForReview()
bootstrap/providers.php - EventServiceProvider ya estaba registrado
```

### Migraciones Ejecutadas

```
2025_07_21_140310_create_notifications_table - Para guardar notificaciones en BD
2025_07_19_173647_create_jobs_table - Para manejar colas (ya existía)
```

## Funcionalidades Implementadas

### ✅ Notificación por Correo Electrónico
- Plantilla HTML profesional con información del trabajo
- Botón de acción para revisar el trabajo directamente
- Información completa: título, tipo, responsable, unidad, fecha

### ✅ Notificación en Base de Datos
- Almacenamiento persistente de notificaciones
- Estado de lectura/no lectura
- Datos estructurados en formato JSON

### ✅ Procesamiento Asíncrono
- Las notificaciones se procesan en cola para no bloquear la interfaz
- Reintentos automáticos en caso de error
- Logging detallado para debugging

### ✅ Localización de Coordinadores
- Búsqueda automática por unidad organizacional
- Basado en roles Spatie (rol: 'coordinador_extension')
- Fallback documentado para casos sin coordinador

## Configuración de Colas

El sistema usa la configuración de colas de Laravel:

```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'database'),
```

Para procesar las colas en producción:
```bash
php artisan queue:work
```

Para desarrollo/testing:
```bash
php artisan queue:work --once
```

## Testing

### Comando de Prueba Implementado

```bash
php artisan test:notifications --work-id=1
```

Este comando permite probar el sistema completo disparando eventos manualmente.

### Resultados de Prueba Exitosos

- ✅ Evento disparado correctamente
- ✅ Listener ejecutado sin errores
- ✅ Coordinador encontrado automáticamente
- ✅ Notificación enviada por correo
- ✅ Notificación guardada en base de datos
- ✅ Datos estructurados correctamente en JSON

## Integración con el Sistema Existente

### Punto de Integración Principal

El sistema se integra automáticamente cuando se ejecuta:

```php
// En WorkOfExtensionController@submitForReview
$work->submitForReview($request->user());
```

Esto dispara toda la cadena de notificaciones de manera transparente.

### Configuración de Roles Requerida

Los coordinadores deben tener:
- Rol: `coordinador_extension` (usando Spatie Permission)
- Campo: `main_organizational_unit_id` asignado a su unidad

## Logs y Monitoreo

El sistema registra logs detallados:

- Inicio del procesamiento de notificaciones
- Resultado de búsqueda de coordinadores
- Éxito/fallo del envío de notificaciones
- Errores con stack trace para debugging

Logs ubicados en: `storage/logs/laravel.log`

## Estado de Implementación

### ✅ Completado
- [x] Arquitectura Event/Listener implementada
- [x] Notificación por correo con plantilla HTML
- [x] Notificación en base de datos
- [x] Procesamiento asíncrono con colas
- [x] Localización automática de coordinadores
- [x] Logging y manejo de errores
- [x] Sistema de testing implementado
- [x] Integración con workflow existente

### 🔄 Mejoras Futuras Sugeridas
- [ ] Implementar fallback a administrador si no hay coordinador
- [ ] Agregar notificaciones web en tiempo real (WebSockets/Pusher)
- [ ] Dashboard de notificaciones para coordinadores
- [ ] Plantillas de correo personalizables por unidad
- [ ] Estadísticas de notificaciones enviadas/leídas

## Conclusión

El sistema de notificaciones está **completamente funcional y listo para producción**. Cumple con todos los requerimientos del CU04 y proporciona una base sólida para futuras expansiones del sistema de notificaciones.

La implementación sigue las mejores prácticas de Laravel, es escalable, mantenible y está completamente integrada con el sistema de autorización y roles existente.
