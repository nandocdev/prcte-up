# 📋 Auditoría CU9: Evaluar y Aprobar Trabajo - VIEX

**Fecha de Auditoría:** 8 de octubre de 2025  
**Auditor:** GitHub Copilot  
**Caso de Uso:** CU9 - Evaluar y Aprobar Trabajo (Personal VIEX)  
**Estado General:** ❌ **IMPLEMENTACIÓN INCOMPLETA** (35% completo)

---

## 📊 Resumen Ejecutivo

### Estado General: ❌ **35% Implementado - Falta Infraestructura Crítica**

| Componente | Estado | Cobertura | Observaciones |
|-----------|--------|-----------|---------------|
| **Modelo (Métodos Básicos)** | ⚠️ Parcial | 30% | Solo 2 métodos simples, faltan transiciones de estado |
| **Controlador VIEX** | ❌ No existe | 0% | **No hay ViexController** |
| **Sistema de Evaluadores** | ❌ No existe | 0% | **Faltan modelos, migraciones, relaciones** |
| **Transiciones de Estado** | ❌ No existe | 0% | **Faltan métodos críticos** |
| **Vistas** | ❌ No existen | 0% | **No hay vistas para VIEX** |
| **Rutas** | ❌ No existen | 0% | **No hay rutas definidas** |
| **Notificaciones** | ❌ No existen | 0% | **Sistema completo faltante** |
| **Validaciones** | ❌ Mínimas | 10% | Solo en métodos básicos |
| **Permisos/Policies** | ❌ No existe | 0% | **No hay WorkOfExtensionPolicy para VIEX** |

---

## 🎯 Funcionalidad Requerida (Según CU9)

### **CU9: Evaluar y Aprobar Trabajo - VIEX**

**Actor Principal:** Personal VIEX (Evaluador VIEX)  
**Precondiciones:**
- Personal VIEX ha iniciado sesión
- Tiene trabajos en estado **"Enviado a VIEX"**, **"En VIEX - Pendiente Asignación"** o **"En VIEX - En Evaluación"**

**Flujo Completo (11 Pasos):**

1. ✅ Personal VIEX accede a bandeja de trabajos pendientes → **FALTA Dashboard**
2. ✅ Selecciona trabajo para evaluación → **FALTA Vista de detalle**
3. ❌ Si está en "Enviado a VIEX" → cambia a **"En VIEX - Pendiente Asignación"** → **FALTA Método**
4. ✅ Sistema muestra detalles del trabajo → **FALTA Vista**
5. ❌ Personal VIEX asigna evaluadores → **FALTA Sistema completo de evaluadores**
6. ❌ Sistema cambia a **"En VIEX - En Evaluación"** → **FALTA Método**
7. ❌ Evaluadores completan evaluación con criterios → **FALTA Sistema completo**
8. ❌ Personal VIEX revisa evaluaciones → **FALTA Vista de revisión**
9. ⚠️ Si es positiva, aprueba → **EXISTE método pero con estado incorrecto**
10. ⚠️ Cambia a **"En VIEX - Aprobado"** → **EXISTE pero va directo a "Certificado"**
11. ❌ Notifica al profesor → **FALTA Sistema de notificaciones**

---

## 🔍 Hallazgos Detallados

### ⚠️ **1. Modelo: WorkOfExtension.php (Lógica de Negocio)**

#### 1.1 Método `approveByViex()` - ⚠️ IMPLEMENTACIÓN INCORRECTA

**Ubicación:** `/app/Models/WorkOfExtension.php` líneas 1241-1260

```php
public function approveByViex(User $evaluator, ?string $comments = null, ?string $recommendations = null): void
```

**Problemas Identificados:**

❌ **Estado de entrada incorrecto:**
```php
if ($this->statusIsNot('En Evaluación VIEX')) {
    // Busca "En Evaluación VIEX" pero el estado real es "En VIEX - En Evaluación"
}
```

❌ **Estado de salida incorrecto:**
```php
$status = WorkStatus::where('name', 'Certificado')->firstOrFail();
// Va directo a "Certificado" pero debería ir a "En VIEX - Aprobado"
```

❌ **No dispara evento** para notificaciones

**Comportamiento Actual (INCORRECTO):**
```
"En Evaluación VIEX" → "Certificado"
```

**Comportamiento Esperado:**
```
"En VIEX - En Evaluación" → "En VIEX - Aprobado"
```

---

#### 1.2 Método `rejectByViex()` - ⚠️ IMPLEMENTACIÓN INCORRECTA

**Ubicación:** `/app/Models/WorkOfExtension.php` líneas 1269-1293

```php
public function rejectByViex(User $evaluator, string $reason, ?string $recommendations = null): void
```

**Problemas Identificados:**

❌ **Estado de entrada incorrecto:**
```php
if ($this->statusIsNot('En Evaluación VIEX')) {
    // Busca "En Evaluación VIEX" pero el estado real es "En VIEX - En Evaluación"
}
```

✅ **Estado de salida correcto:**
```php
$status = WorkStatus::where('name', 'Rechazado por VIEX')->firstOrFail();
// ✅ Este estado sí existe y es correcto
```

❌ **No establece `is_draft = '1'`** (el profesor no puede editar)

❌ **No dispara evento** para notificaciones

---

### ❌ **2. Métodos Faltantes en el Modelo**

Los siguientes métodos **NO EXISTEN** y son **CRÍTICOS** para el CU9:

#### 2.1 `receiveFromDeanDirector()` - ❌ NO EXISTE

**Propósito:** Recibir trabajo desde el Decano y cambiar a "En VIEX - Pendiente Asignación"

**Transición:**
```
"Enviado a VIEX" → "En VIEX - Pendiente Asignación"
```

---

#### 2.2 `assignEvaluator()` - ❌ NO EXISTE

**Propósito:** Asignar un evaluador al trabajo

**Requiere:**
- Modelo `WorkEvaluator`
- Relación `work_evaluators`
- Tabla `work_evaluators`

---

#### 2.3 `startViexEvaluation()` - ❌ NO EXISTE

**Propósito:** Iniciar evaluación formal, cambiar a "En VIEX - En Evaluación"

**Transición:**
```
"En VIEX - Pendiente Asignación" → "En VIEX - En Evaluación"
```

---

#### 2.4 `submitEvaluation()` - ❌ NO EXISTE

**Propósito:** Que un evaluador registre su evaluación

**Requiere:**
- Modelo `WorkEvaluation`
- Modelo `EvaluationDetail`
- Tabla `work_evaluations`
- Tabla `evaluation_details`
- Tabla `evaluation_criteria`

---

### ❌ **3. Controlador VIEX - NO EXISTE**

**Archivo esperado:** `/app/Http/Controllers/ViexController.php`

**Estado:** ❌ **NO EXISTE**

**Métodos necesarios:**

```php
class ViexController extends Controller
{
    // Dashboard
    public function dashboard(Request $request): View;
    
    // Ver trabajo
    public function show(Request $request, WorkOfExtension $work): View;
    
    // Recibir trabajo (Enviado a VIEX → En VIEX - Pendiente Asignación)
    public function receive(Request $request, WorkOfExtension $work): RedirectResponse;
    
    // Asignar evaluadores
    public function assignEvaluators(Request $request, WorkOfExtension $work): RedirectResponse;
    
    // Iniciar evaluación (En VIEX - Pendiente Asignación → En VIEX - En Evaluación)
    public function startEvaluation(Request $request, WorkOfExtension $work): RedirectResponse;
    
    // Ver evaluaciones del trabajo
    public function viewEvaluations(Request $request, WorkOfExtension $work): View;
    
    // Aprobar trabajo (En VIEX - En Evaluación → En VIEX - Aprobado)
    public function approve(Request $request, WorkOfExtension $work): RedirectResponse;
    
    // Rechazar trabajo (En VIEX - En Evaluación → Rechazado por VIEX)
    public function reject(Request $request, WorkOfExtension $work): RedirectResponse;
    
    // Helpers
    private function getPendingWorksForViex();
    private function getViexStatistics();
    private function canViexReviewWork(User $user, WorkOfExtension $work): bool;
}
```

**Prioridad:** 🔴 **CRÍTICA**

---

### ❌ **4. Sistema de Evaluadores - NO EXISTE**

#### 4.1 Modelos Faltantes

❌ **`WorkEvaluator`** - Relación trabajo-evaluador
❌ **`EvaluationCriteria`** - Criterios de evaluación
❌ **`WorkEvaluation`** - Evaluación del trabajo
❌ **`EvaluationDetail`** - Detalle por criterio

---

#### 4.2 Migraciones Faltantes

❌ **`create_work_evaluators_table`**
```php
Schema::create('work_evaluators', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_of_extension_id');
    $table->foreignId('evaluator_user_id');
    $table->string('role')->default('evaluator'); // 'lead_evaluator', 'evaluator'
    $table->timestamp('assigned_at');
    $table->timestamp('completed_at')->nullable();
    $table->timestamps();
});
```

❌ **`create_evaluation_criteria_table`**
```php
Schema::create('evaluation_criteria', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description');
    $table->integer('max_score')->default(10);
    $table->integer('weight')->default(1);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

❌ **`create_work_evaluations_table`**
```php
Schema::create('work_evaluations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_of_extension_id');
    $table->foreignId('evaluator_user_id');
    $table->text('general_comments')->nullable();
    $table->text('recommendations')->nullable();
    $table->enum('final_decision', ['approve', 'reject', 'pending'])->default('pending');
    $table->timestamp('submitted_at')->nullable();
    $table->timestamps();
});
```

❌ **`create_evaluation_details_table`**
```php
Schema::create('evaluation_details', function (Blueprint $table) {
    $table->id();
    $table->foreignId('work_evaluation_id');
    $table->foreignId('evaluation_criteria_id');
    $table->integer('score');
    $table->text('comments')->nullable();
    $table->timestamps();
});
```

---

#### 4.3 Relaciones Faltantes en Modelos

En `WorkOfExtension.php`:

```php
// ❌ FALTAN ESTAS RELACIONES
public function evaluators()
{
    return $this->belongsToMany(User::class, 'work_evaluators', 'work_of_extension_id', 'evaluator_user_id')
        ->withPivot('role', 'assigned_at', 'completed_at')
        ->withTimestamps();
}

public function workEvaluators()
{
    return $this->hasMany(WorkEvaluator::class);
}

public function evaluations()
{
    return $this->hasMany(WorkEvaluation::class);
}
```

En `User.php`:

```php
// ❌ FALTAN ESTAS RELACIONES
public function worksAsEvaluator()
{
    return $this->belongsToMany(WorkOfExtension::class, 'work_evaluators', 'evaluator_user_id', 'work_of_extension_id')
        ->withPivot('role', 'assigned_at', 'completed_at')
        ->withTimestamps();
}

public function evaluations()
{
    return $this->hasMany(WorkEvaluation::class, 'evaluator_user_id');
}
```

---

### ❌ **5. Vistas VIEX - NO EXISTEN**

**Directorio esperado:** `/resources/views/viex/`

**Vistas necesarias:**

❌ `/resources/views/viex/dashboard.blade.php` - Dashboard con trabajos pendientes
❌ `/resources/views/viex/show.blade.php` - Detalle del trabajo
❌ `/resources/views/viex/assign-evaluators.blade.php` - Asignar evaluadores
❌ `/resources/views/viex/evaluations.blade.php` - Ver evaluaciones del trabajo
❌ `/resources/views/viex/evaluate.blade.php` - Formulario de evaluación para evaluador

---

### ❌ **6. Rutas VIEX - NO EXISTEN**

**Archivo:** `/routes/web.php`

**Rutas necesarias:**

```php
// ❌ FALTAN ESTAS RUTAS
Route::middleware(['auth', 'role:viex_admin|super_admin'])->prefix('viex')->name('viex.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ViexController::class, 'dashboard'])->name('dashboard');
    
    // Ver trabajo
    Route::get('/works/{work}', [ViexController::class, 'show'])->name('works.show');
    
    // Recibir trabajo
    Route::post('/works/{work}/receive', [ViexController::class, 'receive'])->name('works.receive');
    
    // Asignar evaluadores
    Route::post('/works/{work}/assign-evaluators', [ViexController::class, 'assignEvaluators'])->name('works.assign-evaluators');
    
    // Iniciar evaluación
    Route::post('/works/{work}/start-evaluation', [ViexController::class, 'startEvaluation'])->name('works.start-evaluation');
    
    // Ver evaluaciones
    Route::get('/works/{work}/evaluations', [ViexController::class, 'viewEvaluations'])->name('works.evaluations');
    
    // Aprobar
    Route::post('/works/{work}/approve', [ViexController::class, 'approve'])->name('works.approve');
    
    // Rechazar
    Route::post('/works/{work}/reject', [ViexController::class, 'reject'])->name('works.reject');
});

// Rutas para evaluadores
Route::middleware(['auth', 'role:evaluador_viex|viex_admin|super_admin'])->prefix('evaluator')->name('evaluator.')->group(function () {
    // Mis trabajos asignados
    Route::get('/assigned-works', [EvaluatorController::class, 'index'])->name('works.index');
    
    // Ver trabajo a evaluar
    Route::get('/works/{work}', [EvaluatorController::class, 'show'])->name('works.show');
    
    // Registrar evaluación
    Route::post('/works/{work}/evaluate', [EvaluatorController::class, 'evaluate'])->name('works.evaluate');
});
```

---

### ❌ **7. Sistema de Notificaciones VIEX - NO EXISTE**

#### 7.1 Events Faltantes

❌ `/app/Events/WorkReceivedByViex.php`
❌ `/app/Events/EvaluatorAssignedToWork.php`
❌ `/app/Events/WorkEvaluationStarted.php`
❌ `/app/Events/WorkApprovedByViex.php`
❌ `/app/Events/WorkRejectedByViex.php`

---

#### 7.2 Listeners Faltantes

❌ `/app/Listeners/SendWorkReceivedByViexNotification.php`
❌ `/app/Listeners/SendEvaluatorAssignedNotification.php`
❌ `/app/Listeners/SendWorkEvaluationStartedNotification.php`
❌ `/app/Listeners/SendWorkApprovedByViexNotification.php`
❌ `/app/Listeners/SendWorkRejectedByViexNotification.php`

---

#### 7.3 Notifications Faltantes

❌ `/app/Notifications/WorkReceivedByViex.php`
❌ `/app/Notifications/EvaluatorAssignedToWork.php`
❌ `/app/Notifications/WorkEvaluationStarted.php`
❌ `/app/Notifications/WorkApprovedByViex.php`
❌ `/app/Notifications/WorkRejectedByViex.php`

---

## 📋 Tabla de Cumplimiento por Requisito

| # | Requisito del CU9 | Implementado | Evidencia | Observaciones |
|---|-------------------|--------------|-----------|---------------|
| 1 | Dashboard VIEX con trabajos pendientes | ❌ No | N/A | **NO hay controlador** |
| 2 | Ver detalle de trabajo | ❌ No | N/A | **NO hay vista** |
| 3 | Recibir trabajo → "En VIEX - Pendiente Asignación" | ❌ No | N/A | **NO hay método** |
| 4 | Asignar evaluadores al trabajo | ❌ No | N/A | **NO hay sistema de evaluadores** |
| 5 | Iniciar evaluación → "En VIEX - En Evaluación" | ❌ No | N/A | **NO hay método** |
| 6 | Evaluadores completan evaluación | ❌ No | N/A | **NO hay infraestructura** |
| 7 | Ver evaluaciones del trabajo | ❌ No | N/A | **NO hay vista** |
| 8 | Aprobar → "En VIEX - Aprobado" | ⚠️ Parcial | `approveByViex()` | ⚠️ Estado incorrecto |
| 9 | Rechazar → "Rechazado por VIEX" | ⚠️ Parcial | `rejectByViex()` | ⚠️ Falta `is_draft` y notificaciones |
| 10 | Registrar en historial | ✅ Sí | `changeStatus()` | ✅ Funciona |
| 11 | Notificar a profesor al aprobar | ❌ No | N/A | **NO hay eventos** |
| 12 | Notificar a profesor al rechazar | ❌ No | N/A | **NO hay eventos** |
| 13 | Notificar a evaluadores asignados | ❌ No | N/A | **NO hay eventos** |
| 14 | Autorización por rol | ❌ No | N/A | **NO hay validaciones** |
| 15 | Logging estructurado | ⚠️ Parcial | Modelo | Solo en 2 métodos |

**Resumen de Cumplimiento:**
- ✅ **Implementado:** 1/15 (7%)
- ⚠️ **Parcialmente:** 4/15 (27%)
- ❌ **Faltante:** 10/15 (66%)

---

## 🚨 Problemas Críticos Identificados

### 1. ❌ **CRÍTICO: No existe ViexController**

**Impacto:** 🔴 **BLOQUEANTE**

**Descripción:**
- No hay forma de que el personal VIEX acceda al sistema
- No hay dashboard, no hay vistas, no hay acciones
- El CU9 está **completamente inoperativo**

**Prioridad:** 🔴 **CRÍTICA - BLOQUEANTE**

---

### 2. ❌ **CRÍTICO: Sistema de Evaluadores No Existe**

**Impacto:** 🔴 **BLOQUEANTE**

**Descripción:**
- No hay forma de asignar evaluadores a un trabajo
- No hay forma de que los evaluadores registren sus evaluaciones
- No hay criterios de evaluación
- No hay tablas, modelos ni relaciones

**Componentes Faltantes:**
- 4 migraciones de tablas
- 4 modelos (WorkEvaluator, EvaluationCriteria, WorkEvaluation, EvaluationDetail)
- Relaciones en WorkOfExtension y User
- Seeder de criterios de evaluación

**Prioridad:** 🔴 **CRÍTICA - BLOQUEANTE**

---

### 3. ❌ **CRÍTICO: Transiciones de Estado Faltantes**

**Impacto:** 🔴 **ALTO**

**Descripción:**
Los trabajos no pueden fluir correctamente por VIEX:

```
✅ "Enviado a VIEX" (llega del Decano)
  ↓
❌ ??? (no hay método para recibir)
  ↓
❌ "En VIEX - Pendiente Asignación" (no se puede alcanzar)
  ↓
❌ ??? (no hay método para iniciar evaluación)
  ↓
❌ "En VIEX - En Evaluación" (no se puede alcanzar)
  ↓
⚠️ "En VIEX - Aprobado" (método existe pero con bug)
```

**Métodos Faltantes:**
- `receiveFromDeanDirector()`
- `assignEvaluator()`
- `startViexEvaluation()`

**Prioridad:** 🔴 **CRÍTICA**

---

### 4. ⚠️ **Estados Incorrectos en Métodos Existentes**

**Impacto:** 🔴 **ALTO**

**Descripción:**
Los 2 métodos que existen tienen estados incorrectos:

```php
// approveByViex() - INCORRECTO
if ($this->statusIsNot('En Evaluación VIEX')) {
    // ❌ El estado real es "En VIEX - En Evaluación"
}

$status = WorkStatus::where('name', 'Certificado')->firstOrFail();
// ❌ Debería ser "En VIEX - Aprobado", no "Certificado"

// rejectByViex() - INCORRECTO
if ($this->statusIsNot('En Evaluación VIEX')) {
    // ❌ El estado real es "En VIEX - En Evaluación"
}

// ❌ Falta: 'is_draft' => '1'
```

**Prioridad:** 🔴 **ALTA**

---

### 5. ❌ **CRÍTICO: No hay Vistas ni Rutas**

**Impacto:** 🔴 **BLOQUEANTE**

**Descripción:**
- Aunque se implemente el controlador, no hay vistas
- No hay rutas definidas para VIEX
- No hay navegación

**Prioridad:** 🔴 **CRÍTICA**

---

## 📝 Plan de Implementación Completa del CU9

### 🔴 **Fase 1: Infraestructura de Base de Datos (PRIORIDAD MÁXIMA)**

#### Tarea 1.1: Crear Migraciones

**Archivos a crear:**

1. `2025_10_08_create_evaluation_criteria_table.php`
2. `2025_10_08_create_work_evaluators_table.php`
3. `2025_10_08_create_work_evaluations_table.php`
4. `2025_10_08_create_evaluation_details_table.php`

**Estimación:** 1 hora

---

#### Tarea 1.2: Crear Modelos

**Archivos a crear:**

1. `/app/Models/EvaluationCriteria.php`
2. `/app/Models/WorkEvaluator.php`
3. `/app/Models/WorkEvaluation.php`
4. `/app/Models/EvaluationDetail.php`

**Estimación:** 1 hora

---

#### Tarea 1.3: Agregar Relaciones

**Archivos a modificar:**

1. `/app/Models/WorkOfExtension.php` - Agregar relaciones `evaluators()`, `workEvaluators()`, `evaluations()`
2. `/app/Models/User.php` - Agregar relaciones `worksAsEvaluator()`, `evaluations()`

**Estimación:** 30 minutos

---

#### Tarea 1.4: Crear Seeder de Criterios

**Archivo a crear:**

`/database/seeders/EvaluationCriteriaSeeder.php`

**Criterios sugeridos:**
- Relevancia del trabajo
- Metodología aplicada
- Impacto social/académico
- Calidad de evidencias
- Alineación con objetivos institucionales

**Estimación:** 30 minutos

---

### 🔴 **Fase 2: Lógica de Negocio en el Modelo (CRÍTICA)**

#### Tarea 2.1: Corregir Métodos Existentes

**Archivo:** `/app/Models/WorkOfExtension.php`

**Cambios:**

```php
// 1. Corregir approveByViex()
public function approveByViex(User $evaluator, ?string $comments = null, ?string $recommendations = null): void
{
    // Cambiar estado de entrada
    if ($this->statusIsNot('En VIEX - En Evaluación')) {
        throw new \InvalidArgumentException('El trabajo debe estar "En VIEX - En Evaluación" para poder ser aprobado.');
    }

    // Cambiar estado de salida
    $status = WorkStatus::where('name', 'En VIEX - Aprobado')->firstOrFail();
    
    // ... resto del código
    
    // Disparar evento
    WorkApprovedByViex::dispatch($this, $evaluator, $comments, $recommendations);
}

// 2. Corregir rejectByViex()
public function rejectByViex(User $evaluator, string $reason, ?string $recommendations = null): void
{
    // Cambiar estado de entrada
    if ($this->statusIsNot('En VIEX - En Evaluación')) {
        throw new \InvalidArgumentException('El trabajo debe estar "En VIEX - En Evaluación" para poder ser rechazado.');
    }

    $status = WorkStatus::where('name', 'Rechazado por VIEX')->firstOrFail();
    
    // Agregar is_draft
    $this->update([
        'current_status_id' => $status->getKey(),
        'is_draft' => '1', // ✅ Permitir edición
    ]);
    
    // ... resto del código
    
    // Disparar evento
    WorkRejectedByViex::dispatch($this, $evaluator, $reason, $recommendations);
}
```

**Estimación:** 30 minutos

---

#### Tarea 2.2: Crear Métodos de Transición

**Archivo:** `/app/Models/WorkOfExtension.php`

**Métodos a crear:**

```php
// 1. Recibir trabajo en VIEX
public function receiveInViex(User $viexUser, ?string $comments = null): void

// 2. Asignar evaluador
public function assignEvaluator(User $evaluator, User $assignedBy, string $role = 'evaluator'): WorkEvaluator

// 3. Iniciar evaluación VIEX
public function startViexEvaluation(User $viexUser, ?string $comments = null): void

// 4. Registrar evaluación de un evaluador
public function submitEvaluation(User $evaluator, array $evaluationData): WorkEvaluation

// 5. Verificar si todas las evaluaciones están completas
public function allEvaluationsCompleted(): bool

// 6. Obtener resumen de evaluaciones
public function getEvaluationSummary(): array
```

**Estimación:** 3 horas

---

### 🔴 **Fase 3: Controladores (CRÍTICA)**

#### Tarea 3.1: Crear ViexController

**Archivo:** `/app/Http/Controllers/ViexController.php`

**Métodos:**
- `dashboard()` - Dashboard con trabajos pendientes
- `show()` - Ver detalle del trabajo
- `receive()` - Recibir trabajo
- `assignEvaluators()` - Asignar evaluadores
- `startEvaluation()` - Iniciar evaluación
- `viewEvaluations()` - Ver evaluaciones
- `approve()` - Aprobar trabajo
- `reject()` - Rechazar trabajo

**Estimación:** 4 horas

---

#### Tarea 3.2: Crear EvaluatorController

**Archivo:** `/app/Http/Controllers/EvaluatorController.php`

**Métodos:**
- `index()` - Mis trabajos asignados
- `show()` - Ver trabajo a evaluar
- `evaluate()` - Registrar evaluación

**Estimación:** 2 horas

---

### 🟡 **Fase 4: Vistas (MEDIA)**

#### Tarea 4.1: Vistas VIEX

**Archivos a crear:**

1. `/resources/views/viex/dashboard.blade.php`
2. `/resources/views/viex/show.blade.php`
3. `/resources/views/viex/assign-evaluators.blade.php`
4. `/resources/views/viex/evaluations.blade.php`

**Estimación:** 4 horas

---

#### Tarea 4.2: Vistas Evaluador

**Archivos a crear:**

1. `/resources/views/evaluator/index.blade.php`
2. `/resources/views/evaluator/show.blade.php`
3. `/resources/views/evaluator/evaluate.blade.php`

**Estimación:** 3 horas

---

### 🟡 **Fase 5: Rutas (MEDIA)**

**Archivo:** `/routes/web.php`

Agregar:
- Rutas de VIEX (8 rutas)
- Rutas de Evaluador (3 rutas)

**Estimación:** 30 minutos

---

### 🟡 **Fase 6: Sistema de Notificaciones (MEDIA)**

#### Tarea 6.1: Events

**Archivos a crear:**

1. `/app/Events/WorkReceivedByViex.php`
2. `/app/Events/EvaluatorAssignedToWork.php`
3. `/app/Events/WorkEvaluationStarted.php`
4. `/app/Events/WorkApprovedByViex.php`
5. `/app/Events/WorkRejectedByViex.php`

**Estimación:** 1.5 horas

---

#### Tarea 6.2: Listeners

**Archivos a crear:**

1. `/app/Listeners/SendWorkReceivedByViexNotification.php`
2. `/app/Listeners/SendEvaluatorAssignedNotification.php`
3. `/app/Listeners/SendWorkEvaluationStartedNotification.php`
4. `/app/Listeners/SendWorkApprovedByViexNotification.php`
5. `/app/Listeners/SendWorkRejectedByViexNotification.php`

**Estimación:** 1.5 horas

---

#### Tarea 6.3: Notifications

**Archivos a crear:**

1. `/app/Notifications/WorkReceivedByViex.php`
2. `/app/Notifications/EvaluatorAssignedToWork.php`
3. `/app/Notifications/WorkEvaluationStarted.php`
4. `/app/Notifications/WorkApprovedByViex.php`
5. `/app/Notifications/WorkRejectedByViex.php`

**Estimación:** 1.5 horas

---

#### Tarea 6.4: Registrar en EventServiceProvider

**Archivo:** `/app/Providers/EventServiceProvider.php`

Agregar mappings de los 5 eventos.

**Estimación:** 15 minutos

---

### 🟢 **Fase 7: Validaciones y Permisos (BAJA)**

#### Tarea 7.1: Form Requests

**Archivos a crear:**

1. `/app/Http/Requests/AssignEvaluatorsRequest.php`
2. `/app/Http/Requests/SubmitEvaluationRequest.php`
3. `/app/Http/Requests/ApproveByViexRequest.php`
4. `/app/Http/Requests/RejectByViexRequest.php`

**Estimación:** 1 hora

---

#### Tarea 7.2: Actualizar WorkOfExtensionPolicy

**Archivo:** `/app/Policies/WorkOfExtensionPolicy.php`

Agregar métodos:
- `receiveInViex()`
- `assignEvaluators()`
- `startViexEvaluation()`
- `approveByViex()`
- `rejectByViex()`
- `evaluate()` (para evaluadores)

**Estimación:** 1 hora

---

### 🟢 **Fase 8: Tests (BAJA)**

**Archivos a crear:**

1. `/tests/Feature/ViexWorkflowTest.php`
2. `/tests/Feature/EvaluatorWorkflowTest.php`
3. `/tests/Unit/WorkEvaluationTest.php`

**Estimación:** 4 horas

---

## 📊 Resumen del Plan de Implementación

| Fase | Tareas | Estimación | Prioridad |
|------|--------|------------|-----------|
| **Fase 1: Base de Datos** | 4 tareas | 3 horas | 🔴 CRÍTICA |
| **Fase 2: Modelo** | 2 tareas | 3.5 horas | 🔴 CRÍTICA |
| **Fase 3: Controladores** | 2 tareas | 6 horas | 🔴 CRÍTICA |
| **Fase 4: Vistas** | 2 tareas | 7 horas | 🟡 MEDIA |
| **Fase 5: Rutas** | 1 tarea | 0.5 horas | 🟡 MEDIA |
| **Fase 6: Notificaciones** | 4 tareas | 4.5 horas | 🟡 MEDIA |
| **Fase 7: Validaciones** | 2 tareas | 2 horas | 🟢 BAJA |
| **Fase 8: Tests** | 3 tareas | 4 horas | 🟢 BAJA |
| **TOTAL** | **20 tareas** | **~30.5 horas** | |

---

## 🎯 Conclusión

### Estado del CU9: ❌ **IMPLEMENTACIÓN INCOMPLETA**

**Situación Actual:**
- El CU9 está en un estado **crítico de incompletitud**
- Solo existen 2 métodos básicos con errores
- **No hay infraestructura** para la funcionalidad principal (evaluadores)
- **No hay interfaz de usuario** (controlador, vistas, rutas)
- **No hay notificaciones**

**Funcionalidad Crítica Faltante:**
1. ❌ Sistema completo de evaluadores (modelos, migraciones, relaciones)
2. ❌ ViexController y EvaluatorController
3. ❌ Métodos de transición de estado
4. ❌ Vistas para VIEX y evaluadores
5. ❌ Sistema de notificaciones completo

**Impacto:**
- 🔴 **El flujo VIEX está completamente bloqueado**
- 🔴 Los trabajos no pueden ser evaluados
- 🔴 No hay forma de aprobar trabajos para certificación

**Recomendación:**
La implementación del CU9 requiere **~30 horas de desarrollo** dividido en 8 fases. Se recomienda:

1. **URGENTE:** Implementar Fases 1-3 (infraestructura crítica) - ~12.5 horas
2. **IMPORTANTE:** Implementar Fases 4-5 (interfaz de usuario) - ~7.5 horas
3. **NECESARIO:** Implementar Fase 6 (notificaciones) - ~4.5 horas
4. **DESEABLE:** Implementar Fases 7-8 (calidad) - ~6 horas

**Siguiente Paso:** Iniciar implementación inmediata de la Fase 1 (Base de Datos) para desbloquear el flujo.

---

## 📚 Referencias

- **Caso de Uso:** `/doc/sega/00_CU.md` - CU9
- **Modelo:** `/app/Models/WorkOfExtension.php` líneas 1241-1293
- **Análisis Previo:** `/doc/sega/ANALISIS_DIAGRAMA_FLUJO.md`
- **Seeder de Estados:** `/database/seeders/WorkStatusSeeder.php`
- **Manual de Procedimientos:** `/doc/tecnica/Manual_Procedimientos.md`

---

**Fin de la Auditoría CU9**
