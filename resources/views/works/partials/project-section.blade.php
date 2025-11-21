<!-- Sección 2: Específica para Proyectos de Extensión -->
@php
$projectDetail = $projectDetail ?? null;
$workTypesConfig = $workTypesConfig ?? config('work_types', []);
$projectCategories = $workTypesConfig['project_categories'] ?? [];
$institutionalProjectTypes = $institutionalProjectTypes ?? collect();
$projectDetailsData = is_array(optional($projectDetail)->details_json)
? $projectDetail->details_json
: [];
$scheduleData = data_get(optional($projectDetail)->schedule_json ?? [], 'schedule');
$resourceData = data_get(optional($projectDetail)->resources_json ?? [], 'resources')
?? data_get($projectDetailsData, 'resource_plan');
$costData = data_get(optional($projectDetail)->costs_json ?? [], 'cost_plan');
$communicationPlan = $projectDetailsData['communication_plan']
?? $projectDetailsData['community_plan']
?? null;
$beneficiariesDescription = $projectDetailsData['beneficiaries_description'] ?? null;
$institutionRelationships = $projectDetailsData['institution_relationships'] ?? null;
$finalComments = $projectDetailsData['final_comments'] ?? null;
$generalDescription = $projectDetailsData['general_description'] ?? null;
$justification = $projectDetailsData['justification'] ?? null;
$projectScope = $projectDetailsData['project_scope'] ?? null;
$ssSummary = optional($projectDetail)->ss_intervention_summary
?? ($projectDetailsData['ss_intervention_summary'] ?? null);
@endphp

<div class="card card-success work-section" id="section-proyecto" style="display: none;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-project-diagram"></i>
            {{ __('2. Información Específica del Proyecto') }}
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="project_category">
                        <strong>{{ __('Categoría del Proyecto') }}</strong> <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('project_category') is-invalid @enderror"
                        id="project_category" name="project_category" data-required="true">
                        <option value="">{{ __('Seleccione...') }}</option>
                        @foreach($projectCategories as $value => $label)
                        <option value="{{ $value }}" {{ old('project_category', optional($projectDetail)->project_category ?? 'general') == $value ? 'selected' : '' }}>
                            {{ __($label) }}
                        </option>
                        @endforeach
                    </select>
                    @error('project_category')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="institutional_project_type_id">
                        <strong>{{ __('Tipo de Proyecto Institucional') }}</strong>
                    </label>
                    <select class="form-control @error('institutional_project_type_id') is-invalid @enderror"
                        id="institutional_project_type_id" name="institutional_project_type_id">
                        <option value="">{{ __('Seleccione (solo si aplica)...') }}</option>
                        @foreach($institutionalProjectTypes as $projectType)
                        <option value="{{ $projectType->id }}" {{ old('institutional_project_type_id', optional($projectDetail)->institutional_project_type_id) == $projectType->id ? 'selected' : '' }}>
                            {{ $projectType->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('institutional_project_type_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="project_general_description">
                <strong>{{ __('Descripción General del Proyecto') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('project_general_description') is-invalid @enderror"
                id="project_general_description" name="project_general_description" rows="3" data-required="true"
                placeholder="{{ __('Explique la naturaleza, contexto y alcance general del proyecto...') }}">{{ old('project_general_description', $generalDescription) }}</textarea>
            @error('project_general_description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="project_justification">
                <strong>{{ __('Justificación') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('project_justification') is-invalid @enderror" id="project_justification"
                name="project_justification" rows="3" data-required="true"
                placeholder="{{ __('Argumente la necesidad del proyecto y su alineación institucional...') }}">{{ old('project_justification', $justification) }}</textarea>
            @error('project_justification')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="objectives">
                <strong>{{ __('Objetivos del Proyecto') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('objectives') is-invalid @enderror" id="objectives"
                name="objectives" rows="3" data-required="true"
                placeholder="{{ __('Describa los objetivos general y específicos del proyecto...') }}">{{ old('objectives', optional($projectDetail)->objectives) }}</textarea>
            @error('objectives')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="methodology">
                <strong>{{ __('Metodología') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('methodology') is-invalid @enderror" id="methodology"
                name="methodology" rows="3" data-required="true"
                placeholder="{{ __('Describa la metodología a utilizar en el proyecto...') }}">{{ old('methodology', optional($projectDetail)->methodology) }}</textarea>
            @error('methodology')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="project_scope">
                        <strong>{{ __('Alcance / Cobertura') }}</strong> <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('project_scope') is-invalid @enderror" id="project_scope"
                        name="project_scope" rows="3" data-required="true"
                        placeholder="{{ __('Detalle la cobertura territorial o poblacional...') }}">{{ old('project_scope', $projectScope) }}</textarea>
                    @error('project_scope')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="geographic_area">
                        <strong>{{ __('Área Geográfica de Intervención') }}</strong>
                    </label>
                    <input type="text" class="form-control @error('geographic_area') is-invalid @enderror"
                        id="geographic_area" name="geographic_area"
                        value="{{ old('geographic_area', optional($projectDetail)->geographic_area) }}"
                        placeholder="{{ __('Municipio, departamento, región...') }}">
                    @error('geographic_area')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="project_resource_plan">
                        <strong>{{ __('Plan de Recursos') }}</strong> <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('project_resource_plan') is-invalid @enderror"
                        id="project_resource_plan" name="project_resource_plan" rows="3" data-required="true"
                        placeholder="{{ __('Detalle recursos humanos, materiales y financieros requeridos...') }}">{{ old('project_resource_plan', $resourceData) }}</textarea>
                    @error('project_resource_plan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="project_schedule">
                        <strong>{{ __('Cronograma General') }}</strong> <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('project_schedule') is-invalid @enderror" id="project_schedule"
                        name="project_schedule" rows="3" data-required="true"
                        placeholder="{{ __('Describa las fases principales y sus fechas...') }}">{{ old('project_schedule', $scheduleData) }}</textarea>
                    @error('project_schedule')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="project_cost_plan">
                        <strong>{{ __('Presupuesto / Plan de Costos') }}</strong> <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('project_cost_plan') is-invalid @enderror" id="project_cost_plan"
                        name="project_cost_plan" rows="3" data-required="true"
                        placeholder="{{ __('Detalle los rubros principales de inversión...') }}">{{ old('project_cost_plan', $costData) }}</textarea>
                    @error('project_cost_plan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="project_beneficiaries">
                        <strong>{{ __('Descripción de Beneficiarios') }}</strong> <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('project_beneficiaries') is-invalid @enderror"
                        id="project_beneficiaries" name="project_beneficiaries" rows="3" data-required="true"
                        placeholder="{{ __('Explique quiénes son los beneficiarios y cómo se seleccionan...') }}">{{ old('project_beneficiaries', $beneficiariesDescription) }}</textarea>
                    @error('project_beneficiaries')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="project_communication_plan">
                        <strong>{{ __('Plan de Comunicación / Difusión') }}</strong>
                    </label>
                    <textarea class="form-control @error('project_communication_plan') is-invalid @enderror"
                        id="project_communication_plan" name="project_communication_plan" rows="3"
                        placeholder="{{ __('Describa las acciones de divulgación y comunicación...') }}">{{ old('project_communication_plan', $communicationPlan) }}</textarea>
                    @error('project_communication_plan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="project_institution_relationships">
                        <strong>{{ __('Vinculación con Instituciones Externas') }}</strong>
                    </label>
                    <textarea class="form-control @error('project_institution_relationships') is-invalid @enderror"
                        id="project_institution_relationships" name="project_institution_relationships" rows="3"
                        placeholder="{{ __('Detalle convenios o alianzas relevantes...') }}">{{ old('project_institution_relationships', $institutionRelationships) }}</textarea>
                    @error('project_institution_relationships')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="project_final_comments">
                <strong>{{ __('Comentarios Finales / Observaciones') }}</strong>
            </label>
            <textarea class="form-control @error('project_final_comments') is-invalid @enderror"
                id="project_final_comments" name="project_final_comments" rows="3"
                placeholder="{{ __('Incluya notas adicionales relevantes para la evaluación...') }}">{{ old('project_final_comments', $finalComments) }}</textarea>
            @error('project_final_comments')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="ss_intervention_summary">
                <strong>{{ __('Resumen de Intervención (Servicio Social)') }}</strong>
            </label>
            <textarea class="form-control @error('ss_intervention_summary') is-invalid @enderror"
                id="ss_intervention_summary" name="ss_intervention_summary" rows="3"
                placeholder="{{ __('Describa brevemente la intervención cuando aplique Servicio Social...') }}">{{ old('ss_intervention_summary', $ssSummary) }}</textarea>
            @error('ss_intervention_summary')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="direct_beneficiaries">
                        <strong>{{ __('Beneficiarios Directos (número)') }}</strong>
                    </label>
                    <input type="number" class="form-control @error('direct_beneficiaries') is-invalid @enderror"
                        id="direct_beneficiaries" name="direct_beneficiaries" min="0"
                        value="{{ old('direct_beneficiaries', optional($projectDetail)->direct_beneficiaries) }}"
                        placeholder="{{ __('Número estimado') }}">
                    @error('direct_beneficiaries')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="indirect_beneficiaries">
                        <strong>{{ __('Beneficiarios Indirectos (número)') }}</strong>
                    </label>
                    <input type="number" class="form-control @error('indirect_beneficiaries') is-invalid @enderror"
                        id="indirect_beneficiaries" name="indirect_beneficiaries" min="0"
                        value="{{ old('indirect_beneficiaries', optional($projectDetail)->indirect_beneficiaries) }}"
                        placeholder="{{ __('Número estimado') }}">
                    @error('indirect_beneficiaries')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>