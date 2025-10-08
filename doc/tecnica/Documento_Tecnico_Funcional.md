¡Excelente! A continuación, genero la versión actualizada y completa del **Documento Técnico Funcional (DTF)**, incorporando las mejoras y recomendaciones identificadas en el análisis previo.

---

**Sistema de Registro y Certificación de Trabajos de Extensión**
**Cliente:** Universidad de Panamá
**Unidad responsable:** Vicerrectoría de Extensión (VIEX)
**Fecha:** Julio 2025
**Versión:** 1.1

---

### **Control de Cambios**

| Versión | Fecha      | Autor                 | Descripción de Cambios                                                                                                                                                                                                                          |
| :------ | :--------- | :-------------------- | :---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1.0     | 07/2025    | Equipo de Análisis    | Versión inicial del documento.                                                                                                                                                                                                                  |
| 1.1     | 07/2025    | Ingeniero de Software | **Versión revisada y aumentada:**<br>- Se añade RF16 para gestionar el consentimiento de publicación.<br>- Se mejora RF08 para incluir reportes de gestión (SLA).<br>- Se añade RF17 para registrar trabajos históricos.<br>- Se detallan los anexos. |

---

## 1. Introducción

El presente documento describe los requerimientos funcionales del sistema de registro y certificación de trabajos de extensión para la Universidad de Panamá. El sistema busca digitalizar el proceso establecido en el "Manual de Procedimientos para Presentar Trabajos de Extensión", garantizando transparencia, trazabilidad y eficiencia en la gestión.

---

## 2. Objetivos del Sistema

*   Registrar digitalmente los trabajos de extensión realizados por docentes.
*   Automatizar el flujo de aprobación: Coordinador de Extensión → Decano/Director → VIEX.
*   Centralizar la documentación y evidencias.
*   Emitir certificados electrónicos firmados por la VIEX.
*   Proporcionar seguimiento en tiempo real del estado de los trámites.
*   Monitorear los tiempos de gestión para cumplir con los plazos establecidos.

---

## 3. Actores del Sistema

| Actor                                      | Descripción                                                            |
| ------------------------------------------ | ---------------------------------------------------------------------- |
| **Docente**                                | Registra trabajos, adjunta evidencias, consulta estado y certificados. |
| **Coordinador de Extensión**               | Revisa formularios, hace observaciones o aprueba.                      |
| **Decano/Director/Coordinador Ext. Univ.** | Aprueba o rechaza trabajos, remite a VIEX.                             |
| **VIEX**                                   | Revisa, certifica y emite certificados.                                |
| **Administrador**                          | Gestiona catálogos, usuarios y configuraciones del sistema.            |

---

## 4. Tipos de Trabajo de Extensión

*   Proyectos de Extensión:
    *   Institucionales
    *   De Unidad Académica
    *   Servicio Social
*   Actividades de Extensión
*   Asistencias Técnicas:
    *   Consultoría
    *   Asesoría
*   Publicaciones

---

## 5. Requerimientos Funcionales

### RF01 - Autenticación y gestión de cuentas

*   Login con usuario y contraseña institucional.
*   Recuperación de contraseña.
*   Asignación de roles (Docente, Coordinador, etc.).

### RF02 - Registro de trabajo de extensión

*   Selección de tipo de trabajo (Proyecto, Actividad, Publicación, etc.).
*   Completar formulario dinámico que **mostrará únicamente los campos requeridos para el tipo de trabajo seleccionado**, conforme a la estructura del formulario oficial (ver Anexo 2).
*   Adjuntar evidencias en formatos permitidos (PDF, Word, JPG, PNG, etc.).
*   Guardar como borrador para continuar más tarde o enviar para revisión.

### RF03 - Revisión por Coordinador de Extensión

*   Visualizar un panel con los trabajos pendientes de revisión de su unidad.
*   Aprobar y remitir al siguiente nivel (Decano/Director).
*   Devolver al docente con observaciones claras para subsanación.
*   Agregar comentarios internos visibles solo para otros revisores.

### RF04 - Revisión por Decano/Director/Centro Regional

*   Recibir notificaciones de trabajos aprobados por el Coordinador.
*   Aprobar y remitir a VIEX para certificación final.
*   Devolver al Coordinador con observaciones.

### RF05 - Revisión y certificación por VIEX

*   Visualizar el expediente completo del trabajo, incluyendo toda la trazabilidad (comentarios, fechas, responsables).
*   Aprobar o rechazar el trabajo con una justificación obligatoria.
*   Emitir el certificado final en formato PDF.
*   Aplicar una firma electrónica o digital válida al certificado.

### RF06 - Seguimiento de estado

*   El docente y los revisores podrán visualizar en una línea de tiempo o diagrama de flujo:
    *   El estado actual del trámite (Ej: "En revisión por Decanato").
    *   El historial completo de cambios de estado, con fechas y responsable de cada paso.
    *   Las observaciones realizadas en cada etapa.

### RF07 - Generación de certificados

*   Descarga del certificado en formato PDF desde el perfil del docente.
*   Inclusión de la firma electrónica autorizada de la VIEX.
*   Incorporación de un código de verificación único (QR o alfanumérico).

### RF08 - Reportes

*   Generación de reportes operativos filtrando por tipo de trabajo, facultad, sede, docente, estado o rango de fechas.
*   **Reportes de gestión (SLA):** Módulo para VIEX y Administradores que muestre:
    *   Tiempo promedio de tramitación por cada etapa del flujo.
    *   Alertas visuales para trámites que se aproximan o superan el plazo de 20 días hábiles.
*   Exportación de todos los reportes a formatos Excel y PDF.

### RF09 - Gestión de catálogos y configuraciones

*   Módulo para el Administrador para gestionar:
    *   Facultades, Escuelas, Departamentos, Centros Regionales.
    *   Tipos de trabajo y sus sub-tipos.
    *   Períodos académicos.

### RF10 - Notificaciones

*   Envío de correos electrónicos automáticos a los actores involucrados en cada cambio de estado relevante (Ej: "Su trabajo ha sido aprobado por el Coordinador").
*   Alertas visuales en un dashboard o panel de inicio dentro del sistema.

### RF11 - Gestión de Participantes

*   Permitir al docente responsable agregar otros participantes (docentes, estudiantes, externos) al trabajo.
*   Definir el rol de cada participante (coordinador, colaborador, tutor, estudiante, etc.).

### RF12 - Subsanaciones y observaciones

*   Habilitar un campo de texto obligatorio cuando un trabajo es devuelto o rechazado.
*   El docente recibirá una notificación con las observaciones y podrá editar su registro y reenviarlo, iniciando nuevamente el ciclo de revisión desde el punto donde fue devuelto.

### RF13 - Control de versiones de evidencias

*   Si un docente reemplaza un archivo de evidencia durante una subsanación, el sistema deberá conservar la versión anterior en el historial para fines de auditoría.

### RF14 - Validación pública de certificados

*   Una página pública accesible a través del escaneo del código QR o introduciendo un código en una URL segura.
*   Esta página mostrará la información esencial del certificado (nombre del docente, título del trabajo, fecha de emisión) para verificar su autenticidad, sin exponer datos sensibles.

### RF15 - Asociación por período o ciclo evaluativo

*   Cada trabajo de extensión deberá estar asociado a un período académico específico, seleccionable de un catálogo.
*   El sistema permitirá filtrar y agrupar trabajos por ciclo académico.

### **RF16 - Consentimiento de Publicación**

*   Durante el registro del trabajo, el formulario deberá incluir una sección clara con un **checkbox** para que el docente autorice explícitamente a la Universidad de Panamá a publicar los resultados finales del trabajo.
*   El estado de este consentimiento (otorgado o no otorgado) deberá quedar registrado y ser visible para la VIEX.

### **RF17 - Registro de Trabajos Históricos**

*   El sistema ofrecerá una funcionalidad para el registro simplificado de trabajos de extensión realizados y certificados antes de la implementación de este sistema.
*   Este registro consistirá en capturar datos mínimos (título, docente, fecha, tipo de trabajo) y **adjuntar el formulario y/o certificado original en formato PDF**.
*   **Nota:** Esta funcionalidad no realizará extracción automática de datos (OCR) del PDF; funcionará como un repositorio digital.

---

## 6. Requerimientos No Funcionales

*   **Accesibilidad:** Cumplimiento con las pautas WCAG 2.1 nivel AA.
*   **Seguridad:** Arquitectura basada en roles y permisos, protección contra vulnerabilidades web comunes (OWASP Top 10), registro de auditoría (logs).
*   **Escalabilidad:** Diseño modular que permita agregar nuevos tipos de trámites o funcionalidades en el futuro sin reestructuraciones mayores.
*   **Compatibilidad:** Funcionamiento garantizado en las últimas dos versiones de los navegadores web modernos (Chrome, Firefox, Safari, Edge).
*   **Disponibilidad:** Alta disponibilidad del servicio y políticas de respaldo (backup) y recuperación de datos automáticas y periódicas.

---

## 7. Flujo de Trabajo (Resumen)

1.  **Docente:** Registra el trabajo, adjunta evidencias, otorga consentimiento de publicación y lo envía a revisión.
2.  **Coordinador:** Recibe, revisa y aprueba o devuelve con observaciones.
3.  **Decano:** Recibe lo aprobado por el coordinador, revisa y aprueba o devuelve.
4.  **VIEX:** Recibe el expediente completo, realiza la revisión final y, si procede, certifica y emite el certificado digital.
5.  **Docente:** Recibe la notificación final y descarga su certificado verificado desde el sistema.

---

## 8. Casos de Uso

| ID   | Nombre                                            |
| :--- | :------------------------------------------------ |
| CU01 | Iniciar sesión                                    |
| CU02 | Registrar trabajo de extensión                    |
| CU03 | Subir evidencias                                  |
| CU04 | Enviar trabajo a coordinador                      |
| CU05 | Revisión por coordinador                          |
| CU06 | Revisión por decano o director                    |
| CU07 | Certificación por VIEX                            |
| CU08 | Emitir certificado digital                        |
| CU09 | Consultar estado del trabajo                      |
| CU10 | Descargar certificado PDF                         |
| CU11 | Generar reporte por filtros (operativo y de SLA)  |
| CU12 | Gestionar catálogos                               |
| CU13 | Agregar observaciones al flujo                    |
| CU14 | Recuperar contraseña                              |
| CU15 | Enviar notificaciones por correo                  |
| CU16 | Agregar participantes al trabajo                  |
| CU17 | Subsanar trabajo rechazado                        |
| CU18 | Visualizar historial de versiones de evidencias   |
| CU19 | Verificar certificado con QR                      |
| CU20 | Asociar trabajo a período académico               |
| **CU21** | **Otorgar consentimiento de publicación**           |
| **CU22** | **Registrar trabajo histórico desde PDF**         |

---

## 9. Consideraciones Adicionales

*   El frontend debe ser intuitivo y accesible para usuarios de diferentes niveles de habilidad técnica.
*   El sistema debe estar preparado para integrarse a futuro con otros sistemas de la universidad, como el de gestión de personal docente.
*   Se recomienda establecer un plan de capacitación para los diferentes roles de usuario antes del lanzamiento.

---

## 10. Anexos

*   **Anexo 1:** Manual de Procedimientos para Trabajos de Extensión (Referencia primaria).
*   **Anexo 2:** Mapeo Detallado de Campos del Formulario Oficial. Este anexo detallará la correspondencia entre cada campo del formulario en papel y su contraparte digital en el sistema para cada tipo de trabajo.
