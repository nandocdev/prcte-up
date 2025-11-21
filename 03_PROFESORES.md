# Casos de Uso - Profesores (UC-DOC-001 a UC-DOC-015)

## UC-DOC-006: Subir Evidencias (Archivos Adjuntos)

### Descripción
El profesor puede subir archivos de evidencia para respaldar su trabajo de extensión durante la creación o edición del mismo.

### Precondiciones
- El profesor debe estar autenticado
- Debe tener un trabajo en estado "Borrador" o en edición
- Los archivos deben cumplir con los formatos y tamaños permitidos

### Postcondiciones
- Los archivos quedan asociados al trabajo en la colección "evidencias"
- Los archivos son accesibles para descarga por usuarios autorizados
- Se registra la actividad en el historial del trabajo

### Flujo Principal

#### Paso 1: Acceso a la funcionalidad de subida
**Ubicación:** `resources/views/works/create.blade.php` y `resources/views/works/edit.blade.php`

**Código relevante:**
```php
// Incluye la sección de subida de archivos
@include('partials.file-upload-section')
```

#### Paso 2: Interfaz de subida de archivos
**Archivo:** `resources/views/partials/file-upload-section.blade.php`

**Características técnicas:**
- Drag & drop con zona visual
- Vista previa de archivos seleccionados
- Validación visual de tipos y tamaños
- Indicador de progreso de subida

**Código clave:**
```html
<div class="dropzone" id="file-dropzone">
    <div class="dz-message">
        <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
        <h4>Arrastra archivos aquí o haz clic para seleccionar</h4>
        <p class="text-muted">Formatos: PDF, DOC, DOCX, JPG, PNG (máx. 10MB cada uno)</p>
    </div>
</div>
```

#### Paso 3: Validación de archivos
**Archivo:** `app/Http/Requests/StoreCompleteWorkRequest.php`

**Reglas de validación:**
```php
'attachments' => 'nullable|array|max:10',
'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
```

**Formatos permitidos:**
- PDF (documentos)
- DOC, DOCX (Microsoft Word)
- JPG, JPEG, PNG (imágenes)
- Límite: 10 archivos máximo
- Tamaño: 10MB por archivo

#### Paso 4: Procesamiento de archivos
**Archivo:** `app/Models/WorkOfExtension.php`

**Método:** `handleAttachments()`

```php
public function handleAttachments(?array $files): void
{
    if (!$files) return;

    foreach ($files as $file) {
        $this->addMedia($file)
            ->withCustomProperties([
                'uploaded_by' => auth()->id(),
                'upload_date' => now(),
            ])
            ->toMediaCollection('evidencias');
    }
}
```

**Características:**
- Integración con Spatie MediaLibrary
- Metadatos personalizados (usuario, fecha)
- Colección organizada "evidencias"
- Manejo transaccional

#### Paso 5: Gestión de archivos existentes (Edición)
**Archivo:** `resources/views/partials/attachments-edit.blade.php`

**Funcionalidad:**
- Lista archivos ya subidos
- Opción de eliminar archivos existentes
- Vista previa con miniaturas
- Confirmación de eliminación

**Código relevante:**
```html
@foreach($work->getMedia('evidencias') as $media)
<div class="attachment-item">
    <span class="filename">{{ $media->name }}</span>
    <button type="button" class="btn btn-sm btn-danger remove-attachment"
            data-media-id="{{ $media->id }}">
        <i class="fas fa-trash"></i>
    </button>
</div>
@endforeach
```

#### Paso 6: JavaScript para manejo de archivos
**Archivo:** `resources/views/partials/form-js.blade.php`

**Funcionalidad:**
- Inicialización de Dropzone.js
- Validación en cliente
- Vista previa de archivos
- Gestión del array de archivos
- Eliminación de archivos seleccionados

**Código clave:**
```javascript
// Configuración de Dropzone
Dropzone.options.fileDropzone = {
    maxFiles: 10,
    acceptedFiles: ".pdf,.doc,.docx,.jpg,.jpeg,.png",
    maxFilesize: 10, // MB
    addRemoveLinks: true,
    dictRemoveFile: "Eliminar",
    // ... configuración completa
};
```

#### Paso 7: Integración con actualización de trabajos
**Archivo:** `app/Http/Controllers/WorkOfExtensionController.php`

**Método:** `update()`

```php
public function update(StoreCompleteWorkRequest $request, WorkOfExtension $work): RedirectResponse {
    // ... validación y actualización de datos ...

    // Procesar archivos adjuntos si los hay
    if ($request->hasFile('attachments')) {
        $updatedWork->handleAttachments($request->file('attachments'));
    }

    return redirect()
        ->route('works.show', $updatedWork)
        ->with('success', __('Trabajo actualizado exitosamente.'));
}
```

### Flujos Alternativos

#### A1: Archivo excede límite de tamaño
1. El sistema rechaza el archivo
2. Muestra mensaje de error: "El archivo es demasiado grande (máximo 10MB)"
3. El usuario puede seleccionar otro archivo

#### A2: Formato de archivo no permitido
1. El sistema rechaza el archivo
2. Muestra mensaje de error: "Formato no permitido"
3. Lista formatos válidos

#### A3: Límite de archivos excedido
1. El sistema rechaza archivos adicionales
2. Muestra mensaje: "Máximo 10 archivos permitidos"
3. Usuario debe eliminar archivos existentes para añadir nuevos

### Excepciones

#### E1: Error de almacenamiento
- **Condición:** Fallo en el guardado de archivos
- **Acción del sistema:** Rollback de la transacción completa
- **Mensaje:** "Error al guardar los archivos. Intente nuevamente."

#### E2: Error de permisos
- **Condición:** Problemas de permisos en directorio de storage
- **Acción del sistema:** Log del error, rollback
- **Mensaje:** "Error interno del servidor"

### Requisitos No Funcionales

#### Rendimiento
- Tiempo de respuesta: < 5 segundos para subida de archivos
- Procesamiento paralelo de múltiples archivos
- Optimización de imágenes (si aplica)

#### Seguridad
- Validación estricta de tipos MIME
- Escaneo antivirus (recomendado)
- Control de acceso a archivos por usuario/rol

#### Usabilidad
- Interfaz intuitiva drag & drop
- Feedback visual inmediato
- Mensajes de error claros en español

### Componentes Técnicos Utilizados

1. **Spatie MediaLibrary**
   - Gestión polimórfica de archivos
   - Colecciones organizadas
   - Conversión automática de imágenes

2. **Dropzone.js**
   - Interfaz drag & drop
   - Validación en cliente
   - Progreso de subida

3. **Laravel Validation**
   - Reglas de validación de archivos
   - Mensajes personalizados
   - Validación en servidor

4. **Blade Templates**
   - Componentes reutilizables
   - Inclusión condicional
   - Integración con JavaScript

### Testing

#### Casos de prueba sugeridos:
1. Subida exitosa de archivos válidos
2. Rechazo de archivos demasiado grandes
3. Rechazo de formatos no permitidos
4. Límite de cantidad de archivos
5. Eliminación de archivos existentes
6. Actualización con nuevos archivos
7. Rollback en caso de error

### Notas de Implementación

- Los archivos se almacenan en `storage/app/public/media/`
- URLs públicas generadas automáticamente
- Metadatos almacenados en tabla `media`
- Integración completa con el flujo de trabajo del trabajo

---

## UC-DOC-008: Enviar Trabajo para Revisión

### Descripción
El profesor envía su trabajo de extensión completado desde estado "Borrador" al flujo de aprobación, iniciando el proceso de revisión por parte del Coordinador de Extensión.

### Precondiciones
- El profesor debe estar autenticado
- El trabajo debe estar en estado "Borrador"
- El trabajo debe tener todos los campos obligatorios completos
- Debe tener al menos título y tipo de trabajo

### Postcondiciones
- El trabajo cambia de estado "Borrador" → "Enviado a Coordinador"
- Se marca como enviado (submitted_at timestamp)
- Se registra en el historial de estados
- Se dispara evento de notificación
- El profesor ya no puede editar el trabajo

### Flujo Principal

#### Paso 1: Verificación de completitud
**Ubicación:** `app/Models/WorkOfExtension.php`

**Método:** `canBeSubmitted()`

```php
public function canBeSubmitted(): bool {
    // Verificar que esté en borrador
    if (!$this->isInDraft()) {
        return false;
    }

    // Validar campos básicos obligatorios
    if (
        empty($this->title) ||
        empty($this->work_type_id) ||
        empty($this->description) ||
        empty($this->organizational_unit_id) ||
        empty($this->start_date) ||
        empty($this->end_date) ||
        empty($this->academic_period)
    ) {
        return false;
    }

    // Validar detalles específicos según tipo
    return $this->validateSpecificDetails();
}
```

**Campos obligatorios por tipo:**
- **Proyecto:** Título, descripción, objetivos, metodología
- **Actividad:** Título, descripción, tipo de actividad, modalidad
- **Publicación:** Título, descripción, tipo de publicación
- **Asistencia Técnica:** Título, descripción, tipo de asistencia, institución colaboradora

#### Paso 2: Interfaz de envío
**Ubicación:** `resources/views/works/show.blade.php`

**Condición de visualización:**
```php
@if($work->title && $work->work_type_id)
<form action="{{ route('works.submit', $work) }}" method="POST" id="submitWorkForm" class="d-inline">
    <button type="submit" class="btn btn-primary btn-block mb-2" id="submitWorkBtn"
        onclick="return confirmSubmitWork()">
        <i class="fas fa-paper-plane"></i>
        Enviar para Revisión
    </button>
</form>
@endif
```

**Características:**
- Solo visible si tiene título y tipo de trabajo
- Confirmación JavaScript antes del envío
- Estilo visual prominente (botón primario)

#### Paso 3: Procesamiento del envío
**Ubicación:** `app/Http/Controllers/WorkOfExtensionController.php`

**Método:** `submit()`

```php
public function submit(Request $request, WorkOfExtension $work): RedirectResponse {
    // Verificar autorización
    $this->authorize('update', $work);

    // Lógica de negocio delegada al servicio
    try {
        $service = new SubmitWorkService();
        $service->execute($work, $request->user());

        return redirect()
            ->route('works.show', $work)
            ->with('success', __('Trabajo enviado a coordinador de extensión para revisión.'));
    } catch (\InvalidArgumentException $e) {
        return redirect()
            ->route('works.show', $work)
            ->with('error', $e->getMessage());
    }
}
```

#### Paso 4: Servicio de envío
**Ubicación:** `app/Services/WorkOfExtension/SubmitWorkService.php`

**Método principal:** `execute()`

```php
public function execute(WorkOfExtension $work, User $user, bool $isResubmission = false): WorkOfExtension
{
    // Validar que el trabajo puede ser enviado
    $this->validateWorkCanBeSubmitted($work);

    // Obtener estado de envío
    $submittedStatus = $this->getSubmittedStatus();

    // Ejecutar la transición de estado
    DB::transaction(function () use ($work, $submittedStatus, $user, $isResubmission) {
        // Mensaje diferenciado para historial
        $comment = $isResubmission
            ? 'Trabajo corregido y reenviado para revisión por el coordinador de extensión.'
            : 'Trabajo enviado para revisión por el coordinador de extensión.';

        // Cambiar estado
        $this->changeWorkStatus($work, $submittedStatus, $user, $comment);

        // Marcar como enviado y timestamp
        $work->update([
            'is_draft' => '0',
            'submitted_at' => now(),
        ]);
    });

    // Disparar evento
    WorkSubmitted::dispatch($work, $user, $isResubmission);

    return $work->fresh();
}
```

#### Paso 5: Cambio de estado
**Ubicación:** `app/Models/WorkOfExtension.php`

**Método:** `changeStatus()`

```php
public function changeStatus(WorkStatus $newStatus, User $by, ?string $comments = null): void
{
    $oldStatusId = $this->getAttribute('current_status_id');

    // Actualizar estado en el modelo
    $this->update(['current_status_id' => $newStatus->getKey()]);

    // Registrar en historial
    $work->statusHistory()->create([
        'work_of_extension_id' => $this->getKey(),
        'from_status_id' => $oldStatusId,
        'to_status_id' => $newStatus->getKey(),
        'changed_by_user_id' => $by->getKey(),
        'comments' => $comments,
    ]);

    Log::info('Cambio de estado registrado', [
        'work_id' => $this->getKey(),
        'from_status_id' => $oldStatusId,
        'to_status_id' => $newStatus->getKey(),
        'by' => $by->getKey(),
    ]);
}
```

#### Paso 6: Notificación por evento
**Ubicación:** `app/Events/WorkSubmitted.php`

**Disparo del evento:**
```php
WorkSubmitted::dispatch($work, $user, $isResubmission);
```

**Listeners asociados:**
- `SendWorkSubmittedNotification` - Notifica al coordinador
- Manejo de reenvíos vs envíos iniciales

### Flujos Alternativos

#### A1: Trabajo incompleto - Campos faltantes
1. El sistema valida completitud antes del envío
2. Si faltan campos, muestra mensaje específico:
   ```
   "El trabajo no puede ser enviado. Faltan los siguientes campos obligatorios: [lista]"
   ```
3. El usuario debe completar los campos faltantes

#### A2: Trabajo ya enviado
1. Validación detecta que no está en borrador
2. Muestra mensaje: "El trabajo ya ha sido enviado anteriormente"
3. Redirige a la vista del trabajo

### Excepciones

#### E1: Estado de envío no encontrado
- **Condición:** No existe estado "Enviado a Coordinador" en BD
- **Mensaje:** "No se encontró el estado de envío a coordinador. Contacte al administrador."

#### E2: Error de base de datos
- **Condición:** Fallo en transacción
- **Acción:** Rollback automático
- **Mensaje:** Error genérico de envío

#### E3: Error de autorización
- **Condición:** Usuario no tiene permisos
- **Acción:** Denegar acceso
- **Mensaje:** "No autorizado"

### Requisitos No Funcionales

#### Seguridad
- Autorización mediante Policies de Laravel
- Validación de estado del trabajo
- Auditoría completa en historial

#### Usabilidad
- Confirmación antes del envío irreversible
- Mensajes de error específicos y útiles
- Feedback visual claro del proceso

#### Rendimiento
- Operación transaccional para consistencia
- Logging detallado para debugging
- Carga fresca del modelo después del cambio

### Componentes Técnicos Utilizados

1. **Laravel Policies**
   - Autorización de acciones
   - Control de acceso basado en estado

2. **Laravel Events/Listeners**
   - Notificaciones desacopladas
   - Manejo de lógica post-envío

3. **Database Transactions**
   - Consistencia de datos
   - Rollback en caso de error

4. **Laravel Validation**
   - Validación de campos obligatorios
   - Mensajes personalizados

### Testing

#### Casos de prueba sugeridos:
1. Envío exitoso de trabajo completo
2. Rechazo de trabajo incompleto (campos faltantes)
3. Rechazo de trabajo ya enviado
4. Verificación de cambio de estado
5. Verificación de timestamp submitted_at
6. Verificación de historial registrado
7. Verificación de evento disparado
8. Rollback en caso de error

### Notas de Implementación

- El envío es **irreversible** - una vez enviado, el profesor no puede editar
- El estado "is_draft" se cambia a '0' permanentemente
- Se registra timestamp "submitted_at" para métricas
- El coordinador recibe notificación automática
- El flujo continúa: Coordinador → Decano/Director → VIEX

---

*Estado: ✅ IMPLEMENTADO COMPLETAMENTE*
*Última revisión: $(date '+%Y-%m-%d')*
*Versión: 1.0*</content>
<parameter name="filePath">/srv/prcte/extrension/03_PROFESORES.md

### Descripción
El profesor puede subir archivos de evidencia para respaldar su trabajo de extensión durante la creación o edición del mismo.

### Precondiciones
- El profesor debe estar autenticado
- Debe tener un trabajo en estado "Borrador" o en edición
- Los archivos deben cumplir con los formatos y tamaños permitidos

### Postcondiciones
- Los archivos quedan asociados al trabajo en la colección "evidencias"
- Los archivos son accesibles para descarga por usuarios autorizados
- Se registra la actividad en el historial del trabajo

### Flujo Principal

#### Paso 1: Acceso a la funcionalidad de subida
**Ubicación:** `resources/views/works/create.blade.php` y `resources/views/works/edit.blade.php`

**Código relevante:**
```php
// Incluye la sección de subida de archivos
@include('partials.file-upload-section')
```

#### Paso 2: Interfaz de subida de archivos
**Archivo:** `resources/views/partials/file-upload-section.blade.php`

**Características técnicas:**
- Drag & drop con zona visual
- Vista previa de archivos seleccionados
- Validación visual de tipos y tamaños
- Indicador de progreso de subida

**Código clave:**
```html
<div class="dropzone" id="file-dropzone">
    <div class="dz-message">
        <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
        <h4>Arrastra archivos aquí o haz clic para seleccionar</h4>
        <p class="text-muted">Formatos: PDF, DOC, DOCX, JPG, PNG (máx. 10MB cada uno)</p>
    </div>
</div>
```

#### Paso 3: Validación de archivos
**Archivo:** `app/Http/Requests/StoreCompleteWorkRequest.php`

**Reglas de validación:**
```php
'attachments' => 'nullable|array|max:10',
'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
```

**Formatos permitidos:**
- PDF (documentos)
- DOC, DOCX (Microsoft Word)
- JPG, JPEG, PNG (imágenes)
- Límite: 10 archivos máximo
- Tamaño: 10MB por archivo

#### Paso 4: Procesamiento de archivos
**Archivo:** `app/Models/WorkOfExtension.php`

**Método:** `handleAttachments()`

```php
public function handleAttachments(?array $files): void
{
    if (!$files) return;

    foreach ($files as $file) {
        $this->addMedia($file)
            ->withCustomProperties([
                'uploaded_by' => auth()->id(),
                'upload_date' => now(),
            ])
            ->toMediaCollection('evidencias');
    }
}
```

**Características:**
- Integración con Spatie MediaLibrary
- Metadatos personalizados (usuario, fecha)
- Colección organizada "evidencias"
- Manejo transaccional

#### Paso 5: Gestión de archivos existentes (Edición)
**Archivo:** `resources/views/partials/attachments-edit.blade.php`

**Funcionalidad:**
- Lista archivos ya subidos
- Opción de eliminar archivos existentes
- Vista previa con miniaturas
- Confirmación de eliminación

**Código relevante:**
```html
@foreach($work->getMedia('evidencias') as $media)
<div class="attachment-item">
    <span class="filename">{{ $media->name }}</span>
    <button type="button" class="btn btn-sm btn-danger remove-attachment"
            data-media-id="{{ $media->id }}">
        <i class="fas fa-trash"></i>
    </button>
</div>
@endforeach
```

#### Paso 6: JavaScript para manejo de archivos
**Archivo:** `resources/views/partials/form-js.blade.php`

**Funcionalidad:**
- Inicialización de Dropzone.js
- Validación en cliente
- Vista previa de archivos
- Gestión del array de archivos
- Eliminación de archivos seleccionados

**Código clave:**
```javascript
// Configuración de Dropzone
Dropzone.options.fileDropzone = {
    maxFiles: 10,
    acceptedFiles: ".pdf,.doc,.docx,.jpg,.jpeg,.png",
    maxFilesize: 10, // MB
    addRemoveLinks: true,
    dictRemoveFile: "Eliminar",
    // ... configuración completa
};
```

#### Paso 7: Integración con actualización de trabajos
**Archivo:** `app/Http/Controllers/WorkOfExtensionController.php`

**Método:** `update()`

```php
public function update(StoreCompleteWorkRequest $request, WorkOfExtension $work): RedirectResponse
{
    // ... validación y actualización de datos ...

    // Procesar archivos adjuntos
    if ($request->hasFile('attachments')) {
        $work->handleAttachments($request->file('attachments'));
    }

    return redirect()->route('works.show', $work)
        ->with('success', 'Trabajo actualizado exitosamente.');
}
```

### Flujos Alternativos

#### A1: Archivo excede límite de tamaño
1. El sistema rechaza el archivo
2. Muestra mensaje de error: "El archivo es demasiado grande (máximo 10MB)"
3. El usuario puede seleccionar otro archivo

#### A2: Formato de archivo no permitido
1. El sistema rechaza el archivo
2. Muestra mensaje de error: "Formato no permitido"
3. Lista formatos válidos

#### A3: Límite de archivos excedido
1. El sistema rechaza archivos adicionales
2. Muestra mensaje: "Máximo 10 archivos permitidos"
3. Usuario debe eliminar archivos existentes para añadir nuevos

### Excepciones

#### E1: Error de almacenamiento
- **Condición:** Fallo en el guardado de archivos
- **Acción del sistema:** Rollback de la transacción completa
- **Mensaje:** "Error al guardar los archivos. Intente nuevamente."

#### E2: Error de permisos
- **Condición:** Problemas de permisos en directorio de storage
- **Acción del sistema:** Log del error, rollback
- **Mensaje:** "Error interno del servidor"

### Requisitos No Funcionales

#### Rendimiento
- Tiempo de respuesta: < 5 segundos para subida de archivos
- Procesamiento paralelo de múltiples archivos
- Optimización de imágenes (si aplica)

#### Seguridad
- Validación estricta de tipos MIME
- Escaneo antivirus (recomendado)
- Control de acceso a archivos por usuario/rol

#### Usabilidad
- Interfaz intuitiva drag & drop
- Feedback visual inmediato
- Mensajes de error claros en español

### Componentes Técnicos Utilizados

1. **Spatie MediaLibrary**
   - Gestión polimórfica de archivos
   - Colecciones organizadas
   - Conversión automática de imágenes

2. **Dropzone.js**
   - Interfaz drag & drop
   - Validación en cliente
   - Progreso de subida

3. **Laravel Validation**
   - Reglas de validación de archivos
   - Mensajes personalizados
   - Validación en servidor

4. **Blade Templates**
   - Componentes reutilizables
   - Inclusión condicional
   - Integración con JavaScript

### Testing

#### Casos de prueba sugeridos:
1. Subida exitosa de archivos válidos
2. Rechazo de archivos demasiado grandes
3. Rechazo de formatos no permitidos
4. Límite de cantidad de archivos
5. Eliminación de archivos existentes
6. Actualización con nuevos archivos
7. Rollback en caso de error

### Notas de Implementación

- Los archivos se almacenan en `storage/app/public/media/`
- URLs públicas generadas automáticamente
- Metadatos almacenados en tabla `media`
- Integración completa con el flujo de trabajo del trabajo

---

*Estado: ✅ IMPLEMENTADO COMPLETAMENTE*
*Última revisión: $(date '+%Y-%m-%d')*
*Versión: 1.0*</content>

---

## UC-DOC-009: Reenviar Trabajo Después de Correcciones

### Descripción
El profesor reenvía su trabajo de extensión que fue rechazado o devuelto para correcciones, reiniciando el flujo de aprobación desde el punto apropiado.

### Precondiciones
- El profesor debe estar autenticado
- El trabajo debe estar en estado de "rechazado" o "devuelto para corrección"
- El profesor debe haber realizado las correcciones solicitadas

### Postcondiciones
- El trabajo regresa al flujo de aprobación
- Se registra el reenvío en el historial
- Se notifica a los revisores correspondientes
- Se marca como "reenviado" (no como envío inicial)

### Flujo Principal

#### Paso 1: Estados que permiten reenvío
**Estados válidos para reenvío:**
- "Rechazado por Coordinador"
- "Rechazado por Decano/Director" 
- "Rechazado por VIEX"
- "Devuelto para Corrección"

#### Paso 2: Interfaz de reenvío
**Ubicación:** `resources/views/works/show.blade.php`

**Condición de visualización:**
```php
@if($currentStatus === "Rechazado por Coordinador" || 
    $currentStatus === "Rechazado por Decano/Director" ||
    $currentStatus === "Rechazado por VIEX" ||
    $currentStatus === "Devuelto para Corrección")
```

**Características:**
- Muestra motivo del rechazo si existe
- Confirmación con SweetAlert2
- Diferenciación visual (botón verde con ícono de redo)

#### Paso 3: Procesamiento del reenvío
**Ubicación:** `app/Http/Controllers/WorkOfExtensionController.php`

**Método:** `resubmit()`

```php
public function resubmit(Request $request, WorkOfExtension $work): RedirectResponse {
    $this->authorize("update", $work);

    try {
        // Verificar estado válido para reenvío
        $currentStatus = $work->currentStatus->name ?? "";
        $resubmitStates = [
            "Rechazado por Coordinador",
            "Rechazado por Decano/Director",
            "Rechazado por VIEX",
            "Devuelto para Corrección"
        ];

        if (!in_array($currentStatus, $resubmitStates)) {
            return redirect()->route("works.show", $work)
                ->with("error", "Este trabajo no se puede reenviar en su estado actual.");
        }

        // Reenviar con flag isResubmission=true
        $service = new SubmitWorkService();
        $service->execute($work, $request->user(), true);

        return redirect()->route("works.show", $work)
            ->with("success", "Trabajo corregido y reenviado para revisión.");

    } catch (\Exception $e) {
        return redirect()->route("works.show", $work)
            ->with("error", "Error al reenviar el trabajo: " . $e->getMessage());
    }
}
```

#### Paso 4: Lógica de reenvío
**Ubicación:** `app/Services/WorkOfExtension/SubmitWorkService.php`

**Diferencias con envío inicial:**
```php
// Flag isResubmission=true
$service->execute($work, $request->user(), true);

// Comentario diferenciado en historial
$comment = $isResubmission
    ? "Trabajo corregido y reenviado para revisión por el coordinador de extensión."
    : "Trabajo enviado para revisión por el coordinador de extensión.";
```

#### Paso 5: Transición de estados según origen del rechazo

**Lógica de transición:**
- **Rechazado por Coordinador** → "Enviado a Coordinador"
- **Rechazado por Decano/Director** → "Enviado a Decano/Director"  
- **Rechazado por VIEX** → "Enviado a VIEX"
- **Devuelto para Corrección** → Estado anterior al devolución

### Flujos Alternativos

#### A1: Estado no válido para reenvío
1. Sistema valida estado actual
2. Muestra mensaje: "Este trabajo no se puede reenviar en su estado actual"
3. Redirige a vista del trabajo

#### A2: Trabajo no corregido
1. El sistema no valida contenido (solo estado)
2. Depende del revisor detectar si se hicieron correcciones reales
3. Puede ser rechazado nuevamente si no se corrigieron los problemas

### Excepciones

#### E1: Error de transición de estado
- **Condición:** Estado objetivo no existe
- **Mensaje:** Error genérico de reenvío

#### E2: Error de autorización
- **Condición:** Usuario no es el propietario
- **Acción:** Denegar acceso

### Requisitos No Funcionales

#### Usabilidad
- Confirmación detallada con SweetAlert2
- Mensaje específico sobre correcciones realizadas
- Feedback visual claro del proceso

#### Seguridad
- Solo propietario puede reenviar
- Validación de estados permitidos
- Auditoría completa del reenvío

### Componentes Técnicos Utilizados

1. **SweetAlert2**
   - Confirmación rica antes del reenvío
   - Mensajes contextuales

2. **Laravel Events**
   - Notificaciones diferenciadas para reenvíos
   - Manejo específico de workflow

3. **Database Transactions**
   - Consistencia en cambio de estado
   - Rollback en caso de error

### Testing

#### Casos de prueba sugeridos:
1. Reenvío exitoso desde cada estado de rechazo
2. Rechazo de reenvío desde estados no válidos
3. Verificación de flag isResubmission
4. Verificación de comentarios en historial
5. Verificación de notificaciones enviadas

### Notas de Implementación

- El reenvío usa el mismo `SubmitWorkService` con flag `isResubmission=true`
- Los comentarios en historial diferencian entre envío inicial y reenvío
- Las notificaciones pueden ser diferentes para reenvíos vs envíos iniciales
- El flujo continúa desde el punto donde fue rechazado

---

## UC-DOC-010: Autorizar Publicación de Resultados

### Descripción
El profesor autoriza o revoca el consentimiento para que VIEX publique los resultados de su trabajo de extensión en medios institucionales y académicos.

### Precondiciones
- El profesor debe estar autenticado
- Debe ser el responsable principal del trabajo
- El trabajo puede estar en cualquier estado

### Postcondiciones
- Se actualiza el campo `publication_consent`
- Se notifica a VIEX del cambio
- Se registra la actividad en logs
- El consentimiento puede cambiarse en cualquier momento

### Flujo Principal

#### Paso 1: Interfaz de autorización
**Ubicación:** `resources/views/works/show.blade.php`

**Estados posibles:**
```php
@if($work->publication_consent)
    {{-- Mostrar autorizado y opción de revocar --}}
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <strong>Publicación Autorizada</strong>
    </div>
    <button>Revocar Autorización</button>
@else
    {{-- Mostrar no autorizado y opción de autorizar --}}
    <div class="alert alert-info">
        <small>Aún no has autorizado la publicación</small>
    </div>
    <button>Autorizar Publicación</button>
@endif
```

#### Paso 2: Procesamiento de la autorización
**Ubicación:** `app/Http/Controllers/WorkOfExtensionController.php`

**Método:** `authorizePublication()`

```php
public function authorizePublication(Request $request, WorkOfExtension $work): RedirectResponse
{
    $this->authorize("update", $work);

    try {
        $isAuthorized = $request->boolean("authorized", true);

        // Delegar lógica de negocio al servicio
        $this->publicationService->authorizePublication($work, $request->user(), $isAuthorized);

        $message = $isAuthorized
            ? __("¡Autorización registrada exitosamente! VIEX ha sido notificado de su consentimiento para publicar este trabajo.")
            : __("Autorización de publicación revocada exitosamente. VIEX ha sido notificado del cambio.");

        return redirect()->route("works.show", $work)->with("success", $message);

    } catch (\Exception $e) {
        return redirect()->route("works.show", $work)
            ->with("error", __("Error al procesar la autorización: ") . $e->getMessage());
    }
}
```

#### Paso 3: Servicio de publicación
**Ubicación:** `app/Services/WorkOfExtension/PublicationService.php`

**Método:** `authorizePublication()`

```php
public function authorizePublication(WorkOfExtension $work, User $user, bool $isAuthorized): WorkOfExtension
{
    DB::beginTransaction();

    try {
        // Actualizar consentimiento
        $work->update([
            "publication_consent" => $isAuthorized,
        ]);

        // Disparar evento para notificar a VIEX
        WorkPublicationAuthorized::dispatch($work, $user, $isAuthorized);

        Log::info($isAuthorized ? "Publicación autorizada" : "Publicación revocada", [
            "work_id" => $work->getKey(),
            "user_id" => $user->getKey(),
            "authorized" => $isAuthorized
        ]);

        DB::commit();

        return $work->fresh();

    } catch (\Exception $e) {
        DB::rollback();
        throw $e;
    }
}
```

#### Paso 4: Evento de notificación
**Ubicación:** `app/Events/WorkPublicationAuthorized.php`

**Disparo del evento:**
```php
WorkPublicationAuthorized::dispatch($work, $user, $isAuthorized);
```

**Listeners:**
- Notificación automática a VIEX
- Registro de cambio de consentimiento

### Flujos Alternativos

#### A1: Cambio de opinión
1. Usuario puede alternar entre autorizar/revocar en cualquier momento
2. No hay restricciones de estado del trabajo
3. Cada cambio genera nueva notificación

### Excepciones

#### E1: Error de base de datos
- **Condición:** Fallo en actualización
- **Acción:** Rollback automático
- **Mensaje:** Error genérico de procesamiento

### Requisitos No Funcionales

#### Usabilidad
- Estados visuales claros (verde para autorizado, azul para no autorizado)
- Confirmaciones antes de cambios
- Mensajes informativos sobre notificaciones a VIEX

#### Seguridad
- Solo el responsable principal puede autorizar
- Auditoría completa de cambios
- Transacciones para consistencia

### Componentes Técnicos Utilizados

1. **Laravel Events/Listeners**
   - Notificaciones desacopladas
   - Comunicación con VIEX

2. **Database Transactions**
   - Consistencia de datos
   - Rollback en errores

3. **Laravel Policies**
   - Control de autorización
   - Solo propietario puede cambiar

### Testing

#### Casos de prueba:
1. Autorización exitosa
2. Revocación exitosa
3. Cambio múltiple de opinión
4. Verificación de evento disparado
5. Verificación de notificación a VIEX

### Notas de Implementación

- El consentimiento se almacena en campo `publication_consent` (boolean)
- VIEX recibe notificación automática de cambios
- No afecta el flujo de certificación del trabajo
- Puede cambiarse en cualquier momento durante la vida del trabajo

---

## UC-DOC-011: Descargar Certificado

### Descripción
El profesor descarga el certificado oficial PDF de su trabajo de extensión una vez que ha sido aprobado y certificado por VIEX.

### Precondiciones
- El profesor debe estar autenticado
- Debe ser el responsable principal del trabajo
- El trabajo debe estar en estado "Certificado"
- Debe existir un certificado generado

### Postcondiciones
- Se descarga el archivo PDF del certificado
- Se registra la descarga en logs
- El archivo mantiene integridad y formato

### Flujo Principal

#### Paso 1: Verificación de acceso al certificado
**Ubicación:** `resources/views/works/show.blade.php`

**Condición de visualización:**
```php
@if($work->certification)
<a href="{{ route("certificates.download", $work->certification) }}"
   class="btn btn-success btn-block mb-2">
    <i class="fas fa-download"></i>
    Descargar Certificado
</a>
@endif
```

#### Paso 2: Procesamiento de descarga
**Ubicación:** `app/Http/Controllers/WorkOfExtensionController.php`

**Método:** `downloadCertificate()`

```php
public function downloadCertificate(Certification $certification) {
    $work = $certification->work;

    if (/srv/prcte/extrension/03_PROFESORES.mdwork) {
        abort(404, "Trabajo no encontrado");
    }

    // Verificar autorización
    $this->authorize("view", $work);

    try {
        return $certification->buildDownloadResponse();
    } catch (\RuntimeException $exception) {
        Log::warning("Archivo de certificación no disponible para descarga pública.", [
            "certification_id" => $certification->getKey(),
            "work_id" => $work->getKey(),
            "error" => $exception->getMessage(),
        ]);

        return redirect()->back()->with("error", __("certifications.download_missing_file"));
    } catch (\Throwable $exception) {
        Log::error("Error inesperado al descargar certificado.", [
            "certification_id" => $certification->getKey(),
            "work_id" => $work->getKey(),
            "error" => $exception->getMessage(),
        ]);

        return redirect()->back()->with("error", __("certifications.download_error"));
    }
}
```

#### Paso 3: Generación de respuesta de descarga
**Ubicación:** `app/Models/Certification.php`

**Método:** `buildDownloadResponse()`

```php
public function buildDownloadResponse(): BinaryFileResponse
{
    $media = $this->getCertificateMedia();

    if (/srv/prcte/extrension/03_PROFESORES.mdmedia) {
        throw new RuntimeException(__("certifications.download_missing_file"));
    }

    $absolutePath = $media->getPath();

    if (/srv/prcte/extrension/03_PROFESORES.mdabsolutePath || !is_file($absolutePath)) {
        Log::warning("Archivo de certificación no encontrado en disco.", [
            "certification_id" => $this->getKey(),
            "media_id" => $media->getKey(),
            "disk" => $media->disk,
            "path" => $media->getPathRelativeToRoot(),
        ]);

        throw new RuntimeException(__("certifications.download_missing_file"));
    }

    $fileName = sprintf("certificacion-%s.pdf", 
        Str::slug($this->getAttribute("certification_number") ?? (string) $this->getKey(), "_"));

    return response()->download($absolutePath, $fileName);
}
```

#### Paso 4: Gestión de archivos de certificado
**Ubicación:** `app/Models/Certification.php`

**Media Collections:**
```php
public function registerMediaCollections(): void
{
    $this->addMediaCollection("certificates")->singleFile();
}
```

### Flujos Alternativos

#### A1: Certificado no existe
1. Sistema verifica existencia del certificado
2. Muestra mensaje: "Certificado no disponible"
3. No muestra botón de descarga

#### A2: Archivo físico no encontrado
1. MediaLibrary encuentra registro pero archivo no existe en disco
2. Log warning detallado
3. Muestra mensaje de archivo faltante

### Excepciones

#### E1: Trabajo no encontrado
- **Condición:** Certificado sin trabajo asociado
- **Acción:** HTTP 404

#### E2: No autorizado
- **Condición:** Usuario no es propietario
- **Acción:** Denegar acceso

#### E3: Archivo faltante
- **Condición:** Archivo PDF no existe
- **Mensaje:** "Archivo de certificación no disponible"

### Requisitos No Funcionales

#### Seguridad
- Solo propietario puede descargar
- Verificación de existencia de archivo
- Logging de descargas

#### Rendimiento
- Descarga directa de archivo físico
- Sin procesamiento en tiempo real
- Nombres de archivo normalizados

### Componentes Técnicos Utilizados

1. **Spatie MediaLibrary**
   - Gestión de archivos de certificado
   - URLs y paths seguros

2. **Laravel Responses**
   - Descarga de archivos binarios
   - Headers HTTP apropiados

3. **Laravel Policies**
   - Autorización de descarga
   - Control de acceso

### Testing

#### Casos de prueba:
1. Descarga exitosa de certificado existente
2. Rechazo de descarga sin autorización
3. Manejo de archivo faltante
4. Verificación de nombre de archivo
5. Logging de descargas

### Notas de Implementación

- Los certificados se almacenan en colección "certificates" (único archivo)
- Nombre de descarga: `certificacion-{numero}.pdf`
- Formato PDF generado por VIEX
- Descargas ilimitadas para el propietario

---

## UC-DOC-012: Eliminar Trabajo

### Descripción
El profesor elimina un trabajo de extensión que está en estado "Borrador". Esta acción es destructiva e irreversible.

### Precondiciones
- El profesor debe estar autenticado
- Debe ser el responsable principal del trabajo
- El trabajo debe estar en estado "Borrador"
- No debe haber sido enviado para revisión

### Postcondiciones
- El trabajo se elimina completamente de la base de datos
- Todos los archivos adjuntos se eliminan
- Se registra la eliminación en logs
- No queda rastro del trabajo

### Flujo Principal

#### Paso 1: Verificación de estado
**Ubicación:** `app/Models/WorkOfExtension.php`

**Método:** `safeDelete()`

```php
public function safeDelete(): void {
    if (/srv/prcte/extrension/03_PROFESORES.mdthis->isInDraft()) {
        throw new \InvalidArgumentException("Solo se pueden eliminar trabajos en estado borrador.");
    }

    DB::transaction(function () {
        // Eliminar archivos
        if ($this->hasMedia("attachments")) {
            $this->clearMediaCollection("attachments");
        }

        // Eliminar detalles específicos
        $this->removeDetailRecords();

        // Eliminar historial de estados
        $this->statusHistory()->delete();

        // Eliminar participantes
        if ($this->participants()) {
            $this->participants()->delete();
        }

        // Eliminar trabajo principal
        $this->delete();
    });
}
```

#### Paso 2: Interfaz de eliminación
**Ubicación:** `resources/views/works/show.blade.php`

**Condición de visualización:**
```php
@can("delete", $work)
<form action="{{ route("works.destroy", $work) }}" method="POST" class="d-inline">
    @csrf
    @method("DELETE")
    <button type="submit" class="btn btn-danger btn-block mb-2"
        onclick="return confirm(\"¿Está seguro de eliminar este trabajo? Esta acción no se puede deshacer.\")">
        <i class="fas fa-trash"></i>
        Eliminar Trabajo
    </button>
</form>
@endcan
```

#### Paso 3: Procesamiento de eliminación
**Ubicación:** `app/Http/Controllers/WorkOfExtensionController.php`

**Método:** `destroy()`

```php
public function destroy(WorkOfExtension $work): RedirectResponse {
    $this->authorize("delete", $work);

    // Solo permitir eliminación si está en borrador
    if (/srv/prcte/extrension/03_PROFESORES.mdwork->isInDraft()) {
        return redirect()->route("works.show", $work)
            ->with("error", __("Solo se pueden eliminar trabajos en estado borrador."));
    }

    try {
        // Obtener información antes de eliminar
        $workTitle = $work->getAttribute("title");
        $workId = $work->getKey();

        // Lógica de eliminación delegada al modelo
        $work->safeDelete();

        Log::info("Trabajo eliminado exitosamente", [
            "work_id" => $workId,
            "work_title" => $workTitle,
            "user_id" => Auth::id()
        ]);

        return redirect()->route("works.index")
            ->with("success", __("Trabajo eliminado exitosamente."));

    } catch (\Exception $e) {
        return redirect()->route("works.show", $work)
            ->with("error", __("Error al eliminar el trabajo."));
    }
}
```

### Flujos Alternativos

#### A1: Trabajo ya enviado
1. Sistema verifica estado antes de eliminar
2. Muestra mensaje: "Solo se pueden eliminar trabajos en estado borrador"
3. Redirige a vista del trabajo

### Excepciones

#### E1: Error de eliminación
- **Condición:** Fallo en eliminación de archivos o registros
- **Acción:** Rollback completo
- **Mensaje:** Error genérico de eliminación

#### E2: Trabajo no encontrado
- **Condición:** Trabajo ya eliminado
- **Acción:** Redirección con mensaje

### Requisitos No Funcionales

#### Seguridad
- Solo trabajos en borrador pueden eliminarse
- Verificación de propiedad
- Eliminación completa de datos

#### Usabilidad
- Confirmación doble antes de eliminar
- Mensajes claros sobre irreversibilidad
- Redirección al listado después de eliminar

### Componentes Técnicos Utilizados

1. **Database Transactions**
   - Eliminación atómica
   - Rollback en caso de error

2. **Spatie MediaLibrary**
   - Eliminación de archivos adjuntos
   - Limpieza de colecciones

3. **Laravel Policies**
   - Autorización de eliminación
   - Control de acceso

### Testing

#### Casos de prueba sugeridos:
1. Eliminación exitosa de trabajo en borrador
2. Rechazo de eliminación de trabajo enviado
3. Verificación de eliminación de archivos
4. Verificación de eliminación de relaciones
5. Rollback en caso de error

### Notas de Implementación

- Eliminación completa: trabajo, archivos, historial, participantes, detalles
- Solo posible en estado "Borrador"
- Acción irreversible - no hay "papelera de reciclaje"
- Logging detallado de eliminaciones

---

*Estado: ✅ IMPLEMENTADO COMPLETAMENTE*
*Última revisión: $(date '+%Y-%m-%d')*
*Versión: 1.0*