# Auditoría de Caso de Uso CU6: Autorizar Publicación de Resultados

**Fecha de Auditoría:** 8 de octubre de 2025  
**Auditor:** Ingeniero de Calidad Senior - Arquitecto de Software  
**Versión del Sistema:** VIEX 1.0  
**Estado General:** ⚠️ **PARCIALMENTE IMPLEMENTADO** (40% - requiere implementación)

---

## 📋 Resumen Ejecutivo

El **CU6: Autorizar Publicación de Resultados** está **parcialmente implementado** con una cobertura del **40%**. La infraestructura de base de datos existe (`publication_consent` field), y el checkbox aparece en los formularios de creación/edición, pero **faltan componentes críticos**:

### Estado Actual ⚠️
- ✅ **Campo BD:** `publication_consent` existe en tabla `work_of_extensions`
- ✅ **Formulario Creación:** Checkbox presente en `create.blade.php`
- ✅ **Formulario Edición:** Checkbox presente en `edit.blade.php`
- ✅ **Visualización:** Se muestra estado en `show.blade.php`
- ❌ **Acción Específica:** No hay botón "Autorizar Publicación" dedicado
- ❌ **Confirmación Modal:** No existe diálogo de confirmación específico
- ❌ **Notificación VIEX:** No se envía email a `viexproyectos@up.ac.pa`
- ❌ **Ruta Dedicada:** No hay `Route::patch('works/{work}/authorize-publication')`
- ❌ **Auditoría:** No se registra el cambio específico en historial

### Funcionalidad Faltante ❌
1. **Botón dedicado** en vista de detalle del trabajo
2. **Modal de confirmación** con información clara
3. **Método en controlador** para manejar la autorización
4. **Notificación automática** a VIEX Projects
5. **Registro en historial** del evento de autorización
6. **Event/Listener** para procesamiento asíncrono

### Impacto en Cumplimiento 📊
- **Precondiciones:** ✅ 100% (campo existe, profesor autenticado)
- **Flujo Principal:** ❌ 40% (pasos 1-2 faltan, pasos 3-6 no implementados)
- **Postcondiciones:** ⚠️ 60% (se guarda valor pero sin auditoría ni notificación)

---

## 📊 Tabla Detallada de Cumplimiento

| ID CU | Nombre del CU | Estado | Componentes Asociados | Evidencia/Problemas | Recomendación |
|-------|---------------|--------|----------------------|---------------------|---------------|
| **CU6** | **Autorizar Publicación de Resultados** | ⚠️ **40%** | 3 archivos existentes, 6 faltantes | Infraestructura BD existe, UI básica presente, pero falta flujo de autorización completo | Implementar controlador, ruta, notificación y auditoría |

---

## 🔍 Análisis Detallado por Flujo

### **Precondiciones**

#### ✅ **"El Profesor tiene un Trabajo de Extensión"**
**CUMPLIDO**

**Evidencia:**
El profesor puede tener trabajos en cualquier estado. El campo `publication_consent` está presente en la tabla.

```php
// database/migrations/2025_10_07_123510_create_work_of_extensions_table.php línea 26
$table->char('publication_consent', 1)->default('0');
```

**Modelo WorkOfExtension:**
```php
// app/Models/WorkOfExtension.php líneas 50-55
protected $fillable = [
    // ...
    'publication_consent',
    // ...
];

protected $casts = [
    'publication_consent' => 'boolean',
    // ...
];
```

**Scope Disponible:**
```php
// app/Models/WorkOfExtension.php líneas 196-198
public function scopeWithPublicationConsent($query) {
    return $query->where('publication_consent', true);
}
```

**Verificación:** ✅ Campo existe y funciona.

---

### **Flujo Principal**

#### ❌ **Paso 1: "El Profesor selecciona un Trabajo de Extensión para el cual desea autorizar la publicación"**
**NO IMPLEMENTADO ESPECÍFICAMENTE**

**Situación Actual:**
El profesor puede ver sus trabajos en la lista y acceder a la vista de detalle (`works.show`), pero **no hay una opción dedicada para "Autorizar Publicación"**.

**Vista Actual (`show.blade.php`):**
```php
// resources/views/works/show.blade.php líneas 153-159
<div class="col-md-12 mt-3">
    <div class="alert {{ $work->publication_consent ? 'alert-success' : 'alert-warning' }}">
        <i class="fas {{ $work->publication_consent ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
        <strong>Consentimiento de Publicación:</strong>
        {{ $work->publication_consent ? 'AUTORIZADO para publicación' : 'NO autorizado para publicación' }}
    </div>
</div>
```

**Problema:**
- ✅ Se **muestra** el estado actual del consentimiento
- ❌ No hay **botón o acción** para cambiar el estado
- ❌ El profesor debe ir a "Editar Trabajo" para modificarlo (no es intuitivo)

**Verificación:** ❌ Falta UI dedicada para autorización.

---

#### ❌ **Paso 2: "Selecciona la opción 'Autorizar Publicación'"**
**NO IMPLEMENTADO**

**Situación Actual:**
No existe un botón o enlace específico con el texto "Autorizar Publicación" en la vista de detalle del trabajo.

**Botones Actuales en `show.blade.php`:**
```php
// resources/views/works/show.blade.php líneas 285-314
{{-- Acciones según el estado --}}
@if($currentStatus === 'Borrador')
    <a href="{{ route('works.edit', $work) }}" class="btn btn-warning mb-2">
        <i class="fas fa-edit"></i>
        Editar Trabajo
    </a>
    
    <form action="{{ route('works.submit', $work) }}" method="POST">
        <button type="submit" class="btn btn-primary btn-block mb-2">
            <i class="fas fa-paper-plane"></i>
            Enviar para Revisión
        </button>
    </form>
    
    <form action="{{ route('works.destroy', $work) }}" method="POST">
        <button type="submit" class="btn btn-danger btn-block mb-2">
            <i class="fas fa-trash"></i>
            Eliminar Trabajo
        </button>
    </form>
@endif
```

**Búsqueda en Código:**
```bash
# Búsqueda realizada: "Autorizar Publicación" / "authorize publication"
# Resultado: 0 coincidencias en código PHP/Blade
```

**Workaround Actual:**
El profesor debe:
1. Ir a "Editar Trabajo"
2. Buscar el checkbox de `publication_consent` (al final del formulario básico)
3. Marcarlo/desmarcarlo
4. Guardar todo el trabajo

**Problema:**
- ❌ No es un flujo dedicado
- ❌ No hay confirmación específica
- ❌ Requiere editar todo el formulario

**Verificación:** ❌ Botón "Autorizar Publicación" no existe.

---

#### ❌ **Paso 3: "El sistema presenta una confirmación sobre la autorización"**
**NO IMPLEMENTADO**

**Situación Actual:**
No hay modal de confirmación específico para autorización de publicación.

**Confirmaciones Existentes:**
```javascript
// resources/views/works/show.blade.php línea 300
onclick="return confirm('¿Está seguro de enviar este trabajo para revisión?')"

// resources/views/works/show.blade.php línea 377
onclick="return confirm('¿Ha realizado todas las correcciones solicitadas?')"
```

**Lo que Falta:**
Un modal específico como:
```javascript
onclick="return confirm('¿Desea autorizar la publicación de este trabajo? \n\n' +
    'Al confirmar, autoriza a la Universidad de Panamá a publicar información sobre este trabajo ' +
    'en medios oficiales y portales institucionales. El equipo de VIEX será notificado.')"
```

**Verificación:** ❌ No hay confirmación específica para autorización.

---

#### ❌ **Paso 4: "El Profesor confirma su decisión"**
**NO IMPLEMENTADO COMO ACCIÓN INDEPENDIENTE**

**Situación Actual:**
Si el profesor edita el trabajo y marca el checkbox, la confirmación es genérica del formulario de actualización.

**Mensaje Actual al Guardar:**
```php
// app/Http/Controllers/WorkOfExtensionController.php línea 177
return redirect()
    ->route('works.show', $work)
    ->with('success', __('Trabajo actualizado exitosamente.'));
```

**Problema:**
- ❌ Mensaje genérico "Trabajo actualizado"
- ❌ No específica que se autorizó la publicación
- ❌ No diferencia entre otros cambios

**Lo que Debería Decir:**
```php
return redirect()
    ->route('works.show', $work)
    ->with('success', __('¡Autorización registrada exitosamente! VIEX ha sido notificado de su decisión.'));
```

**Verificación:** ❌ No hay confirmación específica post-autorización.

---

#### ⚠️ **Paso 5: "El sistema registra el consentimiento de publicación (`publication_consent = TRUE`) en el trabajo"**
**PARCIALMENTE CUMPLIDO**

**Evidencia - SE GUARDA EL VALOR:**
El campo `publication_consent` se actualiza correctamente en los formularios.

**Formulario de Creación:**
```php
// resources/views/works/create.blade.php líneas 296-303
<div class="custom-control custom-switch">
    <input type="checkbox" class="custom-control-input" id="publication_consent"
           name="publication_consent" value="1" {{ old('publication_consent') ? 'checked' : '' }}>
    <label class="custom-control-label" for="publication_consent">
        <strong>{{ __('Autorizo a la Universidad a publicar información sobre este trabajo') }}</strong>
    </label>
</div>
```

**Formulario de Edición:**
```php
// resources/views/works/edit.blade.php líneas 293-302
<div class="custom-control custom-checkbox">
    <input type="checkbox"
           class="custom-control-input"
           id="publication_consent"
           name="publication_consent"
           value="1"
           @checked(old('publication_consent', $work->publication_consent))>
    <label class="custom-control-label" for="publication_consent">
        <i class="fas fa-globe"></i>
        {{ __('Autorizo la publicación de este trabajo en el portal institucional') }}
    </label>
</div>
```

**Validación en FormRequest:**
```php
// app/Http/Requests/StoreCompleteWorkRequest.php línea 30
'publication_consent' => 'boolean',

// app/Http/Requests/StoreCompleteWorkRequest.php línea 151
'publication_consent' => $validated['publication_consent'] ?? false,
```

**Modelo - Método de Creación:**
```php
// app/Models/WorkOfExtension.php línea 316
'publication_consent' => $workData['publication_consent'] ?? false,
```

**Actualización en Controlador:**
```php
// app/Http/Controllers/WorkOfExtensionController.php línea 174
$work->updateFromCompleteRequest($request->validated(), $request->user());
```

**✅ Positivo:**
- El valor se guarda correctamente
- El checkbox funciona en creación y edición
- La validación está presente

**❌ Negativo:**
- No hay auditoría específica del cambio de autorización
- No se registra en `work_status_history` (aunque no es un cambio de estado)
- No hay logging específico del evento

**Verificación:** ⚠️ Se guarda pero sin auditoría dedicada.

---

#### ❌ **Paso 6: "El sistema envía una notificación al correo electrónico `viexproyectos@up.ac.pa`"**
**NO IMPLEMENTADO**

**Búsqueda de Notificaciones:**
```bash
# Archivos de notificaciones existentes:
- app/Notifications/WorkSubmittedForReview.php  (CU4/CU5)
- NO existe WorkPublicationAuthorized.php o similar
```

**Búsqueda del Email VIEX:**
```bash
# Búsqueda: "viexproyectos@up.ac.pa"
# Resultado: Solo en documentación (doc/sega/00_CU.md, doc/tecnica/Manual_Procedimientos.md)
# NO aparece en código fuente
```

**Notificaciones Existentes:**
```php
// app/Notifications/WorkSubmittedForReview.php
// Solo notifica a coordinador cuando se envía trabajo a revisión
```

**Listeners Existentes:**
```php
// app/Listeners/SendWorkSubmittedNotification.php
// Solo para evento WorkSubmitted (CU4)
```

**Lo que Falta:**
1. **Event:** `WorkPublicationAuthorized`
2. **Listener:** `SendPublicationAuthorizedNotification`
3. **Notification:** `WorkPublicationAuthorized`
4. **Destinatario:** Configurar email VIEX en `.env` o config

**Estructura Propuesta:**
```php
// app/Events/WorkPublicationAuthorized.php
namespace App\Events;

class WorkPublicationAuthorized {
    use Dispatchable, SerializesModels;
    
    public WorkOfExtension $work;
    public User $authorizedBy;
    public bool $isAuthorized; // true = autorizado, false = revocado
    
    public function __construct(WorkOfExtension $work, User $authorizedBy, bool $isAuthorized) {
        $this->work = $work;
        $this->authorizedBy = $authorizedBy;
        $this->isAuthorized = $isAuthorized;
    }
}
```

```php
// app/Listeners/SendPublicationAuthorizedNotification.php
namespace App\Listeners;

class SendPublicationAuthorizedNotification implements ShouldQueue {
    public function handle(WorkPublicationAuthorized $event): void {
        $viexEmail = config('work_types.viex_projects_email', 'viexproyectos@up.ac.pa');
        
        // Enviar a email específico de VIEX
        Notification::route('mail', $viexEmail)
            ->notify(new WorkPublicationAuthorized($event->work, $event->isAuthorized));
        
        Log::info('Notificación de autorización de publicación enviada', [
            'work_id' => $event->work->getKey(),
            'authorized_by' => $event->authorizedBy->getKey(),
            'is_authorized' => $event->isAuthorized,
            'viex_email' => $viexEmail
        ]);
    }
}
```

```php
// app/Notifications/WorkPublicationAuthorized.php
namespace App\Notifications;

class WorkPublicationAuthorized extends Notification {
    public WorkOfExtension $work;
    public bool $isAuthorized;
    
    public function toMail($notifiable): MailMessage {
        $subject = $this->isAuthorized 
            ? __('Autorización de Publicación de Trabajo de Extensión')
            : __('Revocación de Autorización de Publicación');
        
        $message = $this->isAuthorized
            ? __('El profesor ha autorizado la publicación del siguiente trabajo de extensión:')
            : __('El profesor ha revocado la autorización de publicación del siguiente trabajo:');
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('Estimado Equipo VIEX'))
            ->line($message)
            ->line(__('**Título:** :title', ['title' => $this->work->title]))
            ->line(__('**Tipo:** :type', ['type' => $this->work->workType->name]))
            ->line(__('**Responsable:** :responsible', ['responsible' => $this->work->responsibleUser->name]))
            ->line(__('**Unidad:** :unit', ['unit' => $this->work->organizationalUnit->name]))
            ->action(__('Ver Trabajo'), route('works.show', $this->work))
            ->line(__('Atentamente, Sistema VIEX - Universidad de Panamá'));
    }
}
```

**Configuración Necesaria:**
```php
// config/work_types.php
return [
    // ...
    'viex_projects_email' => env('VIEX_PROJECTS_EMAIL', 'viexproyectos@up.ac.pa'),
];
```

```bash
# .env
VIEX_PROJECTS_EMAIL=viexproyectos@up.ac.pa
```

**Verificación:** ❌ Notificación a VIEX no implementada.

---

### **Postcondiciones**

#### ⚠️ **"El consentimiento de publicación está registrado en el sistema"**
**PARCIALMENTE CUMPLIDO**

**Evidencia:**

**✅ Se Registra el Valor:**
```php
// El campo publication_consent se guarda en BD
// Verificable en:
mysql> SELECT id, title, publication_consent FROM work_of_extensions LIMIT 5;
```

**✅ Se Muestra en UI:**
```php
// resources/views/works/show.blade.php líneas 153-159
<div class="alert {{ $work->publication_consent ? 'alert-success' : 'alert-warning' }}">
    {{ $work->publication_consent ? 'AUTORIZADO para publicación' : 'NO autorizado para publicación' }}
</div>
```

**❌ No Hay Auditoría:**
El cambio no se registra específicamente. No se puede saber:
- ¿Cuándo autorizó el profesor?
- ¿El consentimiento fue dado en creación o después?
- ¿Se ha revocado alguna vez?

**❌ No Hay Timestamp:**
No existe `publication_consent_granted_at` para registrar la fecha.

**❌ No Hay Notificación:**
VIEX no es informado del cambio.

**Mejoras Necesarias:**

1. **Agregar Timestamp:**
```php
// Migration (nueva)
Schema::table('work_of_extensions', function (Blueprint $table) {
    $table->timestamp('publication_consent_granted_at')->nullable()->after('publication_consent');
    $table->unsignedBigInteger('publication_consent_granted_by')->nullable()->after('publication_consent_granted_at');
    
    $table->foreign('publication_consent_granted_by')
        ->references('id')
        ->on('users')
        ->onDelete('set null');
});
```

2. **Registrar en Tabla de Auditoría Personalizada:**
```php
// Crear tabla publication_consent_history
Schema::create('publication_consent_history', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_of_extension_id')->constrained()->onDelete('cascade');
    $table->boolean('consent_granted'); // true = autorizado, false = revocado
    $table->foreignId('changed_by_user_id')->constrained('users');
    $table->text('comments')->nullable();
    $table->timestamps();
});
```

**Verificación:** ⚠️ Registrado pero sin auditoría completa.

---

## 🔧 Componentes/Archivos Asociados - Listado Completo

### **Existentes (3 archivos)**

#### 1. **Modelo:** `app/Models/WorkOfExtension.php`
**Estado:** ✅ Parcial
- Línea 52: Campo `publication_consent` en `$fillable`
- Línea 73: Cast `'publication_consent' => 'boolean'`
- Líneas 196-198: Scope `scopeWithPublicationConsent()`

**Funcionalidad:**
- ✅ Almacenamiento del campo
- ✅ Query scope para filtrar trabajos autorizados
- ❌ No tiene método `authorizePublication()`

#### 2. **Vista Detalle:** `resources/views/works/show.blade.php`
**Estado:** ⚠️ Solo visualización
- Líneas 153-159: Muestra estado de autorización con alerta

**Funcionalidad:**
- ✅ Visualiza estado actual
- ❌ No tiene botón para cambiar autorización

#### 3. **Formulario Edición:** `resources/views/works/edit.blade.php`
**Estado:** ⚠️ Checkbox genérico
- Líneas 289-312: Checkbox de `publication_consent`

**Funcionalidad:**
- ✅ Permite marcar/desmarcar autorización
- ❌ No es un flujo dedicado
- ❌ No tiene confirmación específica

---

### **Faltantes (6 archivos mínimos)**

#### 1. **Controlador:** `app/Http/Controllers/WorkOfExtensionController.php`
**Método Faltante:** `authorizePublication()`

```php
/**
 * Autorizar/Revocar publicación de resultados del trabajo
 * CU06: Autorizar Publicación de Resultados
 */
public function authorizePublication(Request $request, WorkOfExtension $work): RedirectResponse {
    // Verificar autorización
    $this->authorize('update', $work);
    
    try {
        $isAuthorized = $request->boolean('authorized', true);
        
        // Actualizar consentimiento
        $work->update([
            'publication_consent' => $isAuthorized,
            'publication_consent_granted_at' => $isAuthorized ? now() : null,
            'publication_consent_granted_by' => $isAuthorized ? $request->user()->id : null,
        ]);
        
        // Disparar evento para notificar a VIEX
        \App\Events\WorkPublicationAuthorized::dispatch($work, $request->user(), $isAuthorized);
        
        $message = $isAuthorized
            ? __('¡Autorización registrada exitosamente! VIEX ha sido notificado.')
            : __('Autorización de publicación revocada.');
        
        return redirect()
            ->route('works.show', $work)
            ->with('success', $message);
            
    } catch (\Exception $e) {
        return redirect()
            ->route('works.show', $work)
            ->with('error', __('Error al procesar la autorización: ') . $e->getMessage());
    }
}
```

#### 2. **Ruta:** `routes/web.php`
**Línea de Inserción:** Después de línea 48

```php
// Ruta para autorizar publicación de trabajo (CU06)
Route::patch('works/{work}/authorize-publication', [WorkOfExtensionController::class, 'authorizePublication'])
    ->name('works.authorize-publication');
```

#### 3. **Event:** `app/Events/WorkPublicationAuthorized.php`
**Archivo Nuevo**

```php
<?php

namespace App\Events;

use App\Models\WorkOfExtension;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado cuando un profesor autoriza/revoca la publicación de su trabajo
 * CU06: Autorizar Publicación de Resultados
 */
class WorkPublicationAuthorized {
    use Dispatchable, SerializesModels;

    public WorkOfExtension $work;
    public User $authorizedBy;
    public bool $isAuthorized;

    /**
     * Create a new event instance.
     *
     * @param WorkOfExtension $work El trabajo
     * @param User $authorizedBy Usuario que autoriza/revoca
     * @param bool $isAuthorized True = autorizado, False = revocado
     */
    public function __construct(WorkOfExtension $work, User $authorizedBy, bool $isAuthorized) {
        $this->work = $work;
        $this->authorizedBy = $authorizedBy;
        $this->isAuthorized = $isAuthorized;
    }
}
```

#### 4. **Listener:** `app/Listeners/SendPublicationAuthorizedNotification.php`
**Archivo Nuevo**

```php
<?php

namespace App\Listeners;

use App\Events\WorkPublicationAuthorized;
use App\Notifications\WorkPublicationAuthorizedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Listener que envía notificación a VIEX cuando se autoriza publicación
 * CU06: Autorizar Publicación de Resultados
 */
class SendPublicationAuthorizedNotification implements ShouldQueue {
    /**
     * Handle the event.
     */
    public function handle(WorkPublicationAuthorized $event): void {
        $work = $event->work;
        $authorizedBy = $event->authorizedBy;
        $isAuthorized = $event->isAuthorized;

        Log::info('Procesando notificación de autorización de publicación', [
            'work_id' => $work->getKey(),
            'authorized_by' => $authorizedBy->getKey(),
            'is_authorized' => $isAuthorized
        ]);

        try {
            // Obtener email de VIEX desde configuración
            $viexEmail = config('work_types.viex_projects_email', 'viexproyectos@up.ac.pa');

            // Enviar notificación anónima al email de VIEX
            Notification::route('mail', $viexEmail)
                ->notify(new WorkPublicationAuthorizedNotification($work, $isAuthorized));

            Log::info('Notificación de autorización enviada a VIEX', [
                'work_id' => $work->getKey(),
                'viex_email' => $viexEmail,
                'is_authorized' => $isAuthorized
            ]);

        } catch (\Exception $e) {
            Log::error('Error al enviar notificación de autorización a VIEX', [
                'work_id' => $work->getKey(),
                'error' => $e->getMessage()
            ]);

            // Re-lanzar excepción para que la cola pueda reintentar
            throw $e;
        }
    }
}
```

#### 5. **Notification:** `app/Notifications/WorkPublicationAuthorizedNotification.php`
**Archivo Nuevo**

```php
<?php

namespace App\Notifications;

use App\Models\WorkOfExtension;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notificación enviada a VIEX cuando un profesor autoriza la publicación
 * CU06: Autorizar Publicación de Resultados
 */
class WorkPublicationAuthorizedNotification extends Notification implements ShouldQueue {
    use Queueable;

    public WorkOfExtension $work;
    public bool $isAuthorized;

    /**
     * Create a new notification instance.
     *
     * @param WorkOfExtension $work El trabajo
     * @param bool $isAuthorized True = autorizado, False = revocado
     */
    public function __construct(WorkOfExtension $work, bool $isAuthorized) {
        $this->work = $work;
        $this->isAuthorized = $isAuthorized;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage {
        $subject = $this->isAuthorized
            ? __('Autorización de Publicación de Trabajo de Extensión')
            : __('Revocación de Autorización de Publicación de Trabajo');

        $introLine = $this->isAuthorized
            ? __('El profesor ha autorizado la publicación del siguiente trabajo de extensión:')
            : __('El profesor ha revocado la autorización de publicación del siguiente trabajo:');

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting(__('Estimado Equipo VIEX - Proyectos'))
            ->line($introLine)
            ->line(__('**Título:** :title', ['title' => $this->work->getAttribute('title')]))
            ->line(__('**Tipo de Trabajo:** :type', ['type' => $this->work->workType->name ?? 'N/A']))
            ->line(__('**Responsable:** :responsible', ['responsible' => $this->work->responsibleUser->name ?? 'N/A']))
            ->line(__('**Unidad Organizacional:** :unit', ['unit' => $this->work->organizationalUnit->name ?? 'N/A']))
            ->line(__('**Período Académico:** :period', ['period' => $this->work->getAttribute('academic_period') ?? 'N/A']));

        if ($this->isAuthorized) {
            $mail->line(__('El profesor ha dado su consentimiento para que los resultados de este trabajo puedan ser publicados por la Universidad de Panamá en medios oficiales y portales institucionales.'));
        } else {
            $mail->line(__('El profesor ha decidido revocar su autorización previamente otorgada.'));
        }

        $mail->action(__('Ver Trabajo en Sistema'), route('works.show', $this->work))
            ->line(__('Esta notificación se envió automáticamente desde el Sistema VIEX.'))
            ->salutation(__('Atentamente, Sistema VIEX - Universidad de Panamá'));

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array {
        return [
            'work_id' => $this->work->getKey(),
            'work_title' => $this->work->getAttribute('title'),
            'is_authorized' => $this->isAuthorized,
            'responsible_name' => $this->work->responsibleUser->name ?? 'N/A',
            'action_url' => route('works.show', $this->work),
        ];
    }
}
```

#### 6. **Configuración:** `config/work_types.php`
**Línea de Inserción:** Al final del array

```php
// Email de VIEX para notificaciones de autorización de publicación (CU06)
'viex_projects_email' => env('VIEX_PROJECTS_EMAIL', 'viexproyectos@up.ac.pa'),
```

---

## 📈 Métricas de Cumplimiento

| Aspecto | Cumplimiento | Comentario |
|---------|--------------|------------|
| **Campo BD** | 100% | `publication_consent` existe y funciona |
| **Visualización Estado** | 100% | Se muestra claramente en vista detalle |
| **Checkbox Formularios** | 100% | Presente en creación y edición |
| **Botón Dedicado** | 0% | No existe "Autorizar Publicación" |
| **Modal Confirmación** | 0% | No hay diálogo específico |
| **Ruta Dedicada** | 0% | No existe `works.authorize-publication` |
| **Método Controlador** | 0% | No hay `authorizePublication()` |
| **Event** | 0% | No existe `WorkPublicationAuthorized` |
| **Listener** | 0% | No procesa evento de autorización |
| **Notification** | 0% | No se notifica a VIEX |
| **Auditoría** | 0% | No se registra timestamp ni usuario |
| **Configuración Email** | 0% | Email VIEX no está en config |

**Cumplimiento Global del CU6:** ⚠️ **40% - REQUIERE IMPLEMENTACIÓN**

---

## ✅ Recomendaciones Priorizadas

### 🔴 Alta Prioridad (Requeridas para Cumplimiento)

#### **1. Implementar Método en Controlador**

**Archivo:** `app/Http/Controllers/WorkOfExtensionController.php`  
**Acción:** Agregar método `authorizePublication()`

**Código:**
```php
/**
 * Autorizar/Revocar publicación de resultados del trabajo
 * CU06: Autorizar Publicación de Resultados
 */
public function authorizePublication(Request $request, WorkOfExtension $work): RedirectResponse {
    $this->authorize('update', $work);
    
    try {
        $isAuthorized = $request->boolean('authorized', true);
        
        $work->update([
            'publication_consent' => $isAuthorized,
        ]);
        
        // Disparar evento
        \App\Events\WorkPublicationAuthorized::dispatch($work, $request->user(), $isAuthorized);
        
        $message = $isAuthorized
            ? __('¡Autorización registrada exitosamente! VIEX ha sido notificado.')
            : __('Autorización de publicación revocada.');
        
        return redirect()
            ->route('works.show', $work)
            ->with('success', $message);
            
    } catch (\Exception $e) {
        return redirect()
            ->route('works.show', $work)
            ->with('error', __('Error al procesar la autorización: ') . $e->getMessage());
    }
}
```

#### **2. Agregar Ruta**

**Archivo:** `routes/web.php`  
**Línea:** Después de línea 48

```php
// Ruta para autorizar publicación de trabajo (CU06)
Route::patch('works/{work}/authorize-publication', [WorkOfExtensionController::class, 'authorizePublication'])
    ->name('works.authorize-publication');
```

#### **3. Crear Event, Listener, Notification**

Crear los 3 archivos nuevos detallados en la sección "Faltantes".

#### **4. Agregar Botón en Vista**

**Archivo:** `resources/views/works/show.blade.php`  
**Línea:** Insertar después de línea 314 (dentro del panel de acciones)

```blade
{{-- Autorizar/Revocar Publicación (CU06) --}}
@if(!$work->publication_consent)
    <form action="{{ route('works.authorize-publication', $work) }}" method="POST" class="mb-2">
        @csrf
        @method('PATCH')
        <input type="hidden" name="authorized" value="1">
        <button type="submit" class="btn btn-success btn-block"
            onclick="return confirm('¿Desea autorizar la publicación de este trabajo?\n\nAl confirmar, autoriza a la Universidad de Panamá a publicar información sobre este trabajo en medios oficiales. El equipo de VIEX será notificado.')">
            <i class="fas fa-globe"></i>
            Autorizar Publicación
        </button>
    </form>
@else
    <form action="{{ route('works.authorize-publication', $work) }}" method="POST" class="mb-2">
        @csrf
        @method('PATCH')
        <input type="hidden" name="authorized" value="0">
        <button type="submit" class="btn btn-outline-secondary btn-block"
            onclick="return confirm('¿Desea revocar la autorización de publicación?\n\nEsta acción notificará a VIEX sobre su decisión.')">
            <i class="fas fa-globe-slash"></i>
            Revocar Autorización
        </button>
    </form>
@endif
```

#### **5. Configurar Email VIEX**

**Archivo:** `config/work_types.php`

```php
'viex_projects_email' => env('VIEX_PROJECTS_EMAIL', 'viexproyectos@up.ac.pa'),
```

**Archivo:** `.env`

```bash
VIEX_PROJECTS_EMAIL=viexproyectos@up.ac.pa
```

---

### 🟡 Media Prioridad (Mejoras Recomendadas)

#### **6. Agregar Auditoría Completa**

**Migración:**
```php
// database/migrations/2025_10_XX_add_publication_consent_audit.php
Schema::table('work_of_extensions', function (Blueprint $table) {
    $table->timestamp('publication_consent_granted_at')->nullable()->after('publication_consent');
    $table->foreignId('publication_consent_granted_by')->nullable()
        ->after('publication_consent_granted_at')
        ->constrained('users')
        ->nullOnDelete();
});
```

**Actualizar Método del Controlador:**
```php
$work->update([
    'publication_consent' => $isAuthorized,
    'publication_consent_granted_at' => $isAuthorized ? now() : null,
    'publication_consent_granted_by' => $isAuthorized ? $request->user()->id : null,
]);
```

#### **7. Crear Tabla de Historial Dedicada**

```php
// database/migrations/2025_10_XX_create_publication_consent_history.php
Schema::create('publication_consent_history', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_of_extension_id')->constrained()->onDelete('cascade');
    $table->boolean('consent_granted');
    $table->foreignId('changed_by_user_id')->constrained('users');
    $table->text('comments')->nullable();
    $table->timestamps();
});
```

---

### 🟢 Baja Prioridad (Opcional)

#### **8. Método Dedicado en Modelo**

```php
// app/Models/WorkOfExtension.php
public function authorizePublication(User $by, bool $isAuthorized, ?string $comments = null): void {
    $this->update([
        'publication_consent' => $isAuthorized,
        'publication_consent_granted_at' => $isAuthorized ? now() : null,
        'publication_consent_granted_by' => $isAuthorized ? $by->id : null,
    ]);
    
    // Registrar en historial si existe tabla
    if (Schema::hasTable('publication_consent_history')) {
        DB::table('publication_consent_history')->insert([
            'work_of_extension_id' => $this->id,
            'consent_granted' => $isAuthorized,
            'changed_by_user_id' => $by->id,
            'comments' => $comments,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    Log::info('Autorización de publicación procesada', [
        'work_id' => $this->id,
        'authorized_by' => $by->id,
        'is_authorized' => $isAuthorized
    ]);
}
```

#### **9. Mostrar Historial de Autorizaciones**

En `show.blade.php`, mostrar timeline de cambios de autorización.

---

## 🏁 Conclusión

El **CU6: Autorizar Publicación de Resultados** está **al 40% de implementación**. La infraestructura básica existe (campo BD, formularios), pero **falta el flujo dedicado** que describe la especificación.

### **Componentes Existentes:**
- ✅ Campo `publication_consent` funcional
- ✅ Checkbox en formularios
- ✅ Visualización de estado

### **Componentes Faltantes:**
- ❌ Botón "Autorizar Publicación" dedicado
- ❌ Modal de confirmación específico
- ❌ Ruta y método del controlador
- ❌ Event/Listener/Notification
- ❌ Notificación a `viexproyectos@up.ac.pa`
- ❌ Auditoría del cambio

### **Impacto:**
- Los profesores **pueden** autorizar publicación (vía checkbox en edición)
- Pero VIEX **NO es notificado** automáticamente
- No hay **registro de auditoría** del evento
- El flujo **no es intuitivo** (requiere editar todo el formulario)

### **Esfuerzo de Implementación:**
- **Tiempo estimado:** 4-6 horas
- **Complejidad:** Media-Baja
- **Archivos a crear:** 3 (Event, Listener, Notification)
- **Archivos a modificar:** 3 (Controller, routes, show.blade.php, config)
- **Migraciones opcionales:** 2 (para auditoría completa)

**Estado Final Recomendado:** ✅ **100% con prioridad alta**

---

**Fecha de Reporte:** 8 de octubre de 2025  
**Próxima Acción:** Implementar funcionalidad completa del CU6  
**Auditor:** Equipo de Calidad - Proyecto VIEX
