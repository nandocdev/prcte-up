<!-- Sección 3: Documentos y Evidencias -->
<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-cloud-upload-alt"></i>
            {{ __('3. Documentos y Evidencias de Soporte') }}
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">

        <!-- Información sobre documentos requeridos -->
        <div class="alert alert-info" id="file-requirements-info">
            <h5><i class="fas fa-info-circle"></i> {{ __('Documentos Requeridos') }}</h5>
            <p id="file-requirements-text">
                {{ __('Seleccione primero el tipo de trabajo para ver los documentos requeridos específicos.') }}
            </p>
        </div>

        <!-- Zona de carga de archivos -->
        <div class="form-group">
            <label for="attachments">
                <strong>{{ __('Archivos Adjuntos') }}</strong>
                <span class="text-muted">({{ __('Opcional en borrador, requerido para envío') }})</span>
            </label>

            <!-- Dropzone para múltiples archivos -->
            <div class="upload-zone" id="uploadZone">
                <div class="upload-zone-content">
                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                    <h5>{{ __('Arrastra archivos aquí o haz clic para seleccionar') }}</h5>
                    <p class="text-muted">
                        {{ __('Formatos permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG') }}<br>
                        {{ __('Tamaño máximo por archivo: 10MB') }}
                    </p>
                    <button type="button" class="btn btn-primary" id="selectFilesBtn">
                        <i class="fas fa-folder-open"></i> {{ __('Seleccionar Archivos') }}
                    </button>
                </div>

                <!-- Input oculto para archivos -->
                <input type="file" id="attachments" name="attachments[]" multiple
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;">
            </div>
        </div>

        <!-- Lista de archivos seleccionados -->
        <div id="filesList" class="files-list" style="display: none;">
            <h6><strong>{{ __('Archivos Seleccionados:') }}</strong></h6>
            <div id="filesContainer">
                <!-- Los archivos se cargarán dinámicamente aquí -->
            </div>
        </div>

        <!-- Tipos de documentos por categoría -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h6><strong>{{ __('Tipos de Documentos Sugeridos:') }}</strong></h6>

                <!-- Documentos para Proyectos -->
                <div class="document-category" id="project-documents" style="display: none;">
                    <div class="alert alert-light">
                        <strong>{{ __('Para Proyectos de Extensión:') }}</strong>
                        <ul class="mb-0 mt-2">
                            <li>{{ __('Plan de trabajo detallado (PDF)') }}</li>
                            <li>{{ __('Cronograma de actividades (PDF/Excel)') }}</li>
                            <li>{{ __('Presupuesto estimado (PDF/Excel)') }}</li>
                            <li>{{ __('Cartas de apoyo institucional (PDF)') }}</li>
                            <li>{{ __('Diagnóstico de necesidades (PDF)') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Documentos para Actividades -->
                <div class="document-category" id="activity-documents" style="display: none;">
                    <div class="alert alert-light">
                        <strong>{{ __('Para Actividades de Extensión:') }}</strong>
                        <ul class="mb-0 mt-2">
                            <li>{{ __('Programa del evento (PDF)') }}</li>
                            <li>{{ __('Lista de participantes (PDF/Excel)') }}</li>
                            <li>{{ __('Material didáctico (PDF)') }}</li>
                            <li>{{ __('Evidencias fotográficas (JPG/PNG)') }}</li>
                            <li>{{ __('Certificados de participación (PDF)') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Documentos para Publicaciones -->
                <div class="document-category" id="publication-documents" style="display: none;">
                    <div class="alert alert-light">
                        <strong>{{ __('Para Publicaciones:') }}</strong>
                        <ul class="mb-0 mt-2">
                            <li>{{ __('Manuscript completo (PDF/DOC)') }}</li>
                            <li>{{ __('Carta de aceptación/publicación (PDF)') }}</li>
                            <li>{{ __('Comprobante de indexación (PDF)') }}</li>
                            <li>{{ __('Portada de la publicación (PDF/JPG)') }}</li>
                            <li>{{ __('Certificado de derechos de autor (PDF)') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Documentos para Asistencias Técnicas -->
                <div class="document-category" id="assistance-documents" style="display: none;">
                    <div class="alert alert-light">
                        <strong>{{ __('Para Asistencias Técnicas:') }}</strong>
                        <ul class="mb-0 mt-2">
                            <li>{{ __('Carta de solicitud institucional (PDF)') }}</li>
                            <li>{{ __('Informe técnico desarrollado (PDF)') }}</li>
                            <li>{{ __('Productos entregables (PDF/DOC)') }}</li>
                            <li>{{ __('Carta de satisfacción del cliente (PDF)') }}</li>
                            <li>{{ __('Evidencias de implementación (PDF/JPG)') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progreso de carga (oculto inicialmente) -->
        <div id="uploadProgress" style="display: none;">
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                    0%
                </div>
            </div>
        </div>
    </div>
</div>
