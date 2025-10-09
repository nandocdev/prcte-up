<script>
    /**
     * Función para mostrar la sección específica según el tipo de trabajo
     */
    function showWorkTypeSection(workTypeId) {
        console.log('Mostrando sección para tipo:', workTypeId);

        // Ocultar todas las secciones disponibles
        $('.work-section').removeClass('active').hide();

        if (!workTypeId) {
            return;
        }

        const sectionKey = window.sectionConfig?.[workTypeId]?.section ?? null;
        if (sectionKey) {
            $(`#section-${sectionKey}`).addClass('active').show();
            return;
        }

        // Compatibilidad con IDs numéricos antiguos
        $(`#section-${workTypeId}`).addClass('active').show();
    }

    // /**
    //  * Función para calcular el período académico basado en las fechas de inicio y finalización
    //  */
    // function calculateAcademicPeriod(startDate, endDate) {
    //     if (!startDate || !endDate) {
    //         return '';
    //     }

    //     const start = new Date(startDate);
    //     const end = new Date(endDate);

    //     // Validar que la fecha de inicio sea menor que la de finalización
    //     if (start > end) {
    //         return '';
    //     }

    //     // Obtener el año de la fecha de inicio
    //     const year = start.getFullYear();

    //     // Determinar el semestre basado en el mes de inicio
    //     // Primer semestre: Enero a Julio (meses 0-6)
    //     // Segundo semestre: Agosto a Diciembre (meses 7-11)
    //     const startMonth = start.getMonth();
    //     const semester = startMonth <= 6 ? 'I' : 'II';

    //     return `${year}-${semester}`;
    // }

    // /**
    //  * Función para actualizar el período académico cuando cambien las fechas
    //  */
    // function updateAcademicPeriod() {
    //     const startDate = $('#start_date').val();
    //     const endDate = $('#end_date').val();
    //     const academicPeriod = calculateAcademicPeriod(startDate, endDate);

    //     $('#academic_period').val(academicPeriod);

    //     // Agregar feedback visual
    //     if (academicPeriod) {
    //         $('#academic_period').removeClass('is-invalid').addClass('is-valid');
    //     } else {
    //         $('#academic_period').removeClass('is-valid');
    //     }
    // }

    $(function() {
        //     // Configurar toastr si está disponible
        //     if (typeof toastr !== 'undefined') {
        //         toastr.options = {
        //             "closeButton": true,
        //             "debug": false,
        //             "newestOnTop": true,
        //             "progressBar": true,
        //             "positionClass": "toast-top-right",
        //             "preventDuplicates": false,
        //             "onclick": null,
        //             "showDuration": "300",
        //             "hideDuration": "1000",
        //             "timeOut": "5000",
        //             "extendedTimeOut": "1000",
        //             "showEasing": "swing",
        //             "hideEasing": "linear",
        //             "showMethod": "fadeIn",
        //             "hideMethod": "fadeOut"
        //         };

        //         // Mostrar mensajes flash con toastr
        //         if (session('success')) {
        //             toastr.success(' <?= session('success') ?> ');
        //         }

        //         if (session('error')) {
        //             toastr.error(' <?= session('error') ?> ');
        //         }

        //         if (session('warning')) {
        //             toastr.warning(' <?= session('warning') ?> ');
        //         }

        //         if (session('info')) {
        //             toastr.info(' <?= session('info') ?> ');
        //         }

        //         // Mostrar errores de validación
        //         const errors_any = '<?= $errors->any() ?>';
        //         if (errors_any) {
        //             const errors = <?= json_encode($errors->all()) ?>
        //             // recorre los errores con un foreach y los muestra en un toastr
        //             errors.forEach(function (e) {
        //                 toastr.error(e);
        //             });
        //         }
        //     }

        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: '{{ __("Seleccione una opción") }}'
        });

        // Variables
        const workTypeSelect = $('#work_type_id');
        const startDateInput = $('#start_date');
        const endDateInput = $('#end_date');
        const descriptionTextarea = $('#description');
        const titleInput = $('#title');
        const submitBtn = $('#submitBtn');
        const form = $('#workForm');
        const progressBar = $('#form-progress');
        const requiredFieldsList = $('#required-fields');
        const contextualHelp = $('#contextual-help');

        // Configuración de secciones específicas
        const sectionConfig = {
            '1': {
                section: 'proyecto',
                title: '{{ __("Proyecto de Extensión") }}',
                help: '{{ __("Complete la información específica del proyecto: objetivos, metodología, beneficiarios y área geográfica.") }}',
                color: 'success'
            },
            '2': {
                section: 'actividad',
                title: '{{ __("Actividad de Extensión") }}',
                help: '{{ __("Defina el tipo de actividad, modalidad, duración y perfil de participantes esperados.") }}',
                color: 'warning'
            },
            '3': {
                section: 'publicacion',
                title: '{{ __("Publicación") }}',
                help: '{{ __("Especifique el tipo de publicación, editorial, público objetivo y detalles de distribución.") }}',
                color: 'info'
            },
            '4': {
                section: 'asistencia',
                title: '{{ __("Asistencia Técnica Especializada") }}',
                help: '{{ __("Describa el tipo de asistencia, institución beneficiaria y productos esperados.") }}',
                color: 'secondary'
            }
        };

        window.sectionConfig = sectionConfig;

        // Configuración de documentos por tipo de trabajo
        const documentRequirements = {
            '1': {
                category: 'project-documents',
                info: '{{ __("Para proyectos se requieren: plan de trabajo, cronograma, presupuesto y cartas de apoyo.") }}'
            },
            '2': {
                category: 'activity-documents',
                info: '{{ __("Para actividades se requieren: programa del evento, lista de participantes y material didáctico.") }}'
            },
            '3': {
                category: 'publication-documents',
                info: '{{ __("Para publicaciones se requieren: manuscript completo, carta de aceptación y comprobante de indexación.") }}'
            },
            '4': {
                category: 'assistance-documents',
                info: '{{ __("Para asistencias técnicas se requieren: carta de solicitud, informe técnico y productos entregables.") }}'
            }
        };

        // Mostrar/ocultar secciones según tipo de trabajo
        workTypeSelect.on('change', function() {
            const selectedType = $(this).val();

            // Ocultar todas las secciones específicas
            $('.work-section').hide().find('input, textarea, select').prop('required', false);

            if (selectedType && sectionConfig[selectedType]) {
                const config = sectionConfig[selectedType];

                // Mostrar sección específica
                $(`#section-${config.section}`).show();

                // Marcar campos como requeridos
                $(`#section-${config.section} [data-required="true"]`).prop('required', true);

                // Actualizar ayuda contextual
                contextualHelp.html(`
                <div class="alert alert-${config.color}">
                    <h6><i class="fas fa-info-circle"></i> ${config.title}</h6>
                    <p class="mb-0">${config.help}</p>
                </div>
            `);

                // Actualizar información de archivos si está disponible
                if (documentRequirements[selectedType]) {
                    const requirements = documentRequirements[selectedType];

                    // Ocultar todas las categorías de documentos
                    $('.document-category').hide();

                    // Mostrar categoría específica
                    $(`#${requirements.category}`).show();

                    // Actualizar información
                    $('#file-requirements-text').text(requirements.info);
                } else {
                    $('#file-requirements-text').text('{{ __("Seleccione primero el tipo de trabajo para ver los documentos requeridos específicos.") }}');
                }
            } else {
                contextualHelp.html(`
                <div class="alert alert-warning">
                    <p class="mb-0">
                        <strong>{{ __("Seleccione un tipo de trabajo") }}</strong> {{ __("para ver información específica y campos adicionales.") }}
                    </p>
                </div>
            `);
            }

            updateFormProgress();
        });

        // Validación de fechas
        function validateDates() {
            if (startDateInput.val() && endDateInput.val()) {
                if (new Date(endDateInput.val()) <= new Date(startDateInput.val())) {
                    endDateInput.addClass('is-invalid');
                    if (!endDateInput.next('.invalid-feedback').length) {
                        endDateInput.after('<div class="invalid-feedback">{{ __("La fecha de finalización debe ser posterior a la fecha de inicio") }}</div>');
                    }
                    return false;
                } else {
                    endDateInput.removeClass('is-invalid');
                    endDateInput.next('.invalid-feedback').remove();
                    return true;
                }
            }
            return true;
        }

        // Contador de caracteres para descripción
        function updateCharacterCount() {
            const maxLength = 2000;
            const currentLength = descriptionTextarea.val().length;
            const remaining = maxLength - currentLength;
            const charCountElement = $('#char-count');

            if (currentLength < 50) {
                charCountElement.text(`{{ __("Necesita al menos") }} ${50 - currentLength} {{ __("caracteres más.") }}`)
                    .removeClass('text-info text-warning text-danger')
                    .addClass('text-muted');
            } else if (remaining < 200) {
                charCountElement.text(`{{ __("Caracteres restantes:") }} ${remaining}`)
                    .removeClass('text-muted text-info text-danger')
                    .addClass(remaining < 50 ? 'text-warning' : 'text-info');
            } else {
                charCountElement.text('{{ __("Mínimo 50 caracteres. Incluya objetivos, metodología y resultados esperados.") }}')
                    .removeClass('text-warning text-danger')
                    .addClass('text-muted');
            }
        }

        // Actualizar progreso del formulario
        function updateFormProgress() {
            const requiredFields = [
                workTypeSelect,
                $('#organizational_unit_id'),
                titleInput,
                descriptionTextarea,
                startDateInput,
                endDateInput
            ];

            let completed = 0;
            let total = requiredFields.length;

            // Verificar campos básicos
            requiredFields.forEach(field => {
                if (field.val() && field.val().trim() !== '') {
                    completed++;
                }
            });

            // Verificar campos específicos de la sección activa
            const activeSection = $('.work-section:visible');
            if (activeSection.length > 0) {
                const sectionRequiredFields = activeSection.find('[data-required="true"]');
                total += sectionRequiredFields.length;

                sectionRequiredFields.each(function() {
                    if ($(this).val() && $(this).val().trim() !== '') {
                        completed++;
                    }
                });
            }

            const percentage = Math.round((completed / total) * 100);
            progressBar.css('width', percentage + '%').text(percentage + '%');

            if (percentage >= 100) {
                progressBar.removeClass('bg-warning bg-info').addClass('bg-success');
            } else if (percentage >= 50) {
                progressBar.removeClass('bg-warning bg-success').addClass('bg-info');
            } else {
                progressBar.removeClass('bg-success bg-info').addClass('bg-warning');
            }

            // Actualizar lista de campos requeridos
            updateRequiredFieldsList();
        }

        // Actualizar lista visual de campos requeridos
        function updateRequiredFieldsList() {
            const fieldChecks = [{
                    field: workTypeSelect,
                    label: '{{ __("Tipo de trabajo") }}'
                },
                {
                    field: $('#organizational_unit_id'),
                    label: '{{ __("Unidad organizacional") }}'
                },
                {
                    field: titleInput,
                    label: '{{ __("Título") }}'
                },
                {
                    field: descriptionTextarea,
                    label: '{{ __("Descripción") }}'
                },
                {
                    field: startDateInput,
                    label: '{{ __("Fecha de inicio") }}'
                },
                {
                    field: endDateInput,
                    label: '{{ __("Fecha de finalización") }}'
                }
            ];

            requiredFieldsList.empty();

            fieldChecks.forEach(check => {
                const isComplete = check.field.val() && check.field.val().trim() !== '';
                const icon = isComplete ? 'fas fa-check-square text-success' : 'fas fa-square text-muted';
                requiredFieldsList.append(`<li><i class="${icon}"></i> ${check.label}</li>`);
            });
        }

        setMinStartDate();
        // Event listeners
        startDateInput.on('change', validateDates);
        startDateInput.on('change', setMinEndDate);
        endDateInput.on('change', validateDates);
        endDateInput.on('change', updateAcademicPeriod);
        descriptionTextarea.on('input', updateCharacterCount);

        // Actualizar progreso en tiempo real
        form.find('input, textarea, select').on('input change', function() {
            setTimeout(updateFormProgress, 100);
        });

        // Validación al enviar formulario
        form.on('submit', function(e) {
            if (!validateDates()) {
                e.preventDefault();
                toastr?.error('{{ __("Por favor corrija los errores en las fechas") }}');
                return false;
            }

            // Verificar campos requeridos
            let missingFields = [];

            if (!workTypeSelect.val()) missingFields.push('{{ __("Tipo de trabajo") }}');
            if (!$('#organizational_unit_id').val()) missingFields.push('{{ __("Unidad organizacional") }}');
            if (!titleInput.val() || titleInput.val().trim().length < 10) missingFields.push('{{ __("Título (mín. 10 caracteres)") }}');
            if (!descriptionTextarea.val() || descriptionTextarea.val().trim().length < 50) missingFields.push('{{ __("Descripción (mín. 50 caracteres)") }}');
            if (!startDateInput.val()) missingFields.push('{{ __("Fecha de inicio") }}');
            if (!endDateInput.val()) missingFields.push('{{ __("Fecha de finalización") }}');

            if (missingFields.length > 0) {
                e.preventDefault();
                toastr?.error(`{{ __("Complete los siguientes campos:") }} ${missingFields.join(', ')}`);
                return false;
            }

            // Mostrar estado de carga
            submitBtn.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> {{ __("Guardando...") }}');
        });

        // Inicializar
        updateCharacterCount();
        updateFormProgress();

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Trigger initial work type change if there's an old value
        const oldWorkType = '{{ old("work_type_id") }}';
        const initialWorkType = workTypeSelect.val() || oldWorkType;

        if (initialWorkType) {
            showWorkTypeSection(initialWorkType);
            workTypeSelect.trigger('change');
        }

        // Auto-save functionality (optional)
        let autoSaveTimer;

        function autoSave() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                console.log('Auto-save checkpoint');
                // Here you could implement actual auto-save functionality
            }, 60000); // Every minute
        }

        form.on('input change', autoSave);

        // ===========================================
        // FUNCIONALIDAD DE CARGA DE ARCHIVOS
        // ===========================================

        // Variables para manejo de archivos
        const uploadZone = $('#uploadZone');
        const fileInput = $('#attachments');
        const selectFilesBtn = $('#selectFilesBtn');
        const filesList = $('#filesList');
        const filesContainer = $('#filesContainer');
        const uploadProgress = $('#uploadProgress');
        let selectedFiles = [];
        let isUpdatingFileInput = false; // Bandera para prevenir recursión
        const semestre = updateAcademicPeriod(); //Calcula el semestre segun la fecha definida


        // si


        // Verificar que los elementos existen antes de agregar event listeners
        if (uploadZone.length === 0 || fileInput.length === 0) {
            console.warn('Elementos de carga de archivos no encontrados en el DOM');
            return;
        }

        // Click en botón seleccionar
        if (selectFilesBtn.length > 0) {
            selectFilesBtn.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.click();
            });
        }

        // Click en zona de carga
        if (uploadZone.length > 0) {
            uploadZone.on('click', function(e) {
                // Solo abrir el selector si no se hizo click en un botón o elemento interactivo
                if (!$(e.target).is('button, a, input') && !$(e.target).closest('button, a, input').length) {
                    console.log('Zona de carga clickeada');
                    e.preventDefault();
                    e.stopPropagation();
                    fileInput.click();
                }
            });
        }


        // Drag and drop functionality
        if (uploadZone.length > 0) {
            uploadZone.on('dragover dragenter', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            uploadZone.on('dragleave dragend', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            uploadZone.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');

                const files = e.originalEvent.dataTransfer.files;
                handleFiles(files);
            });
        }

        // Cambio en input de archivos
        if (fileInput.length > 0) {
            fileInput.on('change', function() {
                // Evitar recursión cuando estamos actualizando programáticamente
                if (isUpdatingFileInput) {
                    return;
                }

                const files = this.files;
                handleFiles(files);
            });
        }

        // Manejar archivos seleccionados
        function handleFiles(files) {
            const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/jpg', 'image/png'];
            const maxSize = 10 * 1024 * 1024; // 10MB

            Array.from(files).forEach(file => {
                // Validar tipo de archivo
                if (!validTypes.includes(file.type)) {
                    toastr?.error(`{{ __("Archivo no permitido:") }} ${file.name}`);
                    return;
                }

                // Validar tamaño
                if (file.size > maxSize) {
                    toastr?.error(`{{ __("Archivo demasiado grande:") }} ${file.name} ({{ __("Máximo 10MB") }})`);
                    return;
                }

                // Verificar duplicados
                if (selectedFiles.some(f => f.name === file.name && f.size === file.size)) {
                    toastr?.warning(`{{ __("Archivo ya seleccionado:") }} ${file.name}`);
                    return;
                }

                // Añadir archivo válido
                selectedFiles.push(file);
            });

            updateFilesList();
        }

        // Actualizar lista de archivos
        function updateFilesList() {
            if (selectedFiles.length === 0) {
                filesList.hide();
                return;
            }

            filesList.show();
            filesContainer.empty();

            selectedFiles.forEach((file, index) => {
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileIcon = getFileIcon(file.type);

                const fileItem = $(`
                    <div class="file-item d-flex align-items-center justify-content-between p-2 mb-2 border rounded">
                        <div class="file-info d-flex align-items-center">
                            <i class="${fileIcon} fa-2x mr-3"></i>
                            <div>
                                <div class="file-name font-weight-bold">${file.name}</div>
                                <div class="file-size text-muted small">${fileSize} MB</div>
                            </div>
                        </div>
                        <div class="file-actions">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-index="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                         </div>
                    </div>
                `);

                filesContainer.append(fileItem);
            });

            // Actualizar el input file con los archivos actuales
            updateFileInput();
        }

        // Obtener icono según tipo de archivo
        function getFileIcon(fileType) {
            if (fileType === 'application/pdf') return 'fas fa-file-pdf text-danger';
            if (fileType.includes('word') || fileType.includes('document')) return 'fas fa-file-word text-primary';
            if (fileType.includes('image')) return 'fas fa-file-image text-success';
            return 'fas fa-file text-muted';
        }

        // Remover archivo - Event listener global (solo se registra una vez)
        $(document).on('click', '.remove-file', function() {
            const index = $(this).data('index');
            selectedFiles.splice(index, 1);
            updateFilesList();
        });

        // Actualizar input de archivos (crear DataTransfer para los archivos seleccionados)
        function updateFileInput() {
            isUpdatingFileInput = true; // Activar bandera

            const dt = new DataTransfer();
            selectedFiles.forEach(file => {
                dt.items.add(file);
            });
            fileInput[0].files = dt.files;

            // Desactivar bandera después de un pequeño delay
            setTimeout(() => {
                isUpdatingFileInput = false;
            }, 100);
        }

        // Mostrar progreso de carga (se puede usar durante el envío)
        function showUploadProgress(percent) {
            uploadProgress.show();
            const progressBar = uploadProgress.find('.progress-bar');
            progressBar.css('width', percent + '%').text(percent + '%');

            if (percent >= 100) {
                progressBar.addClass('bg-success');
                setTimeout(() => {
                    uploadProgress.hide();
                }, 2000);
            }
        }

        /**
         * Función para calcular el período académico basado en las fechas de inicio y finalización
         */
        function calculateAcademicPeriod(startDate, endDate) {
            if (!startDate || !endDate) {
                return '';
            }

            const start = new Date(startDate);
            const end = new Date(endDate);

            // Validar que la fecha de inicio sea menor que la de finalización
            if (start > end) {
                return '';
            }

            // Obtener el año de la fecha de inicio
            const year = start.getFullYear();

            // Determinar el semestre basado en el mes de inicio
            // Primer semestre: Enero a Julio (meses 0-6)
            // Segundo semestre: Agosto a Diciembre (meses 7-11)
            const startMonth = start.getMonth();
            const semester = startMonth < 6 ? 'I' : 'II';

            return `${year}-${semester}`;
        }

        /**
         * Función para actualizar el período académico cuando cambien las fechas
         */
        function updateAcademicPeriod() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();
            const academicPeriod = calculateAcademicPeriod(startDate, endDate);
            // console.log(academicPeriod);

            $('#academic_period').val(academicPeriod);

            // Agregar feedback visual
            if (academicPeriod) {
                $('#academic_period').removeClass('is-invalid').addClass('is-valid');
            } else {
                $('#academic_period').removeClass('is-valid');
            }
        }

        function setMinStartDate() {
            const startDateInput = document.getElementById("start_date");
            const endDateInput = document.getElementById("end_date");
            const today = new Date().toISOString().split("T")[0];
            startDateInput.min = today;
            endDateInput.min = today; // Asegurar que la fecha de fin no sea antes de hoy
        }

        function setMinEndDate() {
            const startDateInput = document.getElementById("start_date");
            const endDateInput = document.getElementById("end_date");

            startDateInput.addEventListener("change", () => {
                if (startDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    startDate.setDate(startDate.getDate() + 1); // fecha siguiente
                    const minEndDate = startDate.toISOString().split("T")[0];
                    endDateInput.min = minEndDate;
                }
            });
        }


    });
</script>