{{-- Gestión de Archivos Adjuntos - Edición --}}
<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-paperclip"></i>
            {{ __('Archivos Adjuntos') }}
        </h3>
    </div>
    <div class="card-body">
        {{-- Archivos existentes --}}
        @if($work->getMedia('evidencias')->count() > 0)
        <div class="mb-4">
            <h5 class="text-primary">
                <i class="fas fa-folder-open"></i>
                {{ __('Archivos actuales') }}
            </h5>

            <div class="row">
                @foreach($work->getMedia('evidencias') as $media)
                <div class="col-md-4 mb-3">
                    <div class="card border-info">
                        <div class="card-body text-center p-3">
                            <div class="mb-2">
                                @php
                                $extension = pathinfo($media->name, PATHINFO_EXTENSION);
                                $iconClass = match (strtolower($extension)) {
                                'pdf' => 'fas fa-file-pdf text-danger',
                                'doc', 'docx' => 'fas fa-file-word text-primary',
                                'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                'ppt', 'pptx' => 'fas fa-file-powerpoint text-warning',
                                'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-info',
                                'zip', 'rar' => 'fas fa-file-archive text-secondary',
                                default => 'fas fa-file text-muted'
                                };
                                @endphp
                                <i class="{{ $iconClass }}" style="font-size: 2rem;"></i>
                            </div>

                            <h6 class="card-title text-truncate" title="{{ $media->name }}">
                                {{ $media->name }}
                            </h6>

                            <p class="card-text text-muted small mb-2">
                                {{ number_format($media->size / 1024, 1) }} KB
                            </p>

                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-outline-primary"
                                    title="{{ __('Ver archivo') }}">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <button type="button" class="btn btn-outline-danger remove-media"
                                    data-media-id="{{ $media->id }}" data-media-name="{{ $media->name }}"
                                    title="{{ __('Eliminar archivo') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Campo hidden para archivos a eliminar --}}
        <input type="hidden" name="remove_media" id="remove_media_input">

        <hr>
        @endif

        {{-- Subir nuevos archivos --}}
        <div>
            <h5 class="text-success mb-3">
                <i class="fas fa-cloud-upload-alt"></i>
                {{ __('Agregar nuevos archivos') }}
            </h5>

            <div class="form-group">
                <label for="attachments">
                    {{ __('Seleccionar archivos') }}
                    <small class="text-muted">
                        ({{ __('Formatos soportados: PDF, DOC, XLS, PPT, imágenes. Tamaño máximo: 10MB por archivo') }})
                    </small>
                </label>

                <div class="custom-file">
                    <input type="file" class="custom-file-input @error('attachments.*') is-invalid @enderror"
                        id="attachments" name="attachments[]" multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar">
                    <label class="custom-file-label" for="attachments">
                        {{ __('Seleccionar archivos...') }}
                    </label>
                </div>

                @error('attachments.*')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror
            </div>

            {{-- Preview de archivos seleccionados --}}
            <div id="files-preview" class="mt-3" style="display: none;">
                <h6 class="text-info">
                    <i class="fas fa-eye"></i>
                    {{ __('Archivos seleccionados:') }}
                </h6>
                <div id="files-list" class="list-group list-group-flush"></div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript para manejo de archivos --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lista para archivos a eliminar
        let mediaToRemove = [];

        // Manejar selección de archivos
        const attachmentsInput = document.getElementById('attachments');
        const fileLabel = document.querySelector('label[for="attachments"]');
        const filesPreview = document.getElementById('files-preview');
        const filesList = document.getElementById('files-list');

        attachmentsInput.addEventListener('change', function() {
            const files = Array.from(this.files);

            if (files.length > 0) {
                // Actualizar label
                const fileNames = files.map(f => f.name).join(', ');
                fileLabel.textContent = files.length === 1 ?
                    files[0].name :
                    `${files.length} archivos seleccionados`;

                // Mostrar preview
                filesPreview.style.display = 'block';
                filesList.innerHTML = '';

                files.forEach((file, index) => {
                    const fileItem = document.createElement('div');
                    fileItem.className = 'list-group-item d-flex justify-content-between align-items-center';

                    fileItem.innerHTML = `
                    <div>
                        <i class="fas fa-file text-info me-2"></i>
                        <strong>${file.name}</strong>
                        <small class="text-muted ms-2">(${(file.size / 1024).toFixed(1)} KB)</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-index="${index}">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                    filesList.appendChild(fileItem);
                });
            } else {
                fileLabel.textContent = '{{ __("Seleccionar archivos...") }}';
                filesPreview.style.display = 'none';
            }
        });

        // Remover archivo de la lista de nuevos
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-file')) {
                const index = parseInt(e.target.closest('.remove-file').dataset.index);
                const dt = new DataTransfer();
                const files = Array.from(attachmentsInput.files);

                files.forEach((file, i) => {
                    if (i !== index) {
                        dt.items.add(file);
                    }
                });

                attachmentsInput.files = dt.files;
                attachmentsInput.dispatchEvent(new Event('change'));
            }
        });

        // Manejar eliminación de archivos existentes
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-media')) {
                e.preventDefault();

                const button = e.target.closest('.remove-media');
                const mediaId = button.dataset.mediaId;
                const mediaName = button.dataset.mediaName;

                // Confirmar eliminación
                if (confirm(`{{ __('¿Estás seguro de que deseas eliminar el archivo') }}: ${mediaName}?`)) {
                    // Agregar a lista de eliminación
                    mediaToRemove.push(mediaId);
                    document.getElementById('remove_media_input').value = mediaToRemove.join(',');

                    // Remover visualmente
                    button.closest('.col-md-4').remove();

                    // Mostrar mensaje de confirmación
                    toastr.info(`{{ __('El archivo será eliminado al guardar los cambios') }}: ${mediaName}`);
                }
            }
        });

        // Validación de archivos
        attachmentsInput.addEventListener('change', function() {
            const files = Array.from(this.files);
            const maxSize = 10 * 1024 * 1024; // 10MB
            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/gif',
                'application/zip',
                'application/x-rar-compressed'
            ];

            let invalidFiles = [];

            files.forEach(file => {
                if (file.size > maxSize) {
                    invalidFiles.push(`${file.name}: {{ __('Archivo demasiado grande (máximo 10MB)') }}`);
                }
                if (!allowedTypes.includes(file.type)) {
                    invalidFiles.push(`${file.name}: {{ __('Formato no soportado') }}`);
                }
            });

            if (invalidFiles.length > 0) {
                alert(`{{ __('Archivos inválidos') }}:\n\n${invalidFiles.join('\n')}`);
                this.value = '';
                filesPreview.style.display = 'none';
                fileLabel.textContent = '{{ __("Seleccionar archivos...") }}';
            }
        });
    });
</script>

{{-- Estilos adicionales --}}
<style>
    .remove-media:hover {
        transform: scale(1.1);
    }

    .card-body .btn-group-sm .btn {
        padding: 0.25rem 0.4rem;
    }

    #files-preview .list-group-item {
        padding: 0.5rem 0.75rem;
    }

    .custom-file-input:focus~.custom-file-label {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
</style>