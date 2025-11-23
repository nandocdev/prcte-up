@extends('adminlte::page')

@section('title', 'Revisar Trabajo - Coordinador')

@section('content')
<div class="row">
    <!-- Columna Izquierda: Información Principal del Trabajo (8/12) -->
    <div class="col-lg-8">

        {{-- Encabezado del Trabajo --}}
        @include('coordinator.partials._work_header', ['work' => $work])

        {{-- Card: Datos Generales y Descripción --}}
        <div class="card card-primary card-outline">
            <div class="card-body">
                {{-- Info boxes de Profesor y Unidad --}}
                @include('coordinator.partials._general_info_cards', ['work' => $work])

                {{-- Fechas, Participantes, Tipo --}}
                @include('coordinator.partials._general_info_details', ['work' => $work])

                <hr>

            </div>
        </div>

        {{-- Detalles Específicos por Tipo de Trabajo --}}
        @if($work->workType && $work->workType->code === 'proyecto' && $work->projectDetail)
        @include('coordinator.partials._work_details_project', ['projectDetails' => $work->projectDetail])
        @elseif($work->workType && $work->workType->code === 'actividad' && $work->activityDetail)
        @include('coordinator.partials._work_details_activity', ['activityDetails' => $work->activityDetail])
        @elseif($work->workType && $work->workType->code === 'publicacion' && $work->publicationDetail)
        @include('coordinator.partials._work_details_publication', ['publicationDetails' => $work->publicationDetail])
        @elseif($work->workType && $work->workType->code === 'asistencia_tecnica' && $work->technicalAssistanceDetail)
        @include('coordinator.partials._work_details_technical_assistance', ['technicalAssistanceDetails' => $work->technicalAssistanceDetail])
        @endif

        {{-- Participantes del Trabajo --}}
        @if($work->participants && $work->participants->count() > 0)
        @include('coordinator.partials._work_participants_list', ['participants' => $work->participants])
        @endif

        {{-- Archivos y Evidencias --}}
        @if($work->getMedia('attachments')->count() > 0)
        @include('coordinator.partials._work_attachments_list', ['mediaItems' => $work->getMedia('attachments')])
        @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Sin evidencias:</strong> Este trabajo no tiene documentos adjuntos.
        </div>
        @endif

        {{-- Historial Completo --}}
        @if($work->statusHistory->count() > 0)
        @include('coordinator.partials._work_status_history_timeline', ['statusHistory' => $work->statusHistory])
        @endif

    </div> {{-- Fin Columna Izquierda --}}

    <!-- Columna Derecha: Panel de Acciones y Notas (4/12) -->
    <div class="col-lg-4">


        {{-- Panel de Notas del Revisor (Sticky) --}}
        @include('coordinator.partials._reviewer_notes_panel')

        {{-- Panel de Acciones de Coordinación (Botones) --}}
        @include('coordinator.partials._coordinator_action_panel', ['work' => $work])

    </div> {{-- Fin Columna Derecha --}}

</div> {{-- Fin Row Principal --}}

{{-- Modales de Acción (deben estar dentro de @section('content')) --}}
@include('coordinator.partials._modals._modal_approve', ['work' => $work])
@include('coordinator.partials._modals._modal_request_changes', ['work' => $work])
@include('coordinator.partials._modals._modal_reject', ['work' => $work])
@stop

@section('css')
{{-- La sección CSS puede quedar aquí o moverse a un archivo CSS dedicado que Vite/Laravel mezcle --}}
<style>
    /* Sidebar sticky */
    .sticky-top {
        position: -webkit-sticky;
        position: sticky;
        z-index: 1020;
    }

    /* Timeline mejorado */
    .timeline {
        margin: 0;
        padding: 0;
    }

    .timeline-item {
        background: #fff;
        border: 1px solid #ddd;
        margin: 10px 0;
        padding: 10px;
        border-radius: 4px;
    }

    .timeline-header {
        font-size: 1rem;
        margin: 0 0 5px 0;
    }

    /* Info boxes mejorados */
    .info-box {
        min-height: 90px;
    }

    .info-box-number {
        font-size: 1.1rem;
    }

    /* Badges más grandes */
    .badge-lg {
        font-size: 0.95rem;
        padding: 0.4rem 0.7rem;
    }

    /* Callouts personalizados */
    .callout {
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .callout-info {
        border-left: 4px solid #17a2b8;
        background-color: #d1ecf1;
    }

    .callout-success {
        border-left: 4px solid #28a745;
        background-color: #d4edda;
    }

    /* Textarea de notas */
    #reviewNotes {
        font-family: 'Courier New', monospace;
        resize: vertical;
    }

    /* Card outline purple para participantes */
    .card-outline.card-purple {
        border-top: 3px solid #6f42c1;
    }

    /* Mejoras en modales */
    .modal-lg {
        max-width: 800px;
    }

    /* Botones de decisión más prominentes */
    .btn-lg {
        font-size: 1.1rem;
        font-weight: 600;
    }
</style>
@stop

@section('js')
<script>
    // Sistema de almacenamiento AJAX para notas de revisión
    const WORK_ID = {{ $work->getKey() }};
    const CSRF_TOKEN = '{{ csrf_token() }}';

    $(document).ready(function() {
        // Debug: Verificar que los modales existen en el DOM
        console.log('Modales cargados:');
        console.log('- approveModal:', $('#approveModal').length > 0 ? 'SÍ' : 'NO');
        console.log('- requestChangesModal:', $('#requestChangesModal').length > 0 ? 'SÍ' : 'NO');
        console.log('- rejectModal:', $('#rejectModal').length > 0 ? 'SÍ' : 'NO');

        // Debug: Verificar que Bootstrap modal está disponible
        if (typeof $.fn.modal === 'undefined') {
            console.error('ERROR: Bootstrap modal no está cargado');
        } else {
            console.log('✓ Bootstrap modal disponible');
        }

        // Auto-guardar notas cada 5 segundos
        let notesTimeout;
        $('#reviewerNotes').on('input', function() {
            clearTimeout(notesTimeout);
            notesTimeout = setTimeout(saveReviewNotes, 5000);

            // Mostrar indicador de guardado
            $('.autosave-indicator').html('<span class="badge badge-warning"><i class="fas fa-spinner fa-spin"></i> Guardando...</span>');
        });

        // Guardar checklist al hacer clic
        $('.checklist-item').on('change', function() {
            saveChecklist();
        });

        // Auto-expandir textareas
        $('textarea').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Fallback manual para abrir modales si data-toggle no funciona
        $('button[data-target="#approveModal"]').on('click', function(e) {
            console.log('Click en botón Aprobar');
            if (!$('#approveModal').hasClass('show')) {
                $('#approveModal').modal('show');
            }
        });

        $('button[data-target="#requestChangesModal"]').on('click', function(e) {
            console.log('Click en botón Solicitar Cambios');
            if (!$('#requestChangesModal').hasClass('show')) {
                $('#requestChangesModal').modal('show');
            }
        });

        $('button[data-target="#rejectModal"]').on('click', function(e) {
            console.log('Click en botón Rechazar');
            if (!$('#rejectModal').hasClass('show')) {
                $('#rejectModal').modal('show');
            }
        });
    });

    // Guardar notas vía AJAX
    function saveReviewNotes() {
        const notes = $('#reviewerNotes').val();

        $.ajax({
            url: `/coordinator/works/${WORK_ID}/checklist`,
            method: 'POST',
            data: {
                _token: CSRF_TOKEN,
                reviewer_notes: notes
            },
            success: function(response) {
                console.log('Notas guardadas automáticamente');

                // Mostrar indicador de guardado
                $('.autosave-indicator').html('<span class="badge badge-success"><i class="fas fa-check"></i> Guardado</span>');

                // Ocultar indicador después de 2 segundos
                setTimeout(function() {
                    $('.autosave-indicator').fadeOut(function() {
                        $(this).html('').show();
                    });
                }, 2000);
            },
            error: function(xhr, status, error) {
                console.error('Error al guardar notas:', error);
                $('.autosave-indicator').html('<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Error</span>');

                setTimeout(function() {
                    $('.autosave-indicator').fadeOut(function() {
                        $(this).html('').show();
                    });
                }, 3000);
            }
        });
    }

    // Guardar checklist vía AJAX
    function saveChecklist() {
        const checklistData = {};

        // Campos globales
        checklistData.format_correct = $('#check_format').is(':checked');
        checklistData.objectives_clear = $('#check_objectives').is(':checked');
        checklistData.description_complete = $('#check_description').is(':checked');
        checklistData.evidence_attached = $('#check_evidence').is(':checked');
        checklistData.participants_complete = $('#check_participants').is(':checked');
        checklistData.dates_coherent = $('#check_dates').is(':checked');
        checklistData.regulations_compliant = $('#check_regulations').is(':checked');

        // Campos específicos por tipo de trabajo
        @if($work->work_type_id == 1) {{-- Proyecto --}}
            checklistData.project_category_valid = $('#check_project_category').is(':checked');
            checklistData.general_description_complete = $('#check_general_description').is(':checked');
            checklistData.justification_adequate = $('#check_justification').is(':checked');
            checklistData.methodology_clear = $('#check_methodology').is(':checked');
            checklistData.scope_defined = $('#check_scope').is(':checked');
            checklistData.resource_plan_complete = $('#check_resource_plan').is(':checked');
            checklistData.schedule_realistic = $('#check_schedule').is(':checked');
            checklistData.cost_plan_detailed = $('#check_cost_plan').is(':checked');
            checklistData.beneficiaries_described = $('#check_beneficiaries').is(':checked');
            checklistData.communication_plan_present = $('#check_communication_plan').is(':checked');
            checklistData.institution_relationships_clear = $('#check_institution_relationships').is(':checked');
            checklistData.final_comments_relevant = $('#check_final_comments').is(':checked');
            checklistData.ss_intervention_appropriate = $('#check_ss_intervention').is(':checked');
        @elseif($work->work_type_id == 2) {{-- Actividad --}}
            checklistData.activity_type_appropriate = $('#check_activity_type').is(':checked');
            checklistData.modality_suitable = $('#check_modality').is(':checked');
            checklistData.duration_reasonable = $('#check_duration').is(':checked');
            checklistData.introduction_contextualized = $('#check_introduction').is(':checked');
            checklistData.justification_adequate = $('#check_activity_justification').is(':checked');
            checklistData.objectives_specific = $('#check_activity_objectives').is(':checked');
            checklistData.methodology_detailed = $('#check_activity_methodology').is(':checked');
            checklistData.resources_available = $('#check_resources').is(':checked');
            checklistData.beneficiaries_profile_clear = $('#check_beneficiaries_profile').is(':checked');
            checklistData.expected_participants_realistic = $('#check_expected_participants').is(':checked');
            checklistData.institution_relationships_present = $('#check_activity_relationships').is(':checked');
            checklistData.comments_relevant = $('#check_activity_comments').is(':checked');
            checklistData.certification_appropriate = $('#check_certification').is(':checked');
        @elseif($work->work_type_id == 3) {{-- Publicación --}}
            checklistData.publication_type_valid = $('#check_publication_type').is(':checked');
            checklistData.summary_comprehensive = $('#check_summary').is(':checked');
            checklistData.editorial_reputable = $('#check_editorial').is(':checked');
            checklistData.isbn_issn_present = $('#check_isbn_issn').is(':checked');
            checklistData.target_audience_defined = $('#check_target_audience').is(':checked');
            checklistData.relevance_justified = $('#check_relevance').is(':checked');
            checklistData.publication_date_valid = $('#check_publication_date').is(':checked');
            checklistData.media_type_appropriate = $('#check_media_type').is(':checked');
            checklistData.media_nature_clear = $('#check_media_nature').is(':checked');
            checklistData.language_appropriate = $('#check_language').is(':checked');
            checklistData.print_run_realistic = $('#check_print_run').is(':checked');
        @elseif($work->work_type_id == 4) {{-- Asistencia Técnica --}}
            checklistData.assistance_type_valid = $('#check_assistance_type').is(':checked');
            checklistData.collaborating_institution_clear = $('#check_institution').is(':checked');
            checklistData.specialization_area_relevant = $('#check_specialization').is(':checked');
            checklistData.description_comprehensive = $('#check_assistance_description').is(':checked');
            checklistData.objectives_clear = $('#check_assistance_objectives').is(':checked');
            checklistData.methodology_detailed = $('#check_assistance_methodology').is(':checked');
            checklistData.expected_products_defined = $('#check_expected_products').is(':checked');
            checklistData.evidence_sufficient = $('#check_assistance_evidence').is(':checked');
            checklistData.work_modality_appropriate = $('#check_work_modality').is(':checked');
            checklistData.estimated_hours_realistic = $('#check_estimated_hours').is(':checked');
        @endif

        $.ajax({
            url: `/coordinator/works/${WORK_ID}/checklist`,
            method: 'POST',
            data: {
                _token: CSRF_TOKEN,
                checklist_data: checklistData
            },
            success: function(response) {
                console.log('Checklist guardado:', response);

                // Actualizar porcentaje de progreso si está disponible
                if (response.progress_percentage !== undefined) {
                    const progressBadge = $('.badge-info');
                    if (progressBadge.length > 0) {
                        progressBadge.text(response.progress_percentage + '% completado');
                    }
                }

                // Mostrar indicador de guardado
                $('.autosave-indicator').html('<span class="badge badge-success"><i class="fas fa-check"></i> Checklist guardado</span>');

                setTimeout(function() {
                    $('.autosave-indicator').fadeOut(function() {
                        $(this).html('').show();
                    });
                }, 2000);
            },
            error: function(xhr, status, error) {
                console.error('Error al guardar checklist:', error);
                $('.autosave-indicator').html('<span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Error al guardar</span>');

                setTimeout(function() {
                    $('.autosave-indicator').fadeOut(function() {
                        $(this).html('').show();
                    });
                }, 3000);
            }
        });
    }

    // Limpiar checklist
    function clearChecklist() {
        if (confirm('¿Está seguro de que desea limpiar el checklist? Esta acción no se puede deshacer.')) {
            // Desmarcar todos los checkboxes
            $('.checklist-item').prop('checked', false);

            // Enviar checklist vacío
            const emptyChecklist = {};
            $('.checklist-item').each(function() {
                const fieldName = $(this).attr('id').replace('check_', '');
                emptyChecklist[fieldName] = false;
            });

            $.ajax({
                url: `/coordinator/works/${WORK_ID}/checklist`,
                method: 'POST',
                data: {
                    _token: CSRF_TOKEN,
                    checklist_data: emptyChecklist
                },
                success: function(response) {
                    console.log('Checklist limpiado');
                    alert('Checklist limpiado correctamente');

                    // Actualizar porcentaje de progreso
                    const progressBadge = $('.badge-info');
                    if (progressBadge.length > 0) {
                        progressBadge.text('0% completado');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error al limpiar checklist:', error);
                    alert('Error al limpiar el checklist');
                }
            });
        }
    }

    // Copiar notas al modal de aprobación
    function copyNotesToApproval() {
        const notes = $('#reviewerNotes').val();
        if (notes) {
            $('#approval_comments').val(notes);
            alert('Notas copiadas al campo de comentarios de aprobación');
        } else {
            alert('No hay notas para copiar');
        }
    }

    // Copiar notas al modal de cambios
    function copyNotesToChanges() {
        const notes = $('#reviewerNotes').val();
        if (notes) {
            $('#change_comments').val(notes);
            alert('Notas copiadas al campo de comentarios de subsanaciones');
        } else {
            alert('No hay notas para copiar');
        }
    }

    // Copiar notas al modal de rechazo
    function copyNotesToReject() {
        const notes = $('#reviewerNotes').val();
        if (notes) {
            $('#rejection_reason').val(notes);
            alert('Notas copiadas al campo de motivo de rechazo');
        } else {
            alert('No hay notas para copiar');
        }
    }

    // === MANEJO DE FORMULARIOS DE ACCIÓN ===

    // Función genérica para manejar el envío de formularios
    function handleFormSubmit($form, minLength = 0) {
        const $submitBtn = $form.find('button[type="submit"]');

        // Deshabilitar botón para evitar doble-clic
        $submitBtn.prop('disabled', true);

        // Mostrar spinner
        const originalText = $submitBtn.html();
        $submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...');

        // Si falla, restaurar botón después de 3 segundos
        setTimeout(function() {
            if ($submitBtn.prop('disabled')) {
                $submitBtn.prop('disabled', false);
                $submitBtn.html(originalText);
            }
        }, 3000);
    }

    // Modal de Aprobación
    $('#approveModal form').on('submit', function(e) {
        const $form = $(this);
        const checkbox = $('#confirm_approve');

        if (!checkbox.is(':checked')) {
            e.preventDefault();
            alert('Debe confirmar que ha revisado completamente el trabajo antes de aprobar.');
            return false;
        }

        handleFormSubmit($form);
    });

    // Modal de Solicitar Cambios
    $('#requestChangesModal form').on('submit', function(e) {
        const $form = $(this);
        const comments = $('#change_comments').val().trim();

        if (comments.length < 10) {
            e.preventDefault();
            alert('Por favor, especifique las correcciones requeridas (mínimo 10 caracteres).');
            return false;
        }

        if (comments.length > 1000) {
            e.preventDefault();
            alert('Los comentarios no pueden exceder 1000 caracteres.');
            return false;
        }

        if (!confirm('¿Está seguro de que desea solicitar subsanaciones a este trabajo?')) {
            e.preventDefault();
            return false;
        }

        handleFormSubmit($form);
    });

    // Modal de Rechazo
    $('#rejectModal form').on('submit', function(e) {
        const $form = $(this);
        const reason = $('#rejection_reason').val().trim();
        const checkbox = $('#confirm_reject');

        if (!checkbox.is(':checked')) {
            e.preventDefault();
            alert('Debe confirmar que desea rechazar este trabajo.');
            return false;
        }

        if (reason.length < 20) {
            e.preventDefault();
            alert('Por favor, proporcione una razón detallada del rechazo (mínimo 20 caracteres).');
            return false;
        }

        if (reason.length > 2000) {
            e.preventDefault();
            alert('Los comentarios no pueden exceder 2000 caracteres.');
            return false;
        }

        if (!confirm('⚠️ ATENCIÓN: ¿Está completamente seguro de que desea RECHAZAR este trabajo? Esta es una acción seria que indica problemas significativos.')) {
            e.preventDefault();
            return false;
        }

        handleFormSubmit($form);
    });

    // Cerrar modales al hacer clic en cancelar
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0]?.reset();
        $(this).find('button[type="submit"]').prop('disabled', false);
    });
</script>
@stop