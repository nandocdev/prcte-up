@if (empty($project))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        {{ __('No se registraron detalles específicos para este proyecto.') }}
    </div>
@else
@php
    $detailData = $project->details_json ?? [];
    $schedule = data_get($project->schedule_json, 'schedule');
    $resourcePlan = data_get($project->resources_json, 'resources');
    $costPlan = data_get($project->costs_json, 'cost_plan');
@endphp
<div class="card card-outline card-primary">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-project-diagram"></i>
            {{ __('Detalles específicos del proyecto') }}
        </h3>
        <span class="badge badge-info text-uppercase">
            {{ $project->project_category ? __(ucfirst(str_replace('_', ' ', $project->project_category))) : __('Categoría no definida') }}
        </span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <h6 class="text-muted text-uppercase">{{ __('Tipo institucional') }}</h6>
                <p class="mb-3">
                    {{ $project->institutionalProjectType->name ?? __('No aplica') }}
                </p>
            </div>
            <div class="col-md-4">
                <h6 class="text-muted text-uppercase">{{ __('Beneficiarios directos') }}</h6>
                <p class="mb-3">
                    {{ $project->direct_beneficiaries ?? __('No registrado') }}
                </p>
            </div>
            <div class="col-md-4">
                <h6 class="text-muted text-uppercase">{{ __('Beneficiarios indirectos') }}</h6>
                <p class="mb-3">
                    {{ $project->indirect_beneficiaries ?? __('No registrado') }}
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Área geográfica') }}</h6>
                <p class="mb-3">{{ $project->geographic_area ?? __('No especificado') }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Resumen de intervención (Servicio Social)') }}</h6>
                <p class="mb-3">{{ $project->ss_intervention_summary ?? __('No aplica') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="text-primary">{{ __('Objetivos del proyecto') }}</h5>
                <p>{{ $project->objectives ?? data_get($detailData, 'general_description', __('No registrado')) }}</p>
            </div>
            <div class="col-md-6">
                <h5 class="text-primary">{{ __('Metodología propuesta') }}</h5>
                <p>{{ $project->methodology ?? __('No registrado') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Alcance y plan de recursos') }}</h6>
                <p class="mb-3">{{ data_get($detailData, 'project_scope') ?? __('No registrado') }}</p>
                <p class="mb-3">{{ $resourcePlan ?? data_get($detailData, 'resource_plan', __('No registrado')) }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Cronograma y costos estimados') }}</h6>
                <p class="mb-3">{{ $schedule ?? __('Sin cronograma capturado') }}</p>
                <p class="mb-0">{{ $costPlan ?? __('Sin plan de costos capturado') }}</p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Beneficiarios y comunicación') }}</h6>
                <p class="mb-2">{{ data_get($detailData, 'beneficiaries_description') ?? __('No registrado') }}</p>
                <p class="mb-0">{{ data_get($detailData, 'communication_plan') ?? __('Sin plan de comunicación') }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Articulación institucional') }}</h6>
                <p class="mb-2">{{ data_get($detailData, 'institution_relationships') ?? __('No especificado') }}</p>
                <p class="mb-0">{{ data_get($detailData, 'final_comments') ?? __('Sin comentarios adicionales') }}</p>
            </div>
        </div>
    </div>
</div>
@endif
