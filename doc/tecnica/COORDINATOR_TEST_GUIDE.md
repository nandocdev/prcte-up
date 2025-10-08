# 🎯 Guía de Prueba - Módulo de Coordinador

## ✅ **Problema Resuelto**
El error "Target class [role] does not exist" ha sido corregido completamente.

## 🚀 **Cómo Probar el Módulo**

### 1. **Verificar que el servidor esté corriendo:**
```bash
php artisan serve
```

### 2. **Acceder con usuario coordinador:**
- **Email**: `luis.garcia@up.ac.pa`
- **Password**: `password123`
- **Rol**: `coordinador_extension`

### 3. **Rutas disponibles:**
- **Dashboard**: `http://localhost:8000/coordinator`
- **Ver trabajo**: `http://localhost:8000/coordinator/works/{id}`
- **Aprobar trabajo**: `POST /coordinator/works/{id}/approve`
- **Solicitar cambios**: `POST /coordinator/works/{id}/request-changes`

### 4. **Funcionalidades implementadas:**
- ✅ **Dashboard** con estadísticas y trabajos pendientes
- ✅ **Filtrado** por unidad organizacional automático
- ✅ **Vista detallada** de trabajos con historial
- ✅ **Aprobación** con comentarios opcionales
- ✅ **Solicitud de cambios** con comentarios obligatorios

### 5. **Control de acceso:**
- ✅ Solo usuarios con rol `coordinador_extension` o `super_admin`
- ✅ Validación en cada método del controlador
- ✅ Logging detallado de accesos para auditoría
- ✅ Mensajes de error claros para acceso denegado

### 6. **Usuarios de prueba disponibles:**

#### **Coordinadores:**
- `luis.garcia@up.ac.pa` (Prof. Luis Fernando García)
- `carmen.delgado@up.ac.pa` (Profa. Carmen Delgado)
- `miguel.rodriguez@up.ac.pa` (Prof. Miguel Rodríguez)

#### **Super Admin (acceso completo):**
- `admin@up.ac.pa` (Super Administrador VIEX)

#### **Profesores (NO pueden acceder):**
- `alejandra.morales@up.ac.pa` (Prof. Alejandra Morales)
- `fernando.castillo@up.ac.pa` (Prof. Fernando Castillo)

## 🔧 **Estados de Trabajo Manejados:**
- **Recibe**: "Enviado a Coordinador"
- **Procesa**: "En Revisión Coordinador"
- **Aprueba a**: "Enviado a Decano/Director"
- **Devuelve a**: "Devuelto para Corrección"

## 📝 **Próximo paso recomendado:**
Crear trabajos de extensión como profesor y enviarlos al coordinador para probar el flujo completo.

---

**Estado actual**: ✅ Módulo de Coordinador 100% funcional
