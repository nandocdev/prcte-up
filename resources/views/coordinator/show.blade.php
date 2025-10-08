@extends('adminlte::page')

@section('title', 'Revisar Trabajo - Coordinador')

@section('content_header')
<!-- <div class="row">
    <div class="col-sm-6">
        <h1><i class="fas fa-file-signature mr-2"></i>Revisar Trabajo de Extensión</h1>
        <p class="text-muted">{{ $work->workType->name ?? 'N/A' }}</p>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('coordinator.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Revisar Trabajo</li>
        </ol>
    </div>
</div>-->
@stop

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
                <!-- 
                {{-- Descripción General --}}
                <div class="mb-3">
                    <h5><i class="fas fa-align-left text-primary"></i> Descripción del Trabajo</h5>
                    <div class="callout callout-info">
                        <p class="mb-0">{{ $work->getAttribute('description') ?? 'Sin descripción disponible' }}</p>
                    </div>
                </div>

                {{-- Objetivos (pueden estar aquí o en los detalles específicos) --}}
                @if($work->projectDetail && ($work->projectDetail->general_objectives || $work->projectDetail->specific_objectives))
                <div class="mb-3">
                    <h5><i class="fas fa-bullseye text-success"></i> Objetivos</h5>
                    <div class="callout callout-success">
                        @if($work->projectDetail->general_objectives)
                        <strong>Generales:</strong>
                        <p class="mb-0">{{ $work->projectDetail->general_objectives }}</p>
                        @endif
                        @if($work->projectDetail->specific_objectives)
                        <strong>Específicos:</strong>
                        <p class="mb-0">{{ $work->projectDetail->specific_objectives }}</p>
                        @endif
                    </div>
                </div>
                @endif -->
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
@stop

{{-- Modales de Acción --}}
@section('modals')
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
    // Sistema de almacenamiento local para notas de revisión
    const WORK_ID = {

        <?=
        $work->getKey()
        ?>

    };
    const STORAGE_KEY = `coordinator_review_notes_${WORK_ID}`;
    const CHECKLIST_KEY = `coordinator_review_checklist_${WORK_ID}`;

    $(document).ready(function() {
        // Cargar notas guardadas
        loadReviewNotes();
        loadChecklist();

        // Auto-guardar notas cada 5 segundos
        let notesTimeout;
        $('#reviewerNotes').on('input', function() {
            clearTimeout(notesTimeout);
            notesTimeout = setTimeout(saveReviewNotes, 5000);

            // Mostrar indicador de guardado
            $('.autosave-indicator').html('<span class="badge badge-warning"><i class="fas fa-spinner fa-spin"></i> Guardando...</span>');
        });

        // Guardar checklist al hacer clic
        $('.review-checklist input[type="checkbox"]').on('change', function() {
            saveChecklist();
        });

        // Auto-expandir textareas
        $('textarea').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Limpiar notas al enviar cualquier formulario de acción
        $('#approveModal form, #requestChangesModal form, #rejectModal form').on('submit', function() {
            // Limpiar notas y checklist del localStorage
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(CHECKLIST_KEY);
            console.log('Notas y checklist limpiados después de enviar acción');
        });
    });

    // Guardar notas en localStorage
    function saveReviewNotes() {
        const notes = $('#reviewerNotes').val();
        localStorage.setItem(STORAGE_KEY, notes);
        console.log('Notas guardadas automáticamente');

        // Mostrar indicador de guardado
        $('.autosave-indicator').html('<span class="badge badge-success"><i class="fas fa-check"></i> Guardado</span>');

        // Ocultar indicador después de 2 segundos
        setTimeout(function() {
            $('.autosave-indicator').fadeOut(function() {
                $(this).html('').show();
            });
        }, 2000);
    }

    // Cargar notas desde localStorage
    function loadReviewNotes() {
        const notes = localStorage.getItem(STORAGE_KEY);
        if (notes) {
            $('#reviewerNotes').val(notes);
        }
    }

    // Guardar checklist
    function saveChecklist() {
        const checklist = {};
        $('.review-checklist input[type="checkbox"]').each(function() {
            checklist[$(this).attr('id')] = $(this).is(':checked');
        });
        localStorage.setItem(CHECKLIST_KEY, JSON.stringify(checklist));
    }

    // Cargar checklist
    function loadChecklist() {
        const checklistJson = localStorage.getItem(CHECKLIST_KEY);
        if (checklistJson) {
            const checklist = JSON.parse(checklistJson);
            $('.review-checklist input[type="checkbox"]').each(function() {
                const checkId = $(this).attr('id');
                if (checklist[checkId]) {
                    $(this).prop('checked', true);
                }
            });
        }
    }

    // Limpiar notas y checklist
    function clearReviewNotes() {
        if (confirm('¿Está seguro de que desea limpiar todas sus notas y el checklist? Esta acción no se puede deshacer.')) {
            $('#reviewerNotes').val('');
            $('.review-checklist input[type="checkbox"]').prop('checked', false);
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(CHECKLIST_KEY);
            alert('Notas y checklist limpiados correctamente');
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

        // Limpiar notas del localStorage
        localStorage.removeItem(STORAGE_KEY);
        localStorage.removeItem(CHECKLIST_KEY);

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