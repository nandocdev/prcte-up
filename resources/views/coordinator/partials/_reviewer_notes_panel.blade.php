{{-- Panel de Notas del Revisor --}}
<div class="card card-primary card-outline sidebar-sticky">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-sticky-note mr-2"></i>
            Notas del Revisor
        </h3>
        <div class="card-tools">
            <span class="autosave-indicator"></span>
        </div>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="reviewerNotes">
                <i class="fas fa-pen"></i>
                Tus Notas de Revisión
            </label>
            <textarea 
                id="reviewerNotes" 
                class="form-control" 
                rows="8"
                placeholder="Escribe aquí tus observaciones durante la revisión del trabajo...&#10;&#10;Estas notas se guardan automáticamente en tu navegador."></textarea>
            <small class="form-text text-muted">
                <i class="fas fa-info-circle"></i>
                Las notas se guardan automáticamente cada 5 segundos. También puedes guardar manualmente usando el botón "Guardar Ahora".
            </small>
        </div>

        {{-- Checklist de Revisión --}}
        <div class="form-group">
            <label>
                <i class="fas fa-tasks"></i>
                Checklist de Revisión
            </label>
            <div class="review-checklist">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="check_format">
                    <label class="custom-control-label" for="check_format">
                        Formato del documento correcto
                    </label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="check_objectives">
                    <label class="custom-control-label" for="check_objectives">
                        Objetivos claros y alcanzables
                    </label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="check_description">
                    <label class="custom-control-label" for="check_description">
                        Descripción completa y coherente
                    </label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="check_evidence">
                    <label class="custom-control-label" for="check_evidence">
                        Evidencias adjuntas y correctas
                    </label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="check_participants">
                    <label class="custom-control-label" for="check_participants">
                        Lista de participantes completa
                    </label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="check_dates">
                    <label class="custom-control-label" for="check_dates">
                        Fechas coherentes y válidas
                    </label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="check_regulations">
                    <label class="custom-control-label" for="check_regulations">
                        Cumple con normativas UP
                    </label>
                </div>
            </div>
        </div>

        {{-- Botones de Acción --}}
        <div class="mt-3">
            <div class="row">
                <div class="col-6">
                    <button type="button" class="btn btn-sm btn-primary btn-block" onclick="saveReviewNotes()">
                        <i class="fas fa-save"></i>
                        Guardar Ahora
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-block" onclick="clearReviewNotes()">
                        <i class="fas fa-eraser"></i>
                        Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
