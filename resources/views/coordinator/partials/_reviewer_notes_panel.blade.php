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
                placeholder="Escribe aquí tus observaciones durante la revisión del trabajo...&#10;&#10;Estas notas se guardan automáticamente en la base de datos.">{{ $checklist->reviewer_notes ?? '' }}</textarea>
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
                @if($checklist->getProgressPercentage() > 0)
                    <span class="badge badge-info ml-2">{{ $checklist->getProgressPercentage() }}% completado</span>
                @endif
                @if(!$checklistComplete)
                    <span class="badge badge-warning ml-2">Requiere completar para aprobar</span>
                @endif
            </label>
            <div class="review-checklist">
                {{-- Checklist dinámico basado en criterios del Manual --}}
                @if($checklistCriteria && $checklistCriteria->count() > 0)
                    @foreach($checklistCriteria->groupBy('category') as $category => $criteria)
                        <div class="mb-3">
                            <h6 class="text-primary">
                                <i class="fas fa-check-circle"></i>
                                {{ $category }}
                                <small class="text-muted">({{ $criteria->count() }} criterios)</small>
                            </h6>
                            @foreach($criteria as $criterion)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox"
                                           class="custom-control-input checklist-item"
                                           id="check_{{ $criterion->criteria_key }}"
                                           {{ ($checklist->checklist_data[$criterion->criteria_key] ?? false) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="check_{{ $criterion->criteria_key }}">
                                        {{ $criterion->name }}
                                        @if($criterion->description)
                                            <small class="text-muted d-block">{{ $criterion->description }}</small>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>No hay criterios definidos:</strong>
                        No se encontraron criterios de evaluación para este tipo de trabajo.
                        Contacte al administrador del sistema.
                    </div>
                @endif
            </div>
        </div>

        {{-- Botones de Acción --}}
        <div class="mt-3">
            <div class="row">
                <div class="col-6">
                    <button type="button" class="btn btn-sm btn-primary btn-block" onclick="saveChecklist()">
                        <i class="fas fa-save"></i>
                        Guardar Ahora
                    </button>
                </div>
                <div class="col-6">
                    <button type="button" class="btn btn-sm btn-outline-secondary btn-block" onclick="clearChecklist()">
                        <i class="fas fa-eraser"></i>
                        Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
