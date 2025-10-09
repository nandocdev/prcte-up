# 📋 Auditoría CU13: Descargar Certificación

**Fecha de Auditoría:** 8 de octubre de 2025  
**Auditor:** GitHub Copilot  
**Caso de Uso:** CU13 - Descargar Certificación  
**Estado General:** ✅ **IMPLEMENTADO** (pendiente QA)

---

## 📊 Resumen Ejecutivo

| Componente | Estado | Cobertura | Observaciones clave |
|-----------|--------|-----------|---------------------|
| **Rutas y Navegación** | ✅ Completo | 100% | Rutas públicas y VIEX consumen el mismo flujo de descarga y retornan el PDF. |
| **Controladores** | ✅ Completo | 100% | `ViexAdminController` y `WorkOfExtensionController` delegan la descarga al modelo, con manejo de errores. |
| **Modelo/Media** | ✅ Completo | 100% | `Certification` usa MediaLibrary, genera PDF vía job y almacena en colección `certificates`. |
| **Policies/Permisos** | ✅ Completo | 100% | Coordinadores y Decanos pueden visualizar trabajos certificados/rechazados (constantes actualizadas). |
| **Vistas** | ✅ Completo | 100% | Botones existentes ahora entregan archivos funcionales; se añadió plantilla PDF dedicada. |
| **Notificaciones/Logs** | N/A | N/A | No aplican directamente al flujo de descarga. |
| **Testing** | ❌ Ausente | 0% | Aún faltan pruebas feature/unit para cubrir la descarga (tarea pendiente). |

**Conclusión:** El caso de uso está operativo: las certificaciones se generan con PDF adjunto, se almacenan en MediaLibrary y se pueden descargar según los permisos definidos. Falta incorporar pruebas automatizadas.

---

## 🎯 Flujo CU13 Requerido

1. Actor autenticado accede a un trabajo en estado **"Certificado"**.
2. Sistema expone enlace/botón para descargar la certificación.
3. Se valida autorización de acceso (profesor, coordinador, decano, VIEX).
4. Se recupera el PDF generado previamente y almacenado en la colección `media`.
5. Se entrega el archivo al usuario.

---

## 🔍 Implementación Detallada

### 1. Rutas disponibles

- **Ruta pública**: `route('certificates.download')` (`WorkOfExtensionController@downloadCertificate`) entrega el PDF a profesores autenticados.
- **Ruta VIEX**: `route('viex.certificate.download')` (`ViexAdminController@downloadCertificate`) reutiliza la misma respuesta y respeta autorizaciones.

### 2. Controladores

- Ambos controladores delegan la descarga a `Certification::buildDownloadResponse()` y registran eventos de error en el log.
- Se devuelven mensajes amigables mediante traducciones cuando el archivo no está disponible.

### 3. Generación y almacenamiento del PDF

- `WorkOfExtension::generateCertification()` sincroniza el estado "En VIEX - Aprobado" → "Certificado", crea el registro y despacha `GenerateCertificationPdf`.
- El job genera el PDF con DomPDF, lo adjunta a la colección `certificates` (MediaLibrary, `singleFile`) y guarda metadatos de auditoría.

### 4. Permisos y Policies

- `COORDINATOR_STATUS_NAMES` y `DEAN_STATUS_NAMES` ahora incluyen `"Certificado"` y `"Rechazado por VIEX"`, eliminando los 403 para coordinadores/decano.
- Las policies reutilizan estas constantes, por lo que no se requirió lógica adicional.

### 5. Vistas

- Se añadió `resources/views/certifications/pdf.blade.php` con estructura estándar y textos traducibles.
- Los botones existentes en `works.show` y `viex.index` descargan correctamente el archivo generado.

### 6. Testing

- Aún pendiente la creación de pruebas automatizadas para validar la descarga, estados de error y autorización (tarea abierta).

---

## ❗ Riesgos y Brechas

1. **Cobertura de pruebas inexistente**: no hay tests que verifiquen descarga, permisos o ausencia de archivo.
2. **Generación síncrona del PDF**: se usa `dispatchSync`; si el job falla se registra en log pero no llega al usuario (se notifica vía flash message). Evaluar cola asíncrona cuando exista workers.
3. **Internacionalización parcial**: vista PDF fija `lang="es"`; si `APP_LOCALE` cambia a `en`, revisar adaptación.

---

## 🛠️ Recomendaciones Prioritarias

1. **Agregar pruebas feature**
   - Cubrir descarga exitosa para profesor/coordinador/decano/VIEX.
   - Validar respuesta de error cuando no exista media adjunta.

2. **Monitoreo y reintentos del job**
   - Configurar cola asíncrona (worker) y reintentos para la generación de PDF.
   - Registrar métricas en caso de fallo repetido.

3. **Mejoras de internacionalización**
   - Ajustar `certifications/pdf.blade.php` para usar `app()->getLocale()` y definir estilos multi-idioma si se habilita `en` como locale principal.

---

## 🧪 Escenarios Sugeridos para QA

| Escenario | Descripción | Resultado Esperado |
|-----------|-------------|--------------------|
| Profesor descarga su propio certificado | Trabajo en estado `Certificado`, profesor autenticado. | Descarga exitosa del PDF. |
| Coordinador descarga certificado de su unidad | Coordinador de la facultad correspondiente. | Acceso permitido tras actualizar policy. |
| Certificado sin archivo adjunto | Registro existe pero no hay media. | Mensaje amigable o respuesta 404 controlada. |
| Usuario no autorizado | Usuario fuera de la unidad o estado. | Respuesta 403. |
| Reintentos después de regenerar PDF | Verificar que se entregue versión actualizada. | PDF actualizado disponible. |

---

## ✅ Checklist de Cumplimiento

| Requisito CU13 | Estado | Evidencia |
|----------------|--------|-----------|
| Enlace visible en trabajos certificados | ✅ | `works/show.blade.php` línea 529. |
| Validación de estado "En VIEX - Aprobado" para certificar | ✅ | `WorkOfExtension::generateCertification` actualiza estado y emite número. |
| Controlador descarga y retorna archivo | ✅ | `ViexAdminController::downloadCertificate` (líneas ~520-555). |
| Archivo PDF almacenado en `media` | ✅ | `GenerateCertificationPdf` adjunta a colección `certificates`. |
| Permisos para todos los actores | ✅ | Constantes `COORDINATOR_STATUS_NAMES` y `DEAN_STATUS_NAMES` incluyen estados finales. |
| Tests automatizados | ❌ | Aún no existen pruebas. |

---

## 📚 Referencias

- `routes/web.php` líneas 83-104 — definición de rutas de descarga.
- `app/Http/Controllers/WorkOfExtensionController.php` líneas 520-550 — descarga directa desde `Certification`.
- `app/Http/Controllers/ViexAdminController.php` líneas 520-560 — nuevo `downloadCertificate` con manejo de errores.
- `app/Models/WorkOfExtension.php` líneas 1530-1600 — generación de certificación y disparo de job PDF.
- `app/Policies/WorkOfExtensionPolicy.php` líneas 20-71 — visibilidad actualizada.
- `app/Models/Certification.php` líneas 1-120 — MediaLibrary y helper de descarga.
- `app/Jobs/GenerateCertificationPdf.php` — generación y adjunto del PDF.
- `resources/views/works/show.blade.php` línea 529 — botón de descarga para profesores.
- `resources/views/viex/index.blade.php` línea 232 — botón de descarga para VIEX.
- `resources/views/certifications/pdf.blade.php` — plantilla DomPDF.

---

**Resultado Final:** ✅ **CU13 cumple con los requisitos funcionales**, pendiente de validar con pruebas automatizadas y QA funcional.
