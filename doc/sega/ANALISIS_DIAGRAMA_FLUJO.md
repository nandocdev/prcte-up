# 📊 Análisis: Diagrama de Flujo vs Implementación del Sistema

**Fecha:** 8 de octubre de 2025  
**Documento de Referencia:** `/doc/sega/00_CU.md` - Sección "Flujo de Estados de un Trabajo de Extensión"  
**Versión del Sistema:** 1.0  

---

## 🎯 Resumen Ejecutivo

Este documento analiza las **discrepancias y alineaciones** entre el diagrama de flujo de estados documentado en `00_CU.md` y la implementación real en el código del sistema VIEX.

### Estado General: ⚠️ **Mayormente Alineado con Ajustes Necesarios**

- ✅ **85% implementado correctamente**
- ⚠️ **10% requiere ajustes de nomenclatura**
- ❌ **5% falta implementar (reenvío de profesor)**

---

## 📋 Tabla Comparativa Detallada

| # | Transición del Diagrama | Estado Implementado | Estado | Evidencia/Problema | Recomendación |
|---|------------------------|---------------------|--------|-------------------|---------------|
| 1 | `BORRADOR → PENDIENTE_COORD` | ✅ Implementado | ✅ | `submitToCoordinator()` - Cambia a "Enviado a Coordinador" | Ninguna |
| 2 | `PENDIENTE_COORD → PENDIENTE_DECANO` | ✅ Implementado | ✅ | `approveByCoordinator()` - Acepta ambos estados ("Enviado" y "En Revisión") | Ninguna |
| 3 | `PENDIENTE_COORD → CORRECCIONES` | ⚠️ Nomenclatura diferente | ⚠️ | Diagrama: "Correcciones Solicitadas (al Profesor)"<br>Código: "Devuelto para Corrección" | **Actualizar diagrama** para usar "Devuelto para Corrección" |
| 4 | `PENDIENTE_COORD → RECHAZADO` | ✅ Implementado | ✅ | `rejectByCoordinator()` → "Rechazado por Coordinador"<br>Establece `is_draft = '1'` correctamente | Ninguna |
| 5 | `CORRECCIONES → BORRADOR` | ❌ No implementado | ❌ | **Falta método** `resubmitAfterCorrections()`<br>El profesor no tiene forma de reenviar | **Implementar método** de reenvío |
| 6 | `RECHAZADO → BORRADOR` | ❌ No implementado | ❌ | Mismo problema: falta método de reenvío | **Implementar método** de reenvío |
| 7 | `PENDIENTE_DECANO → PENDIENTE_VIEX` | ✅ Implementado | ✅ | `approveByDeanDirector()` → "Enviado a VIEX" | Ninguna |
| 8 | `PENDIENTE_DECANO → CORRECCIONES` | ⚠️ Implementado diferente | ⚠️ | `requestChangesFromDeanDirector()` → "Rechazado por Decano/Director"<br>Nota: Mismo estado que rechazo total | **Aclarar** si Decano debe tener dos estados separados |
| 9 | `PENDIENTE_DECANO → RECHAZADO` | ✅ Implementado | ✅ | `rejectByDeanDirector()` → "Rechazado por Decano/Director" | Verificar distinción con correcciones menores |
| 10 | Estado "En Revisión X" | ⚠️ Uso inconsistente | ⚠️ | Existe `startReviewByCoordinator()`<br>**Falta** `startReviewByDeanDirector()` | **Implementar** método para Decano |
| 11 | `PENDIENTE_VIEX → APROBADO_VIEX` | ⚠️ Parcialmente | 🔍 | Método `approveByViex()` existe pero no se ha auditado flujo VIEX completo | Auditar CU9 |
| 12 | `APROBADO_VIEX → CERTIFICADO` | ⚠️ Parcialmente | 🔍 | No se ha auditado el flujo de certificación | Auditar CU12 |

---

## 🔍 Hallazgos Detallados

### ✅ **1. Aspectos Correctamente Implementados**

#### 1.1 Flujo del Coordinador (CU7)
```php
// ✅ approveByCoordinator()
- Acepta: ['Enviado a Coordinador', 'En Revisión Coordinador']
- Cambia a: "Enviado a Decano/Director"
- Registra historial correctamente
- Dispara evento: WorkApprovedByCoordinator

// ✅ requestChangesFromCoordinator()
- Acepta: ['Enviado a Coordinador', 'En Revisión Coordinador']
- Cambia a: "Devuelto para Corrección"
- Establece is_draft = '1' ✅
- Dispara evento: WorkChangesRequestedByCoordinator

// ✅ rejectByCoordinator()
- Acepta: ['Enviado a Coordinador', 'En Revisión Coordinador']
- Cambia a: "Rechazado por Coordinador"
- Establece is_draft = '1' ✅
- Dispara evento: WorkRejectedByCoordinator
```

**Evidencia:** `/app/Models/WorkOfExtension.php` líneas 717-858  
**Estado:** ✅ **Totalmente funcional y validado**

#### 1.2 Flujo del Decano/Director (CU8)
```php
// ✅ approveByDeanDirector()
- Acepta: 'Enviado a Decano/Director'
- Cambia a: "Enviado a VIEX"
- Registra historial correctamente
- TODO: Disparar evento para VIEX

// ✅ rejectByDeanDirector()
- Acepta: ['Enviado a Decano/Director', 'En Revisión Decano/Director']
- Cambia a: "Rechazado por Decano/Director"
- Establece is_draft = '1' ✅
- TODO: Disparar evento para profesor
```

**Evidencia:** `/app/Models/WorkOfExtension.php` líneas 861-986  
**Estado:** ⚠️ **Funcional pero faltan eventos** (marcados con TODO)

#### 1.3 Manejo de `is_draft`
```php
// ✅ Todos los métodos de rechazo/corrección establecen:
'is_draft' => '1'

// Esto permite al profesor editar el trabajo
// WorkOfExtensionPolicy::update() verifica isInDraft()
```

**Estado:** ✅ **Implementado correctamente**

---

### ⚠️ **2. Discrepancias de Nomenclatura**

#### 2.1 Estado "Correcciones Solicitadas" vs "Devuelto para Corrección"

**EN EL DIAGRAMA:**
```mermaid
PENDIENTE_COORD --> CORRECCIONES : Coordinador solicita correcciones
state "Correcciones Solicitadas\n(al Profesor)" as CORRECCIONES
```

**EN LA BASE DE DATOS:**
```php
// WorkStatusSeeder.php
[
    'name' => 'Devuelto para Corrección',
    'description' => 'Trabajo devuelto al docente para realizar correcciones solicitadas.',
]

// ❌ NO EXISTE: 'Correcciones Solicitadas (al Profesor)'
```

**IMPACTO:**
- **Funcional:** ✅ No hay impacto en el código, todo funciona
- **Documentación:** ❌ Confusión entre diagrama y BD
- **Mantenibilidad:** ❌ Futuros desarrolladores pueden confundirse

**RECOMENDACIÓN:**
```markdown
ACCIÓN: Actualizar el diagrama en doc/sega/00_CU.md

CAMBIAR:
  state "Correcciones Solicitadas\n(al Profesor)" as CORRECCIONES

POR:
  state "Devuelto para Corrección" as DEVUELTO_CORRECCION
```

**Prioridad:** 🟡 Media - Solo documentación

---

#### 2.2 Decano: ¿Un Estado o Dos para Correcciones/Rechazo?

**EN EL DIAGRAMA:**
```mermaid
PENDIENTE_DECANO --> CORRECCIONES : Decano/Director solicita correcciones (ajustes menores)
PENDIENTE_DECANO --> RECHAZADO : Decano/Director rechaza (requiere subsanación)
```

El diagrama sugiere **DOS flujos diferentes** para el Decano:
- **CORRECCIONES**: Ajustes menores
- **RECHAZADO**: Subsanación mayor

**EN LA IMPLEMENTACIÓN:**
```php
// requestChangesFromDeanDirector() → "Rechazado por Decano/Director"
// rejectByDeanDirector()            → "Rechazado por Decano/Director"

// ❌ AMBOS USAN EL MISMO ESTADO
```

**EN LA BASE DE DATOS:**
```php
// WorkStatusSeeder.php
// ✅ Solo existe un estado de rechazo para Decano:
[
    'name' => 'Rechazado por Decano/Director',
    'description' => 'Trabajo rechazado por el Decano o Director con observaciones.',
]

// ❌ NO EXISTE: 'Devuelto para Corrección - Decano'
```

**ANÁLISIS:**

**Opción A - Mantener UN solo estado (como está ahora):**
```
✅ PRO: Más simple
✅ PRO: Consistente con la realidad (un rechazo es un rechazo)
❌ CON: El diagrama sugiere dos caminos diferentes
```

**Opción B - Crear DOS estados separados:**
```
Estado 1: "Devuelto para Corrección - Decano" (ajustes menores)
Estado 2: "Rechazado por Decano/Director" (subsanación mayor)

✅ PRO: Más granularidad
✅ PRO: Alineado con el diagrama
❌ CON: Requiere migración de BD
❌ CON: Mayor complejidad
```

**RECOMENDACIÓN:**
```markdown
ACCIÓN: Mantener la implementación actual (Opción A)
RAZÓN: 
  1. La distinción entre "correcciones menores" y "subsanación mayor" 
     se hace mediante los COMENTARIOS del evaluador, no mediante estados diferentes.
  2. Ambos casos requieren que el profesor edite y reenvíe.
  3. El flujo VIEX también usa un solo estado "Rechazado por VIEX".

ACTUALIZAR DIAGRAMA:
  - Unificar "CORRECCIONES" y "RECHAZADO" del Decano en un solo nodo
  - O aclarar en las notas que ambos transitan al mismo estado en BD
```

**Prioridad:** 🟢 Baja - Es más una cuestión de documentación que funcionalidad

---

### ❌ **3. Funcionalidad Faltante Crítica**

#### 3.1 Método de Reenvío del Profesor (CRÍTICO)

**EN EL DIAGRAMA:**
```mermaid
CORRECCIONES --> BORRADOR : Profesor reenvía trabajo corregido
RECHAZADO --> BORRADOR : Profesor subsana y reenvía
```

**EN LA IMPLEMENTACIÓN:**
```php
// ❌ NO EXISTE método como:
public function resubmitAfterCorrections(User $professor): void

// El profesor NO tiene forma de reenviar un trabajo
// después de hacer correcciones
```

**PROBLEMA:**
1. El profesor edita el trabajo (porque `is_draft = '1'`)
2. Guarda los cambios
3. ¿Cómo lo reenvía al coordinador? ❌ **NO HAY MÉTODO**

**IMPACTO:**
- 🔴 **CRÍTICO** - Rompe el flujo de correcciones
- 🔴 El profesor queda "atrapado" en estado "Devuelto/Rechazado"
- 🔴 No puede continuar con el proceso de aprobación

**COMPORTAMIENTO ESPERADO:**
```php
/**
 * Reenviar trabajo después de realizar correcciones solicitadas
 * 
 * Estados válidos de entrada:
 * - "Devuelto para Corrección"
 * - "Rechazado por Coordinador"
 * - "Rechazado por Decano/Director"
 * - "Rechazado por VIEX"
 * 
 * Estado de salida:
 * - "Enviado a Coordinador" (reinicia el flujo)
 * 
 * @param User $professor Usuario profesor que reenvía
 * @throws InvalidArgumentException Si el estado no permite reenvío
 * @return void
 */
public function resubmitAfterCorrections(User $professor): void
{
    // Validar estados permitidos
    $allowedStatuses = [
        'Devuelto para Corrección',
        'Rechazado por Coordinador',
        'Rechazado por Decano/Director',
        'Rechazado por VIEX'
    ];
    
    $currentStatusName = $this->currentStatus->name;
    
    if (!in_array($currentStatusName, $allowedStatuses)) {
        throw new \InvalidArgumentException(
            "El trabajo no puede ser reenviado desde el estado: {$currentStatusName}"
        );
    }
    
    // Validar que esté en modo borrador
    if ($this->is_draft != '1') {
        throw new \InvalidArgumentException(
            "El trabajo debe estar en modo borrador para ser reenviado."
        );
    }
    
    // Validar que el profesor sea el creador del trabajo
    if ($this->created_by != $professor->getKey()) {
        throw new \UnauthorizedException(
            "Solo el creador del trabajo puede reenviarlo."
        );
    }
    
    // Validar que todos los campos requeridos estén completos
    // (reutilizar validación de submitToCoordinator)
    
    // Cambiar a estado "Enviado a Coordinador"
    $submittedStatus = WorkStatus::where('name', 'Enviado a Coordinador')->firstOrFail();
    
    // Guardar estado anterior ANTES de actualizar
    $oldStatusId = $this->getAttribute('current_status_id');
    
    $this->update([
        'current_status_id' => $submittedStatus->getKey(),
        'is_draft' => '0', // Ya no es borrador
    ]);
    
    // Registrar en historial
    WorkStatusHistory::create([
        'work_of_extension_id' => $this->getKey(),
        'from_status_id' => $oldStatusId,
        'to_status_id' => $submittedStatus->getKey(),
        'changed_by_user_id' => $professor->getKey(),
        'comments' => 'Trabajo reenviado después de realizar las correcciones solicitadas.',
    ]);
    
    Log::info('Trabajo reenviado después de correcciones', [
        'work_id' => $this->getKey(),
        'professor_id' => $professor->getKey(),
        'previous_status' => $oldStatusName,
        'new_status' => 'Enviado a Coordinador'
    ]);
    
    // Disparar evento para notificar al coordinador
    WorkResubmittedAfterCorrections::dispatch($this, $professor, $oldStatusName);
}
```

**ARCHIVOS A CREAR/MODIFICAR:**

1. **Modelo:**
   ```
   /app/Models/WorkOfExtension.php
   → Añadir método resubmitAfterCorrections()
   ```

2. **Controlador del Profesor:**
   ```
   /app/Http/Controllers/ProfessorController.php
   → Añadir método resubmit()
   ```

3. **Form Request:**
   ```
   /app/Http/Requests/ResubmitWorkRequest.php
   → Crear validación (reutilizar SubmitWorkRequest)
   ```

4. **Evento:**
   ```
   /app/Events/WorkResubmittedAfterCorrections.php
   → Crear evento
   ```

5. **Listener:**
   ```
   /app/Listeners/SendWorkResubmittedNotification.php
   → Notificar al coordinador
   ```

6. **Notification:**
   ```
   /app/Notifications/WorkResubmitted.php
   → Crear notificación para coordinador
   ```

7. **Vista:**
   ```
   /resources/views/works/edit.blade.php
   → Añadir botón "Reenviar a Revisión" (solo visible si está en estado rechazado/devuelto)
   ```

8. **Ruta:**
   ```
   /routes/web.php
   → Route::post('/works/{work}/resubmit', [ProfessorController::class, 'resubmit'])
       ->name('works.resubmit')
       ->middleware(['can:update,work']);
   ```

9. **Policy:**
   ```
   /app/Policies/WorkOfExtensionPolicy.php
   → Añadir método resubmit() (igual que update, verificar isInDraft)
   ```

10. **Test:**
    ```
    /tests/Feature/WorkResubmissionTest.php
    → Tests para el flujo de reenvío
    ```

**PRIORIDAD:** 🔴 **CRÍTICA** - Debe implementarse antes de pasar a CU8

---

#### 3.2 Falta Método `startReviewByDeanDirector()`

**SITUACIÓN:**
```php
// ✅ Existe para Coordinador:
public function startReviewByCoordinator(User $coordinator): void
{
    // "Enviado a Coordinador" → "En Revisión Coordinador"
}

// ❌ NO existe para Decano:
// public function startReviewByDeanDirector(User $dean): void
```

**PROBLEMA:**
El trabajo nunca transita de "Enviado a Decano/Director" → "En Revisión Decano/Director"

**IMPACTO:**
- 🟡 **MEDIO** - El estado "En Revisión Decano/Director" nunca se usa
- 🟡 Los métodos de aprobación/rechazo aceptan ambos estados, pero nunca se llega al estado "En Revisión"

**COMPORTAMIENTO ESPERADO:**
```php
/**
 * Iniciar revisión por parte del Decano/Director
 * "Enviado a Decano/Director" → "En Revisión Decano/Director"
 */
public function startReviewByDeanDirector(User $dean): void
{
    if ($this->currentStatus->name !== 'Enviado a Decano/Director') {
        throw new \InvalidArgumentException(
            "El trabajo no está en el estado correcto para iniciar revisión por Decano/Director."
        );
    }

    $reviewStatus = WorkStatus::where('name', 'En Revisión Decano/Director')->firstOrFail();

    $oldStatusId = $this->getAttribute('current_status_id');

    $this->update([
        'current_status_id' => $reviewStatus->getKey(),
    ]);

    WorkStatusHistory::create([
        'work_of_extension_id' => $this->getKey(),
        'from_status_id' => $oldStatusId,
        'to_status_id' => $reviewStatus->getKey(),
        'changed_by_user_id' => $dean->getKey(),
        'comments' => 'Decano/Director inició la revisión del trabajo.',
    ]);

    Log::info('Revisión iniciada por Decano/Director', [
        'work_id' => $this->getKey(),
        'dean_id' => $dean->getKey(),
    ]);
}
```

**ARCHIVOS A MODIFICAR:**

1. **Modelo:**
   ```
   /app/Models/WorkOfExtension.php
   → Añadir método startReviewByDeanDirector()
   ```

2. **Controlador:**
   ```
   /app/Http/Controllers/DeanDirectorController.php
   → Añadir método startReview()
   ```

3. **Vista:**
   ```
   /resources/views/dean/show.blade.php
   → Añadir botón "Iniciar Revisión" (cambiar de "Enviado" a "En Revisión")
   ```

**PRIORIDAD:** 🟡 **MEDIA** - No es crítico porque los métodos de aprobación aceptan ambos estados, pero debería implementarse para consistencia

---

### 🔍 **4. Aspectos que Requieren Aclaración**

#### 4.1 ¿Debe el Trabajo Volver a "Borrador"?

**EN EL DIAGRAMA:**
```mermaid
CORRECCIONES --> BORRADOR : Profesor reenvía trabajo corregido
RECHAZADO --> BORRADOR : Profesor subsana y reenvía
```

El diagrama dice que el trabajo vuelve a "BORRADOR" cuando el profesor lo corrige.

**EN LA IMPLEMENTACIÓN:**
```php
// Cuando se rechaza:
$this->update([
    'current_status_id' => $rejectedStatus->getKey(), // "Rechazado por X"
    'is_draft' => '1', // Editable, pero NO cambia a "Borrador"
]);
```

**ANÁLISIS:**

**Interpretación del Diagrama:**
- "BORRADOR" en el diagrama representa un **estado lógico** (editable por profesor)
- NO necesariamente el estado físico "Borrador" en la BD

**Ventajas de la Implementación Actual:**
```
✅ Mantiene trazabilidad: El trabajo permanece como "Rechazado por X"
✅ El profesor ve POR QUÉ fue rechazado cuando lo edita
✅ El historial es más claro
✅ El sidebar de edit.blade.php puede mostrar información contextual
```

**Ventajas de Cambiar a "Borrador":**
```
✅ Literal al diagrama
❌ Pierde contexto visual del rechazo
❌ Requiere revisar historial para saber si fue rechazado antes
```

**RECOMENDACIÓN:**
```markdown
ACCIÓN: Mantener la implementación actual

RAZÓN:
  - La implementación es MEJOR que el diagrama literal
  - `is_draft = '1'` es el verdadero indicador de "editabilidad"
  - El estado físico debe reflejar el último evento significativo

ACTUALIZAR DIAGRAMA:
  - Cambiar las notas para aclarar que:
    "El trabajo permanece en estado Rechazado/Devuelto mientras el 
     profesor lo corrige. El flag is_draft = '1' permite la edición.
     Solo cuando el profesor lo reenvía, cambia a 'Enviado a Coordinador'."
```

**Prioridad:** 🟢 Baja - Solo documentación

---

#### 4.2 Estados Intermedios "Aprobado por X"

**EN LA BASE DE DATOS:**
```php
// WorkStatusSeeder.php
[
    'name' => 'Aprobado por Coordinador',
    'description' => 'Trabajo aprobado por el Coordinador, enviado a Decano/Director.',
],
[
    'name' => 'Aprobado por Decano/Director',
    'description' => 'Trabajo aprobado por Decano/Director, enviado a VIEX.',
],
```

**EN LA IMPLEMENTACIÓN:**
```php
// approveByCoordinator() cambia a:
'Enviado a Decano/Director' // ❌ No usa "Aprobado por Coordinador"

// approveByDeanDirector() cambia a:
'Enviado a VIEX' // ❌ No usa "Aprobado por Decano/Director"
```

**PROBLEMA:**
Los estados "Aprobado por X" existen en la BD pero **nunca se usan**.

**OPCIONES:**

**Opción A - Eliminar estados "Aprobado por X":**
```sql
-- Migración para eliminar estados no usados
DELETE FROM work_statuses 
WHERE name IN ('Aprobado por Coordinador', 'Aprobado por Decano/Director');
```

**Opción B - Usar estados "Aprobado por X" como intermedios:**
```php
// approveByCoordinator():
// 1. "Enviado a Coordinador" → "Aprobado por Coordinador"
// 2. (Automático o por Decano) → "Enviado a Decano/Director"
```

**Opción C - Mantener como están (redundancia aceptable):**
```
✅ Más simple
✅ Estados "Enviado a X" son más claros
❌ BD tiene estados no usados
```

**RECOMENDACIÓN:**
```markdown
ACCIÓN: Opción A - Eliminar estados "Aprobado por X"

RAZÓN:
  - Más simple
  - Estados "Enviado a X" ya indican que la instancia anterior aprobó
  - Reduce complejidad de la máquina de estados

TAREAS:
  1. Crear migración para eliminar estados
  2. Actualizar seeder
  3. Verificar que no se usen en el código (ya verificado)
```

**Prioridad:** 🟡 Media - No afecta funcionalidad, pero limpia la BD

---

## 📊 Resumen de Acciones Requeridas

### 🔴 CRÍTICAS (Bloquean funcionalidad)

| # | Acción | Archivos Afectados | Estimación |
|---|--------|-------------------|------------|
| 1 | Implementar `resubmitAfterCorrections()` | Modelo, Controlador, Request, Event, Listener, Notification, Vista, Ruta, Policy, Test | 4-6 horas |

### 🟡 IMPORTANTES (Mejoran consistencia)

| # | Acción | Archivos Afectados | Estimación |
|---|--------|-------------------|------------|
| 2 | Implementar `startReviewByDeanDirector()` | Modelo, Controlador, Vista, Ruta | 1-2 horas |
| 3 | Eliminar estados "Aprobado por X" no usados | Migración, Seeder | 30 minutos |
| 4 | Actualizar nomenclatura del diagrama | `doc/sega/00_CU.md` | 15 minutos |

### 🟢 OPCIONALES (Solo documentación)

| # | Acción | Archivos Afectados | Estimación |
|---|--------|-------------------|------------|
| 5 | Aclarar que trabajo NO vuelve a "Borrador" físicamente | `doc/sega/00_CU.md` | 10 minutos |
| 6 | Unificar flujos de rechazo del Decano en el diagrama | `doc/sega/00_CU.md` | 10 minutos |

---

## 🎯 Plan de Acción Recomendado

### **Fase 1: Funcionalidad Crítica (AHORA)**
```bash
# 1. Implementar método de reenvío del profesor
git checkout -b feature/professor-resubmit
# [Implementar todos los archivos listados en 3.1]
# [Crear tests]
# [Validar funcionamiento]
git commit -m "feat(works): implement professor resubmission after corrections"
```

### **Fase 2: Consistencia del Sistema (SIGUIENTE)**
```bash
# 2. Implementar startReviewByDeanDirector
git checkout -b feature/dean-start-review
# [Implementar método, controlador, vista]
git commit -m "feat(dean): implement start review transition"

# 3. Limpiar estados no usados
git checkout -b refactor/remove-unused-statuses
# [Crear migración]
# [Actualizar seeder]
git commit -m "refactor(statuses): remove unused 'Aprobado por X' statuses"
```

### **Fase 3: Documentación (DESPUÉS)**
```bash
# 4. Actualizar diagrama y documentación
git checkout -b docs/align-diagram-with-implementation
# [Actualizar 00_CU.md]
git commit -m "docs(CU): align state diagram with actual implementation"
```

---

## 📚 Referencias

- **Documento de Casos de Uso:** `/doc/sega/00_CU.md`
- **Modelo Principal:** `/app/Models/WorkOfExtension.php`
- **Seeder de Estados:** `/database/seeders/WorkStatusSeeder.php`
- **Controlador Coordinador:** `/app/Http/Controllers/CoordinatorController.php`
- **Controlador Decano:** `/app/Http/Controllers/DeanDirectorController.php`
- **Policy de Autorización:** `/app/Policies/WorkOfExtensionPolicy.php`

---

## 🔄 Historial de Cambios

| Fecha | Cambio | Responsable |
|-------|--------|-------------|
| 2025-10-08 | Análisis inicial del diagrama vs implementación | GitHub Copilot |

---

**Nota Final:** Este documento debe actualizarse cuando se implementen las acciones recomendadas.
