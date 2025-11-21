# **03_PROFESORES.md - Documentación de Casos de Uso Implementados**

## **Rol: Profesores (Docentes)**

Esta documentación detalla la implementación de cada caso de uso para el rol de Profesores, basada en el código actual del sistema VIEX.

---

## **UC-DOC-001: Ver lista de mis trabajos de extensión (con filtros: estado, tipo, fecha)**

### **Descripción del Caso de Uso**
El profesor puede visualizar una lista completa de todos sus trabajos de extensión registrados en el sistema, con capacidad de aplicar filtros por estado del trabajo, tipo de trabajo y período académico para facilitar la navegación y búsqueda.

### **Estado de Implementación**
**Implementado** - Funcionalidad completa disponible.

### **Flujo de Proceso Implementado**

#### **1. Acceso al Dashboard de Trabajos**
- **Endpoint**: `GET /works`
- **Controlador**: `WorkOfExtensionController@index`
- **Vista**: `resources/views/works/index.blade.php`

**Código relevante:**
```php
// WorkOfExtensionController.php
public function index(Request $request): View {
    Log::info('Consultando trabajos de extensión', [
        'user_id' => $request->user()->getKey(),
        'filters' => $request->only(['status', 'work_type', 'academic_period', 'search'])
    ]);

    // Delegar lógica de listado al servicio
    $data = $this->workListingService->getWorksListing($request, $request->user());

    return view('works.index', $data);
}
```

#### **2. Aplicación de Filtros**
Los filtros se aplican automáticamente al cambiar las opciones en el formulario:

**Filtros disponibles:**
- **Estado** (alineado al flujo oficial):
    - `Borrador`
    - `Enviado al Coordinador`
    - `En Revisión del Coordinador`
    - `Devuelto para Corrección`
    - `En VIEX`
    - `Aprobado por VIEX`
    - `Certificado`
    - `Rechazado`
- **Tipo de Trabajo**: IDs de tipos de trabajo activos
- **Período Académico**: Valores como "2024-I", "2024-II"
- **Búsqueda**: Texto libre en título y descripción

**Código relevante:**
```php
// WorkListingService.php
private function applyFilters(\Illuminate\Database\Eloquent\Builder $query, Request $request): \Illuminate\Database\Eloquent\Builder
{
    // Filtro por estado
    if ($request->filled('status')) {
        $query = $this->applyStatusFilter($query, $request->input('status'));
    }

    // Filtro por tipo de trabajo
    if ($request->filled('work_type')) {
        $query->where('work_type_id', $request->input('work_type'));
    }

    // Filtro por período académico
    if ($request->filled('academic_period')) {
        $query->where('academic_period', $request->input('academic_period'));
    }

    // Búsqueda por texto
    if ($request->filled('search')) {
        $query = $this->applySearchFilter($query, $request->input('search'));
    }

    return $query;
}
```

#### **3. Visualización de Resultados**
La vista muestra:
- **Estadísticas generales**: Total, borradores, en revisión, certificados
- **Tabla de trabajos**: Título, tipo, estado, período, fecha creación, acciones
- **Paginación**: Si hay muchos resultados

### **Modelo de Datos**
- **WorkOfExtension**: Tabla principal con trabajos
- **WorkType**: Tipos de trabajo (Proyecto, Actividad, Publicación, Asistencia)
- **WorkStatus**: Estados del workflow
- **User**: Usuario propietario del trabajo

### **Reglas de Negocio Implementadas**
1. **Visibilidad**: Solo trabajos del usuario autenticado (`visibleToProfessor` scope)
2. **Filtros opcionales**: Todos los filtros son opcionales
3. **Ordenamiento**: Por fecha de creación descendente
4. **Estados**: Mapeo correcto de estados del workflow

### **Funcionalidades Implementadas**
✅ Listado completo de trabajos del profesor  
✅ Filtros por estado, tipo y período académico  
✅ Búsqueda por texto en título y descripción  
✅ Estadísticas resumidas  
✅ Paginación automática  
✅ Acciones contextuales por estado del trabajo  
✅ Diseño responsive para móviles  

### **Funcionalidades Pendientes**
❌ Filtros avanzados (rango de fechas, múltiples estados)  
❌ Exportación de resultados  
❌ Vista de tarjetas vs tabla  
❌ Ordenamiento personalizado por columnas  

### **Archivos de Código Relacionados**
- `app/Http/Controllers/WorkOfExtensionController.php` - Método index
- `app/Services/Dashboard/WorkListingService.php` - Lógica de filtros y queries
- `resources/views/works/index.blade.php` - Vista principal
- `app/Models/WorkOfExtension.php` - Modelo con scopes de visibilidad
- `routes/web.php` - Ruta GET /works

### **Permisos y Autorización**
- **Middleware**: `auth` (usuario autenticado)
- **Policy**: `WorkPolicy@view` (verificación de propiedad del trabajo)
- **Scope**: `visibleToProfessor` (filtrado automático en BD)

### **Interfaz de Usuario**
- **URL**: `/works`
- **Filtros**: Formulario colapsable con selects y input de búsqueda
- **Resultados**: Tabla responsive con badges de estado coloreados
- **Acciones**: Ver, editar, eliminar, enviar según estado del trabajo

### **Recomendaciones para Mejora**
1. Agregar filtros de rango de fechas
2. Implementar exportación a Excel/PDF
3. Añadir vista de tarjetas para trabajos
4. Mejorar filtros con autocompletado
5. Agregar ordenamiento por columnas

---

## **UC-DOC-007: Ver comentarios y retroalimentación de coordinadores/evaluadores**

### **Descripción del Caso de Uso**
El profesor puede visualizar los comentarios y retroalimentación proporcionados por los coordinadores de extensión y evaluadores de VIEX durante el proceso de revisión del trabajo de extensión. Esta funcionalidad permite al profesor entender las observaciones realizadas en cada etapa del workflow y tomar acciones correctivas cuando sea necesario.

### **Estado de Implementación**
**✅ IMPLEMENTADO COMPLETAMENTE** - Funcionalidad completa con interfaz dedicada y métodos optimizados.

### **Flujo de Proceso Implementado**

#### **1. Visualización de Comentarios en Estados de Rechazo**
Cuando un trabajo es rechazado o devuelto para corrección, el profesor ve automáticamente los comentarios en la vista de detalle del trabajo (`resources/views/works/show.blade.php`).

**Código relevante:**
```php
// En show.blade.php - Estados de rechazo
@if($currentStatus === 'Rechazado por Coordinador')
@php
$lastRejection = $work->statusHistory
    ->where('status.name', 'Rechazado por Coordinador')
    ->sortByDesc('created_at')
    ->first();
@endphp

<div class="alert alert-warning mb-3">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Trabajo rechazado por el Coordinador</strong><br>
    Debe realizar las correcciones solicitadas.

    @if($lastRejection && $lastRejection->comments)
    <hr class="my-2">
    <strong><i class="fas fa-comment-dots"></i> Motivo del rechazo:</strong>
    <p class="mb-2 mt-1">{{ $lastRejection->comments }}</p>
    <small class="text-muted">
        <i class="fas fa-user"></i> Por: {{ $lastRejection->changedBy->name ?? 'Sistema' }} ·
        <i class="fas fa-clock"></i> {{ $lastRejection->created_at->diffForHumans() }}
    </small>
    @endif
</div>
@endif
```

#### **2. Sección Dedicada de Comentarios y Retroalimentación**
Se agregó una nueva sección en la vista de detalle que muestra todos los comentarios históricos de forma organizada.

**Código relevante:**
```php
// Nueva sección en show.blade.php
@php
$comments = $work->getCommentsAndFeedback();
@endphp
@if($comments && $comments->count() > 0)
<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-comments"></i>
            Comentarios y Retroalimentación
            <span class="badge badge-light ml-2">{{ $comments->count() }}</span>
        </h3>
    </div>
    <div class="card-body">
        <div class="timeline timeline-inverse">
            @foreach($comments as $comment)
            <div class="time-label">
                <span class="bg-info">
                    {{ $comment->created_at->format('d M Y') }}
                </span>
            </div>
            <div>
                @php
                $commentIcon = match ($comment->status->name ?? '') {
                    'Rechazado por Coordinador', 'Rechazado por Decano/Director', 'Rechazado por VIEX' => 'fa-times-circle bg-danger',
                    'Devuelto para Corrección' => 'fa-exclamation-triangle bg-warning',
                    'Aprobado por Coordinador', 'Aprobado por Decano/Director' => 'fa-check-circle bg-success',
                    default => 'fa-comment bg-info'
                };
                @endphp
                <i class="fas {{ $commentIcon }}"></i>
                <div class="timeline-item">
                    <span class="time">
                        <i class="far fa-clock"></i>
                        {{ $comment->created_at->format('H:i') }}
                    </span>
                    <h3 class="timeline-header">
                        {{ $comment->status->name ?? 'Comentario' }}
                        @if($comment->changedBy)
                        <small class="text-muted">por {{ $comment->changedBy->name }}</small>
                        @endif
                    </h3>
                    <div class="timeline-body">
                        <div class="card border-left-primary">
                            <div class="card-body py-2">
                                <p class="mb-0">{{ $comment->comments }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
```

#### **3. Historial Completo de Comentarios (Timeline Mejorado)**
El profesor puede ver todo el historial de cambios de estado con sus respectivos comentarios en la sección "Historial de Estados", ahora mejorada con indicadores visuales.

**Código relevante:**
```php
// Timeline mejorado en show.blade.php
@if($timeline && $timeline->count() > 0)
<div class="timeline timeline-inverse">
    @foreach($timeline as $history)
    <div class="time-label">
        <span class="bg-primary">
            {{ $history->created_at->format('d M Y') }}
        </span>
    </div>
    <div>
        @php
        $iconClass = match ($history->status->name ?? '') {
        'Borrador' => 'fa-pencil-alt bg-secondary',
        'Enviado a Coordinador' => 'fa-paper-plane bg-warning',
        'En Revisión Coordinador' => 'fa-search bg-info',
        'Enviado a Decano' => 'fa-level-up-alt bg-primary',
        'En Revisión Decano' => 'fa-user-tie bg-primary',
        'Enviado a VIEX' => 'fa-university bg-dark',
        'En Evaluación VIEX' => 'fa-clipboard-check bg-dark',
        'Certificado' => 'fa-certificate bg-success',
        'Rechazado' => 'fa-times-circle bg-danger',
        'Subsanar' => 'fa-exclamation-triangle bg-orange',
        default => 'fa-circle bg-secondary'
        };
        @endphp
        <i class="fas {{ $iconClass }}"></i>
        <div class="timeline-item">
            <span class="time">
                <i class="far fa-clock"></i>
                {{ $history->created_at->format('H:i') }}
            </span>
            <h3 class="timeline-header">{{ $history->status->name ?? 'Estado Desconocido' }}</h3>
            <div class="timeline-body">
                @if($history->comments)
                <p>{{ $history->comments }}</p>
                @endif
                <small class="text-muted">
                    Por: {{ $history->changedBy->name ?? 'Sistema' }}
                </small>
            </div>
        </div>
    </div>
    @endforeach
    <div>
        <i class="far fa-clock bg-gray"></i>
    </div>
</div>
@endif
```

#### **4. Nuevos Métodos en el Modelo WorkOfExtension**
Se agregaron métodos especializados para obtener comentarios de forma eficiente.

**Código relevante:**
```php
// app/Models/WorkOfExtension.php

/**
 * Obtener comentarios y retroalimentación del trabajo
 * Filtra solo entradas del historial que tienen comentarios
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function getCommentsAndFeedback(): \Illuminate\Database\Eloquent\Collection
{
    return $this->statusHistory()
        ->with(['status', 'changedBy'])
        ->whereNotNull('comments')
        ->where('comments', '!=', '')
        ->orderBy('created_at', 'desc')
        ->get();
}

/**
 * Obtener último comentario de rechazo
 * Útil para mostrar en alertas
 *
 * @return WorkStatusHistory|null
 */
public function getLastRejectionComment(): ?WorkStatusHistory
{
    return $this->statusHistory()
        ->with(['status', 'changedBy'])
        ->whereNotNull('comments')
        ->where('comments', '!=', '')
        ->whereHas('status', function ($query) {
            $query->whereIn('name', [
                'Rechazado por Coordinador',
                'Rechazado por Decano/Director',
                'Rechazado por VIEX',
                'Devuelto para Corrección'
            ]);
        })
        ->orderBy('created_at', 'desc')
        ->first();
}
```

### **Modelo de Datos**
Los comentarios se almacenan en la tabla `work_status_history`:
- `comments` (text): Contiene el comentario del evaluador
- `changed_by` (foreign key): Usuario que realizó el cambio
- `created_at`: Fecha y hora del comentario

### **Funcionalidades Implementadas**
✅ Visualización de comentarios en rechazos (alertas contextuales)  
✅ Sección dedicada de comentarios y retroalimentación  
✅ Historial completo de comentarios en timeline mejorado  
✅ Métodos optimizados para obtener comentarios  
✅ Filtros automáticos de comentarios vacíos  
✅ Notificaciones automáticas con comentarios  
✅ Asociación de comentarios con usuario y fecha  
✅ Iconografía contextual por tipo de comentario  
✅ Contador de comentarios en la interfaz  

### **Funcionalidades Pendientes**
❌ Sistema de chat interno para comunicación bidireccional  
❌ Comentarios en evaluaciones positivas  
❌ Retroalimentación estructurada por criterios  
❌ Historial de conversaciones persistente  

### **Archivos de Código Relacionados**
- `resources/views/works/show.blade.php` - Vista principal con nueva sección de comentarios
- `app/Models/WorkOfExtension.php` - Nuevos métodos getCommentsAndFeedback() y getLastRejectionComment()
- `app/Events/WorkRejectedByCoordinator.php` - Evento de rechazo con comentarios
- `app/Listeners/SendWorkRejectedByCoordinatorNotification.php` - Notificación con comentarios
- `database/migrations/*_create_work_status_history_table.php` - Migración de tabla
- `tests/Feature/WorkCommentsTest.php` - Tests de funcionalidad

### **Permisos y Autorización**
- **Policy**: `WorkOfExtensionPolicy@view` (solo propietario puede ver comentarios)
- **Modelo**: Métodos seguros que filtran por trabajo del usuario
- **Vista**: Condicionales que verifican propiedad del trabajo

### **Interfaz de Usuario**
- **Sección Principal**: "Comentarios y Retroalimentación" con timeline dedicada
- **Alertas Contextuales**: Comentarios destacados en estados de rechazo
- **Timeline Mejorado**: Historial completo con iconografía contextual
- **Contadores**: Número de comentarios disponibles
- **Navegación**: Enlaces directos a secciones relevantes

### **Testing**
Se creó suite completa de tests en `tests/Feature/WorkCommentsTest.php`:

```php
// Tests implementados:
- test_professor_can_view_comments_in_work_detail()
- test_get_comments_and_feedback_filters_correctly()
- test_get_last_rejection_comment_returns_most_recent()
- test_only_owner_can_view_work_comments()
```

### **Recomendaciones para Mejora**
1. Implementar sistema de chat interno para comunicación bidireccional
2. Agregar comentarios obligatorios en todas las evaluaciones
3. Crear vista dedicada para ver toda la retroalimentación histórica
4. Implementar evaluaciones estructuradas con criterios específicos
5. Agregar filtros por tipo de comentario o fecha

---

*Estado: ✅ IMPLEMENTADO COMPLETAMENTE*
*Última revisión: $(date '+%Y-%m-%d')*
*Versión: 1.0*

---

## **UC-DOC-002: Crear un nuevo trabajo de extensión (proyecto, actividad, publicación, asistencia técnica)**

### **Descripción del Caso de Uso**
El profesor puede crear un nuevo trabajo de extensión seleccionando el tipo apropiado (proyecto, actividad, publicación o asistencia técnica) y completando toda la información requerida según el tipo seleccionado, incluyendo datos generales, detalles específicos y archivos adjuntos.

### **Estado de Implementación**
**Implementado** - Funcionalidad completa disponible.

### **Flujo de Proceso Implementado**

#### **1. Acceso al Formulario de Creación**
- **Endpoint**: `GET /works/create`
- **Controlador**: `WorkOfExtensionController@create`
- **Vista**: `resources/views/works/create.blade.php`

**Código relevante:**
```php
// WorkOfExtensionController.php
public function create(): View {
    $workTypes = WorkType::getActiveTypes();
    $organizationalUnits = OrganizationalUnit::getUnitsForSelection();
    $workTypesConfig = config('work_types');

    return view('works.create', [
        'workTypes' => $workTypes,
        'organizationalUnits' => $organizationalUnits,
        'workTypesConfig' => $workTypesConfig,
        'user' => Auth::user()
    ]);
}
```

#### **2. Procesamiento del Formulario**
- **Endpoint**: `POST /works`
- **Request**: `StoreCompleteWorkRequest`
- **Servicio**: `CreateWorkService`

**Código relevante:**
```php
// WorkOfExtensionController.php
public function store(StoreCompleteWorkRequest $request): RedirectResponse {
    $validated = $request->getValidatedData();
    $service = new CreateWorkService();
    $work = $service->execute($validated, $request->user());

    // Manejar archivos adjuntos
    if ($request->hasFile('attachments')) {
        $work->handleAttachments($request->file('attachments'));
    }

    return redirect()->route('works.show', $work)->with('success', 'Trabajo creado');
}
```

#### **3. Creación de Detalles Específicos**
El servicio crea automáticamente los detalles específicos según el tipo de trabajo:

**Código relevante:**
```php
// CreateWorkService.php
private function createSpecificDetails(WorkOfExtension $work, string $workType, array $specificData): void
{
    switch ($workType) {
        case '1': // Proyecto
            $work->projectDetail()->create([...]);
            break;
        case '2': // Actividad
            $work->activityDetail()->create([...]);
            break;
        case '3': // Publicación
            $work->publicationDetail()->create([...]);
            break;
        case '4': // Asistencia Técnica
            $work->technicalAssistanceDetail()->create([...]);
            break;
    }
}
```

### **Tipos de Trabajo Soportados**
1. **Proyecto**: Objetivos, metodología, beneficiarios, área geográfica
2. **Actividad**: Tipo, modalidad, duración, participantes, certificación
3. **Publicación**: Tipo, editorial, ISBN/ISSN, audiencia, idioma, tiraje
4. **Asistencia Técnica**: Tipo, institución colaboradora, área de especialización

### **Modelo de Datos**
- **WorkOfExtension**: Tabla principal con datos generales
- **ProjectDetail**: Detalles específicos para proyectos
- **ActivityDetail**: Detalles específicos para actividades
- **PublicationDetail**: Detalles específicos para publicaciones
- **TechnicalAssistanceDetail**: Detalles específicos para asistencias técnicas
- **WorkStatusHistory**: Historial inicial con estado "Borrador"

### **Validaciones Implementadas**
**StoreCompleteWorkRequest** incluye validaciones específicas:

```php
// Campos generales
'title' => 'required|string|min:10|max:500',
'description' => 'required|string|min:50|max:2000',
'start_date' => 'required|date',
'end_date' => 'required|date|after:start_date',

// Archivos
'attachments.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
```

**Validaciones específicas por tipo:**
- **Proyecto**: objectives, methodology (requeridos)
- **Actividad**: activity_type, modality (requeridos)
- **Publicación**: publication_type, relevance_justification (requeridos)
- **Asistencia Técnica**: assistance_type (requerido)

### **Funcionalidades Implementadas**
✅ Formulario dinámico según tipo de trabajo  
✅ Validaciones específicas por tipo  
✅ Creación automática de detalles específicos  
✅ Manejo de archivos adjuntos (MediaLibrary)  
✅ Historial de estados inicial  
✅ Transacciones de base de datos  
✅ Logging completo de operaciones  
✅ Mensajes de éxito/error  
✅ Redirección automática al detalle del trabajo  

### **Funcionalidades Pendientes**
❌ Previsualización antes de guardar  
❌ Guardado automático como borrador  
❌ Plantillas de trabajos comunes  
❌ Copia de trabajos existentes  
❌ Validación de conflictos de fechas  

### **Archivos de Código Relacionados**
- `app/Http/Controllers/WorkOfExtensionController.php` - Métodos create y store
- `app/Services/WorkOfExtension/CreateWorkService.php` - Lógica de negocio
- `app/Http/Requests/StoreCompleteWorkRequest.php` - Validaciones
- `resources/views/works/create.blade.php` - Formulario de creación
- `app/Models/WorkOfExtension.php` - Modelo principal
- `app/Models/ProjectDetail.php`, `ActivityDetail.php`, etc. - Modelos de detalles

### **Permisos y Autorización**
- **Middleware**: `auth` (usuario autenticado)
- **Policy**: `WorkPolicy@create` (permiso para crear trabajos)
- **Validación**: Usuario debe tener rol de profesor o superior

### **Interfaz de Usuario**
- **URL**: `/works/create`
- **Formulario**: Multi-sección con campos dinámicos según tipo
- **JavaScript**: Mostrar/ocultar campos según selección de tipo
- **Archivos**: Drag & drop para adjuntos múltiples
- **Validación**: Cliente y servidor con mensajes específicos

### **Recomendaciones para Mejora**
1. Implementar guardado automático como borrador cada 30 segundos
2. Agregar previsualización del trabajo antes de guardar
3. Crear plantillas reutilizables para tipos comunes
4. Mejorar UX con indicadores de progreso
5. Agregar validación de conflictos de fechas con otros trabajos

---

## **UC-DOC-003: Editar un trabajo en estado `borrador` o `requiere_corrección`**

### **Descripción del Caso de Uso**
El profesor puede modificar completamente la información de un trabajo de extensión que se encuentra en estado "borrador" o que ha sido devuelto para correcciones, incluyendo datos generales, detalles específicos según el tipo de trabajo, y archivos adjuntos, manteniendo la integridad de los datos y registrando el historial de cambios.

### **Estado de Implementación**
**Implementado** - Funcionalidad completa disponible.

### **Flujo de Proceso Implementado**

#### **1. Acceso al Formulario de Edición**
- **Endpoint**: `GET /works/{work}/edit`
- **Controlador**: `WorkOfExtensionController@edit`
- **Validación**: Solo trabajos en estado borrador o devueltos

**Código relevante:**
```php
// WorkOfExtensionController.php
public function edit(WorkOfExtension $work): View|RedirectResponse {
    $this->authorize('update', $work);

    // Solo permitir edición si está en borrador
    if (!$work->isInDraft()) {
        return redirect()
            ->route('works.show', $work)
            ->with('warning', __('Solo se pueden editar trabajos en estado borrador.'));
    }

    // Cargar datos específicos del tipo de trabajo
    $work->load(['projectDetail', 'activityDetail', 'publicationDetail', 'technicalAssistanceDetail']);

    return view('works.edit', [
        'work' => $work,
        'workTypes' => $workTypes,
        'organizationalUnits' => $organizationalUnits,
        'periods' => config('work_types.academic_periods'),
        'config' => config('work_types')
    ]);
}
```

#### **2. Procesamiento de la Actualización**
- **Endpoint**: `PUT /works/{work}`
- **Request**: `StoreCompleteWorkRequest`
- **Servicio**: `UpdateWorkService`

**Código relevante:**
```php
// WorkOfExtensionController.php
public function update(StoreCompleteWorkRequest $request, WorkOfExtension $work): RedirectResponse {
    $this->authorize('update', $work);

    // Validar que esté en borrador
    if (!$work->isInDraft()) {
        return redirect()
            ->route('works.show', $work)
            ->with('error', __('Solo se pueden actualizar trabajos en estado borrador.'));
    }

    $validatedData = $request->getValidatedData();
    $service = new UpdateWorkService();
    $updatedWork = $service->execute($work, $validatedData, $request->user());

    // Manejar archivos adjuntos
    if ($request->hasFile('attachments')) {
        $updatedWork->handleAttachments($request->file('attachments'));
    }

    return redirect()->route('works.show', $updatedWork)->with('success', 'Trabajo actualizado');
}
```

#### **3. Actualización de Detalles Específicos**
El servicio maneja la actualización de detalles específicos y limpieza de datos obsoletos:

**Código relevante:**
```php
// UpdateWorkService.php
private function updateSpecificDetails(WorkOfExtension $work, string $workType, array $specificData): void
{
    switch ($workType) {
        case '1': // Proyecto
            $work->projectDetail()->updateOrCreate([...]);
            break;
        case '2': // Actividad
            $work->activityDetail()->updateOrCreate([...]);
            break;
        case '3': // Publicación
            $work->publicationDetail()->updateOrCreate([...]);
            break;
        case '4': // Asistencia Técnica
            $work->technicalAssistanceDetail()->updateOrCreate([...]);
            break;
    }
}

// Limpieza de detalles obsoletos si cambia el tipo
private function removeDetailRecordsExcept(WorkOfExtension $work, string $currentType): void
{
    if ($currentType !== '1') $work->projectDetail()->delete();
    if ($currentType !== '2') $work->activityDetail()->delete();
    if ($currentType !== '3') $work->publicationDetail()->delete();
    if ($currentType !== '4') $work->technicalAssistanceDetail()->delete();
}
```

### **Estados Permitidos para Edición**
- **Borrador**: Trabajo recién creado, no enviado
- **Devuelto para Corrección**: Trabajo rechazado que requiere modificaciones

### **Funcionalidades Implementadas**
✅ Validación de estado antes de edición  
✅ Formulario pre-poblado con datos existentes  
✅ Actualización de datos generales y específicos  
✅ Cambio de tipo de trabajo con limpieza automática  
✅ Manejo de archivos adjuntos (agregar/eliminar)  
✅ Historial de cambios registrado  
✅ Transacciones de base de datos  
✅ Logging completo de operaciones  
✅ Mensajes de éxito/error  
✅ Redirección automática al detalle  

### **Funcionalidades Pendientes**
❌ Edición de trabajos en otros estados  
❌ Versionado de cambios  
❌ Comparación de versiones  
❌ Aprobación de cambios mayores  
❌ Notificaciones de cambios  

### **Validaciones Específicas**
- **Estado del trabajo**: Solo borrador o devuelto
- **Permisos**: Usuario debe ser propietario o tener permisos de edición
- **Archivos**: Validación de tipos y tamaños (igual que creación)
- **Fechas**: Validación de coherencia temporal
- **Datos específicos**: Según tipo de trabajo (igual que creación)

### **Manejo de Archivos Adjuntos**
```php
// Eliminación de archivos existentes
if ($request->filled('remove_media')) {
    $mediaToRemove = array_filter(explode(',', $request->input('remove_media')));
    foreach ($mediaToRemove as $mediaId) {
        $media = $work->getMedia('attachments')->where('id', $mediaId)->first();
        if ($media) {
            $media->delete();
        }
    }
}

// Agregar nuevos archivos
if ($request->hasFile('attachments')) {
    $updatedWork->handleAttachments($request->file('attachments'));
}
```

### **Historial de Cambios**
Cada actualización crea una entrada en `work_status_history`:
```php
private function createUpdateHistory(WorkOfExtension $work, User $user): void
{
    $work->statusHistory()->create([
        'from_status_id' => $work->getAttribute('current_status_id'),
        'to_status_id' => $work->getAttribute('current_status_id'), // No cambia estado
        'changed_by_user_id' => $user->getKey(),
        'comments' => 'Trabajo actualizado por el usuario (edición completa)',
    ]);
}
```

### **Archivos de Código Relacionados**
- `app/Http/Controllers/WorkOfExtensionController.php` - Métodos edit y update
- `app/Services/WorkOfExtension/UpdateWorkService.php` - Lógica de negocio
- `app/Http/Requests/StoreCompleteWorkRequest.php` - Validaciones (reutilizado)
- `resources/views/works/edit.blade.php` - Formulario de edición
- `app/Models/WorkOfExtension.php` - Modelo con métodos de estado
- `app/Policies/WorkPolicy.php` - Autorización de edición

### **Permisos y Autorización**
- **Middleware**: `auth` (usuario autenticado)
- **Policy**: `WorkPolicy@update` (permiso para editar)
- **Estado**: Método `isInDraft()` en modelo
- **Propiedad**: Usuario debe ser el responsable principal

### **Interfaz de Usuario**
- **URL**: `/works/{work}/edit`
- **Formulario**: Similar al de creación pero pre-poblado
- **Campos dinámicos**: Según tipo de trabajo seleccionado
- **Archivos**: Gestión de adjuntos existentes + nuevos
- **Validación**: Cliente y servidor con mensajes específicos
- **Navegación**: Breadcrumb completo con retorno al detalle

### **Recomendaciones para Mejora**
1. Implementar versionado de cambios para auditoría
2. Agregar previsualización de cambios antes de guardar
3. Permitir edición de trabajos en estados adicionales (con aprobación)
4. Mejorar UX con indicadores de cambios realizados
5. Agregar validación de impacto en workflow al cambiar tipo

---

## **UC-DOC-005: Enviar trabajo a revisión (cambia estado a `enviado`)**

### **Descripción del Caso de Uso**
El profesor puede enviar un trabajo de extensión que se encuentra en estado "borrador" para revisión por parte del coordinador de extensión, cambiando su estado a "enviado" y notificando automáticamente al coordinador correspondiente, con validaciones exhaustivas de completitud de datos.

### **Estado de Implementación**
**Implementado** - Funcionalidad completa disponible.

### **Flujo de Proceso Implementado**

#### **1. Validación y Envío del Trabajo**
- **Endpoint**: `POST /works/{work}/submit`
- **Controlador**: `WorkOfExtensionController@submit`
- **Servicio**: `SubmitWorkService`

**Código relevante:**
```php
// WorkOfExtensionController.php
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

#### **2. Validaciones Exhaustivas**
El servicio valida campos obligatorios y completitud antes del envío:

**Código relevante:**
```php
// SubmitWorkService.php
private function validateWorkCanBeSubmitted(WorkOfExtension $work): void
{
    // Verificar que esté en borrador
    if (!$work->isInDraft()) {
        throw new \InvalidArgumentException(__('El trabajo ya ha sido enviado anteriormente.'));
    }

    // Validar campos básicos obligatorios
    if (!$this->validateBasicFields($work)) {
        $missingFields = $this->getMissingBasicFields($work);
        $message = __('El trabajo no puede ser enviado. Faltan los siguientes campos obligatorios: ') .
            implode(', ', $missingFields);
        throw new \InvalidArgumentException($message);
    }

    // Validar detalles específicos según tipo
    if (!$this->validateSpecificDetails($work)) {
        $missingFields = $this->getMissingSpecificFields($work);
        $message = __('El trabajo no puede ser enviado. Faltan los siguientes campos específicos: ') .
            implode(', ', $missingFields);
        throw new \InvalidArgumentException($message);
    }
}
```

#### **3. Transición de Estado y Notificación**
Cambio de estado con registro en historial y notificación automática:

**Código relevante:**
```php
// SubmitWorkService.php
DB::transaction(function () use ($work, $submittedStatus, $user, $isResubmission) {
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
```

### **Estados Involucrados**
- **Estado inicial**: Borrador (`is_draft = true`)
- **Estado final**: Enviado a Coordinador
- **Transición**: Irreversible hasta revisión del coordinador

### **Validaciones Implementadas**
**Campos básicos obligatorios:**
- Título del trabajo
- Tipo de trabajo
- Descripción
- Unidad organizacional
- Fecha de inicio y finalización
- Período académico

**Campos específicos por tipo:**
- **Proyecto**: Objetivos, Metodología
- **Actividad**: Tipo de actividad, Modalidad
- **Publicación**: Tipo de publicación
- **Asistencia Técnica**: Tipo de asistencia, Institución colaboradora

### **Funcionalidades Implementadas**
✅ Validación exhaustiva de campos obligatorios  
✅ Validación específica según tipo de trabajo  
✅ Transición de estado segura con transacciones  
✅ Registro completo en historial de estados  
✅ Timestamp de envío (`submitted_at`)  
✅ Notificación automática al coordinador  
✅ Mensajes de error específicos y descriptivos  
✅ Logging completo de operaciones  
✅ Soporte para reenvíos después de correcciones  
✅ Interfaz de confirmación en UI  

### **Funcionalidades Pendientes**
❌ Validación de conflictos de fechas  
❌ Previsualización antes de envío  
❌ Envío masivo de múltiples trabajos  
❌ Recordatorios automáticos de envío  
❌ Estadísticas de envío por período  

### **Notificaciones Automáticas**
**Evento**: `WorkSubmitted`  
**Listener**: `SendWorkSubmittedNotification`  
**Notificación**: `WorkSubmittedForReview`

**Código relevante:**
```php
// SendWorkSubmittedNotification.php
public function handle(WorkSubmitted $event): void {
    $work = $event->work;
    $submittedBy = $event->submittedBy;
    $isResubmission = $event->isResubmission;

    // Encontrar coordinador y enviar notificación
    $coordinator = $this->findCoordinator($work);
    
    if ($coordinator) {
        $coordinator->notify(new WorkSubmittedForReview($work, $isResubmission));
    }
}
```

### **Archivos de Código Relacionados**
- `app/Http/Controllers/WorkOfExtensionController.php` - Método submit
- `app/Services/WorkOfExtension/SubmitWorkService.php` - Lógica de negocio completa
- `app/Events/WorkSubmitted.php` - Evento de envío
- `app/Listeners/SendWorkSubmittedNotification.php` - Listener de notificaciones
- `app/Notifications/WorkSubmittedForReview.php` - Notificación por email/database
- `resources/views/works/show.blade.php` - Botón de envío
- `resources/views/works/index.blade.php` - Botón de envío en listado

### **Permisos y Autorización**
- **Middleware**: `auth` (usuario autenticado)
- **Policy**: `WorkOfExtensionPolicy@update` (permiso para modificar trabajo)
- **Estado**: Método `isInDraft()` en modelo
- **Propiedad**: Usuario debe ser el responsable principal

### **Interfaz de Usuario**
- **Ubicación**: Botones "Enviar para Revisión" en vista detalle y listado
- **Confirmación**: JavaScript confirm() con mensaje explicativo
- **Feedback**: Mensajes flash de éxito/error
- **Estados**: Botón solo visible para trabajos en borrador
- **Navegación**: Redirección automática a vista de detalle

### **Recomendaciones para Mejora**
1. Implementar validación de conflictos de fechas con otros trabajos
2. Agregar previsualización del trabajo antes de envío
3. Permitir envío masivo de múltiples trabajos
4. Mejorar UX con indicadores de progreso de envío
5. Agregar métricas de tiempo promedio de envío

---

## **UC-DOC-004: Eliminar un trabajo en estado `borrador`**

### **Descripción del Caso de Uso**
El profesor puede eliminar permanentemente un trabajo de extensión que se encuentra en estado "borrador", eliminando todos los datos relacionados incluyendo archivos adjuntos, detalles específicos y historial de estados, manteniendo la integridad del sistema y registrando la operación.

### **Estado de Implementación**
**Implementado** - Funcionalidad completa disponible.

### **Flujo de Proceso Implementado**

#### **1. Confirmación de Eliminación**
- **Endpoint**: `DELETE /works/{work}`
- **Controlador**: `WorkOfExtensionController@destroy`
- **Validación**: Solo trabajos en estado borrador, propiedad del usuario

**Código relevante:**
```php
// WorkOfExtensionController.php
public function destroy(WorkOfExtension $work): RedirectResponse {
    // Verificar autorización
    $this->authorize('delete', $work);

    // Solo permitir eliminación si está en borrador
    if (!$work->isInDraft()) {
        return redirect()
            ->route('works.show', $work)
            ->with('error', __('Solo se pueden eliminar trabajos en estado borrador.'));
    }

    try {
        // Lógica de eliminación delegada al modelo
        $work->safeDelete();

        return redirect()
            ->route('works.index')
            ->with('success', __('Trabajo eliminado exitosamente.'));

    } catch (\Exception $e) {
        return redirect()
            ->route('works.show', $work)
            ->with('error', __('Error al eliminar el trabajo. Por favor, inténtalo de nuevo.'));
    }
}
```

#### **2. Eliminación Segura**
El método `safeDelete()` maneja la eliminación completa y segura:

**Código relevante:**
```php
// WorkOfExtension.php
public function safeDelete(): void {
    if (!$this->isInDraft()) {
        throw new \InvalidArgumentException('Solo se pueden eliminar trabajos en estado borrador.');
    }

    DB::transaction(function () {
        // Eliminar archivos relacionados
        if ($this->hasMedia('attachments')) {
            $this->clearMediaCollection('attachments');
        }

        // Eliminar detalles específicos según el tipo
        switch ($this->getAttribute('work_type_id')) {
            case 1: // Proyecto
                if ($this->projectDetail) $this->projectDetail->delete();
                break;
            case 2: // Actividad
                if ($this->activityDetail) $this->activityDetail->delete();
                break;
            case 3: // Publicación
                if ($this->publicationDetail) $this->publicationDetail->delete();
                break;
            case 4: // Asistencia Técnica
                if ($this->technicalAssistanceDetail) $this->technicalAssistanceDetail->delete();
                break;
        }

        // Eliminar registros relacionados
        $this->statusHistory()->delete();
        if ($this->participants()) {
            $this->participants()->delete();
        }

        // Eliminar el trabajo principal
        $this->delete();
    });
}
```

### **Estados Permitidos para Eliminación**
- **Borrador**: Trabajo recién creado, no enviado para revisión

### **Funcionalidades Implementadas**
✅ Validación de estado antes de eliminación  
✅ Verificación de propiedad del trabajo  
✅ Eliminación completa de datos relacionados  
✅ Transacciones de base de datos  
✅ Logging completo de operaciones  
✅ Mensajes de éxito/error  
✅ Redirección automática a listado  
✅ Eliminación de archivos adjuntos (MediaLibrary)  
✅ Limpieza de detalles específicos por tipo  

### **Funcionalidades Pendientes**
❌ Eliminación de trabajos en otros estados  
❌ Eliminación suave (soft delete) con recuperación  
❌ Confirmación adicional para trabajos con muchos datos  
❌ Notificaciones de eliminación  
❌ Backup automático antes de eliminar  

### **Validaciones Específicas**
- **Estado del trabajo**: Solo borrador (`isInDraft()`)
- **Permisos**: Usuario debe ser propietario (`primary_responsible_user_id`)
- **Rol**: Usuario debe tener rol de profesor
- **Transacción**: Toda la eliminación ocurre en una transacción

### **Entidades Afectadas**
- **WorkOfExtension**: Registro principal eliminado
- **ProjectDetail/ActivityDetail/etc.**: Detalles específicos eliminados
- **WorkStatusHistory**: Historial de estados eliminado
- **Media (Spatie)**: Archivos adjuntos eliminados
- **WorkParticipant**: Participantes eliminados (si existen)

### **Archivos de Código Relacionados**
- `app/Http/Controllers/WorkOfExtensionController.php` - Método destroy
- `app/Models/WorkOfExtension.php` - Método safeDelete
- `app/Policies/WorkOfExtensionPolicy.php` - Método delete
- `resources/views/works/index.blade.php` - Botón de eliminación
- `resources/views/works/show.blade.php` - Botón de eliminación
- `routes/web.php` - Ruta DELETE (resource route)

### **Permisos y Autorización**
- **Middleware**: `auth` (usuario autenticado)
- **Policy**: `WorkOfExtensionPolicy@delete` (permiso para eliminar)
- **Estado**: Método `isInDraft()` en modelo
- **Propiedad**: Usuario debe ser el responsable principal

### **Interfaz de Usuario**
- **Ubicación**: Botones en listado y vista de detalle
- **Acción**: Formulario POST con método DELETE (Laravel resource)
- **Confirmación**: JavaScript confirm() nativo
- **Feedback**: Mensajes flash de éxito/error
- **Redirección**: Vuelta al listado de trabajos

### **Recomendaciones para Mejora**
1. Implementar eliminación suave con período de recuperación
2. Agregar diálogo de confirmación más detallado
3. Crear backup automático antes de eliminar
4. Permitir eliminación de trabajos rechazados (con restricciones)
5. Mejorar logging con más detalles de auditoría

---

## **UC-DOC-005: Enviar trabajo a revisión (cambia estado a `enviado`)**

### **Descripción del Caso de Uso**
El profesor puede enviar un trabajo de extensión que se encuentra en estado "borrador" para revisión por parte del coordinador de extensión, cambiando su estado a "enviado" y notificando automáticamente al coordinador correspondiente, con validaciones exhaustivas de completitud de datos.

### **Estado de Implementación**
**Implementado** - Funcionalidad completa disponible.

### **Flujo de Proceso Implementado**

#### **1. Validación y Envío del Trabajo**
- **Endpoint**: `POST /works/{work}/submit`
- **Controlador**: `WorkOfExtensionController@submit`
- **Servicio**: `SubmitWorkService`

**Código relevante:**
```php
// WorkOfExtensionController.php
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

#### **2. Validaciones Exhaustivas**
El servicio valida campos obligatorios y completitud antes del envío:

**Código relevante:**
```php
// SubmitWorkService.php
private function validateWorkCanBeSubmitted(WorkOfExtension $work): void
{
    // Verificar que esté en borrador
    if (!$work->isInDraft()) {
        throw new \InvalidArgumentException(__('El trabajo ya ha sido enviado anteriormente.'));
    }

    // Validar campos básicos obligatorios
    if (!$this->validateBasicFields($work)) {
        $missingFields = $this->getMissingBasicFields($work);
        $message = __('El trabajo no puede ser enviado. Faltan los siguientes campos obligatorios: ') .
            implode(', ', $missingFields);
        throw new \InvalidArgumentException($message);
    }

    // Validar detalles específicos según tipo
    if (!$this->validateSpecificDetails($work)) {
        $missingFields = $this->getMissingSpecificFields($work);
        $message = __('El trabajo no puede ser enviado. Faltan los siguientes campos específicos: ') .
            implode(', ', $missingFields);
        throw new \InvalidArgumentException($message);
    }
}
```

#### **3. Transición de Estado y Notificación**
Cambio de estado con registro en historial y notificación automática:

**Código relevante:**
```php
// SubmitWorkService.php
DB::transaction(function () use ($work, $submittedStatus, $user, $isResubmission) {
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
```

### **Estados Involucrados**
- **Estado inicial**: Borrador (`is_draft = true`)
- **Estado final**: Enviado a Coordinador (`is_draft = false`, `submitted_at` timestamp)

### **Validaciones Implementadas**
**Campos básicos obligatorios:**
- Título del trabajo
- Tipo de trabajo
- Descripción
- Unidad organizacional
- Fecha de inicio y finalización
- Período académico

**Campos específicos por tipo:**
- **Proyecto**: Objetivos, Metodología
- **Actividad**: Tipo de actividad, Modalidad
- **Publicación**: Tipo de publicación
- **Asistencia Técnica**: Tipo de asistencia, Institución colaboradora

### **Funcionalidades Implementadas**
✅ Validación exhaustiva de campos obligatorios  
✅ Validación específica según tipo de trabajo  
✅ Transición de estado segura (transacción DB)  
✅ Registro completo en historial de estados  
✅ Timestamp de envío automático  
✅ Notificación automática al coordinador  
✅ Mensajes de error específicos y descriptivos  
✅ Logging completo de operaciones  
✅ Prevención de envío duplicado  
✅ Confirmación JavaScript en interfaz  

### **Funcionalidades Pendientes**
❌ Validación de adjuntos obligatorios  
❌ Validación de fechas (conflicto con otros trabajos)  
❌ Validación de presupuesto (si aplica)  
❌ Notificación por email adicional  
❌ Recordatorio automático de revisión pendiente  

### **Notificaciones Automáticas**
**Evento**: `WorkSubmitted`  
**Listener**: `SendWorkSubmittedNotification`  
**Notificación**: `WorkSubmittedForReview`

**Flujo de notificación:**
1. Busca coordinador de extensión de la unidad organizacional
2. Envía notificación por email y base de datos
3. Fallback a super_admin si no hay coordinador
4. Registra trazabilidad en historial

### **Archivos de Código Relacionados**
- `app/Http/Controllers/WorkOfExtensionController.php` - Método submit
- `app/Services/WorkOfExtension/SubmitWorkService.php` - Lógica de negocio completa
- `app/Events/WorkSubmitted.php` - Evento de envío
- `app/Listeners/SendWorkSubmittedNotification.php` - Listener de notificaciones
- `app/Notifications/WorkSubmittedForReview.php` - Notificación al coordinador
- `resources/views/works/show.blade.php` - Botón de envío y confirmación
- `routes/web.php` - Ruta POST /works/{work}/submit

### **Permisos y Autorización**
- **Middleware**: `auth` (usuario autenticado)
- **Policy**: `WorkOfExtensionPolicy@update` (permiso para modificar trabajo)
- **Estado**: Método `isInDraft()` en modelo
- **Propiedad**: Usuario debe ser el responsable principal
- **Rol**: Usuario debe tener rol de profesor

### **Interfaz de Usuario**
- **Ubicación**: Vista de detalle del trabajo (`works.show`)
- **Botón**: "Enviar a Revisión" (solo visible en estado borrador)
- **Confirmación**: JavaScript `confirmSubmitWork()` con mensaje descriptivo
- **Feedback**: Mensajes flash de éxito/error
- **Estados**: Botón oculto cuando trabajo ya enviado

### **Recomendaciones para Mejora**
1. Agregar validación de adjuntos obligatorios según tipo de trabajo
2. Implementar validación de conflictos de fechas
3. Mejorar UX con indicadores de progreso de completitud
4. Agregar previsualización antes del envío
5. Implementar notificaciones push adicionales</content>
<parameter name="filePath">/srv/prcte/extrension/docs/03_PROFESORES.md

---
