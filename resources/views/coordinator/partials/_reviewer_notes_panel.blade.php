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
            </label>
            <div class="review-checklist">
                {{-- Campos globales --}}
                <div class="mb-3">
                    <h6 class="text-primary"><i class="fas fa-globe"></i> Campos Globales</h6>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input checklist-item"
                            id="check_format" {{ ($checklist->checklist_data['format_correct'] ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="check_format">
                            Formato del documento correcto
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input checklist-item"
                            id="check_objectives" {{ ($checklist->checklist_data['objectives_clear'] ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="check_objectives">
                            Objetivos claros y alcanzables
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input checklist-item"
                            id="check_description" {{ ($checklist->checklist_data['description_complete'] ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="check_description">
                            Descripción completa y coherente
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input checklist-item"
                            id="check_evidence" {{ ($checklist->checklist_data['evidence_attached'] ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="check_evidence">
                            Evidencias adjuntas y correctas
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input checklist-item"
                            id="check_participants" {{ ($checklist->checklist_data['participants_complete'] ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="check_participants">
                            Lista de participantes completa
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input checklist-item"
                            id="check_dates" {{ ($checklist->checklist_data['dates_coherent'] ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="check_dates">
                            Fechas coherentes y válidas
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input checklist-item"
                            id="check_regulations" {{ ($checklist->checklist_data['regulations_compliant'] ?? false) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="check_regulations">
                            Cumple con normativas UP
                        </label>
                    </div>
                </div>

                {{-- Campos específicos por tipo de trabajo --}}
                @if($work->work_type_id == 1) {{-- Proyecto --}}
                    <div class="mb-3">
                        <h6 class="text-success"><i class="fas fa-project-diagram"></i> Campos Específicos del Proyecto</h6>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_project_category" {{ ($checklist->checklist_data['project_category_valid'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_project_category">
                                Categoría del proyecto válida
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_general_description" {{ ($checklist->checklist_data['general_description_complete'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_general_description">
                                Descripción general completa
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_justification" {{ ($checklist->checklist_data['justification_adequate'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_justification">
                                Justificación adecuada
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_methodology" {{ ($checklist->checklist_data['methodology_clear'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_methodology">
                                Metodología clara
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_scope" {{ ($checklist->checklist_data['scope_defined'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_scope">
                                Alcance definido
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_resource_plan" {{ ($checklist->checklist_data['resource_plan_complete'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_resource_plan">
                                Plan de recursos completo
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_schedule" {{ ($checklist->checklist_data['schedule_realistic'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_schedule">
                                Cronograma realista
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_cost_plan" {{ ($checklist->checklist_data['cost_plan_detailed'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_cost_plan">
                                Plan de costos detallado
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_beneficiaries" {{ ($checklist->checklist_data['beneficiaries_described'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_beneficiaries">
                                Beneficiarios descritos
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_communication_plan" {{ ($checklist->checklist_data['communication_plan_present'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_communication_plan">
                                Plan de comunicación presente
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_institution_relationships" {{ ($checklist->checklist_data['institution_relationships_clear'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_institution_relationships">
                                Vinculación institucional clara
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_final_comments" {{ ($checklist->checklist_data['final_comments_relevant'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_final_comments">
                                Comentarios finales relevantes
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_ss_intervention" {{ ($checklist->checklist_data['ss_intervention_appropriate'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_ss_intervention">
                                Intervención de Servicio Social apropiada
                            </label>
                        </div>
                    </div>
                @elseif($work->work_type_id == 2) {{-- Actividad --}}
                    <div class="mb-3">
                        <h6 class="text-warning"><i class="fas fa-users"></i> Campos Específicos de la Actividad</h6>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_activity_type" {{ ($checklist->checklist_data['activity_type_appropriate'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_activity_type">
                                Tipo de actividad apropiado
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_modality" {{ ($checklist->checklist_data['modality_suitable'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_modality">
                                Modalidad adecuada
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_duration" {{ ($checklist->checklist_data['duration_reasonable'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_duration">
                                Duración razonable
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_introduction" {{ ($checklist->checklist_data['introduction_contextualized'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_introduction">
                                Introducción contextualizada
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_activity_justification" {{ ($checklist->checklist_data['justification_adequate'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_activity_justification">
                                Justificación adecuada
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_activity_objectives" {{ ($checklist->checklist_data['objectives_specific'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_activity_objectives">
                                Objetivos específicos
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_activity_methodology" {{ ($checklist->checklist_data['methodology_detailed'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_activity_methodology">
                                Metodología detallada
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_resources" {{ ($checklist->checklist_data['resources_available'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_resources">
                                Recursos disponibles
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_beneficiaries_profile" {{ ($checklist->checklist_data['beneficiaries_profile_clear'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_beneficiaries_profile">
                                Perfil de beneficiarios claro
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_expected_participants" {{ ($checklist->checklist_data['expected_participants_realistic'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_expected_participants">
                                Participantes esperados realistas
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_activity_relationships" {{ ($checklist->checklist_data['institution_relationships_present'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_activity_relationships">
                                Articulación institucional presente
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_activity_comments" {{ ($checklist->checklist_data['comments_relevant'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_activity_comments">
                                Comentarios relevantes
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_certification" {{ ($checklist->checklist_data['certification_appropriate'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_certification">
                                Certificación apropiada
                            </label>
                        </div>
                    </div>
                @elseif($work->work_type_id == 3) {{-- Publicación --}}
                    <div class="mb-3">
                        <h6 class="text-info"><i class="fas fa-book"></i> Campos Específicos de la Publicación</h6>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_publication_type" {{ ($checklist->checklist_data['publication_type_valid'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_publication_type">
                                Tipo de publicación válido
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_summary" {{ ($checklist->checklist_data['summary_comprehensive'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_summary">
                                Resumen comprehensivo
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_editorial" {{ ($checklist->checklist_data['editorial_reputable'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_editorial">
                                Editorial reputada
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_isbn_issn" {{ ($checklist->checklist_data['isbn_issn_present'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_isbn_issn">
                                ISBN/ISSN presente
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_target_audience" {{ ($checklist->checklist_data['target_audience_defined'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_target_audience">
                                Público objetivo definido
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_relevance" {{ ($checklist->checklist_data['relevance_justified'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_relevance">
                                Relevancia justificada
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_publication_date" {{ ($checklist->checklist_data['publication_date_valid'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_publication_date">
                                Fecha de publicación válida
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_media_type" {{ ($checklist->checklist_data['media_type_appropriate'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_media_type">
                                Tipo de medio apropiado
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_media_nature" {{ ($checklist->checklist_data['media_nature_clear'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_media_nature">
                                Naturaleza del medio clara
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_language" {{ ($checklist->checklist_data['language_appropriate'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_language">
                                Idioma apropiado
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_print_run" {{ ($checklist->checklist_data['print_run_realistic'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_print_run">
                                Tiraje realista
                            </label>
                        </div>
                    </div>
                @elseif($work->work_type_id == 4) {{-- Asistencia Técnica --}}
                    <div class="mb-3">
                        <h6 class="text-secondary"><i class="fas fa-tools"></i> Campos Específicos de Asistencia Técnica</h6>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_assistance_type" {{ ($checklist->checklist_data['assistance_type_valid'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_assistance_type">
                                Tipo de asistencia válido
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_institution" {{ ($checklist->checklist_data['collaborating_institution_clear'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_institution">
                                Institución colaboradora clara
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_specialization" {{ ($checklist->checklist_data['specialization_area_relevant'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_specialization">
                                Área de especialización relevante
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_assistance_description" {{ ($checklist->checklist_data['description_comprehensive'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_assistance_description">
                                Descripción comprehensiva
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_assistance_objectives" {{ ($checklist->checklist_data['objectives_clear'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_assistance_objectives">
                                Objetivos claros
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_assistance_methodology" {{ ($checklist->checklist_data['methodology_detailed'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_assistance_methodology">
                                Metodología detallada
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_expected_products" {{ ($checklist->checklist_data['expected_products_defined'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_expected_products">
                                Productos esperados definidos
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_assistance_evidence" {{ ($checklist->checklist_data['evidence_sufficient'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_assistance_evidence">
                                Evidencias suficientes
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_work_modality" {{ ($checklist->checklist_data['work_modality_appropriate'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_work_modality">
                                Modalidad de trabajo apropiada
                            </label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input checklist-item"
                                id="check_estimated_hours" {{ ($checklist->checklist_data['estimated_hours_realistic'] ?? false) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="check_estimated_hours">
                                Horas estimadas realistas
                            </label>
                        </div>
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
