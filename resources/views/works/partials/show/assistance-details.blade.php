@if (empty($assistance))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        {{ __('No se registraron detalles específicos para esta asistencia técnica.') }}
    </div>
@else
@php
    $detailData = $assistance->details_json ?? [];
@endphp
<div class="card card-outline card-success">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-hands-helping"></i>
            {{ __('Detalles específicos de la asistencia técnica') }}
        </h3>
        <div>
            <span class="badge badge-success text-uppercase mr-2">
                {{ $assistance->assistance_type ? __(ucfirst($assistance->assistance_type)) : __('Tipo no definido') }}
            </span>
            <span class="badge badge-secondary text-uppercase">
                {{ $assistance->work_modality ? __(ucfirst($assistance->work_modality)) : __('Modalidad no definida') }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <h6 class="text-muted text-uppercase">{{ __('Institución beneficiaria') }}</h6>
                <p class="mb-0">{{ $assistance->collaborating_institution ?? __('No registrada') }}</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-muted text-uppercase">{{ __('Área de especialización') }}</h6>
                <p class="mb-0">{{ $assistance->specialization_area ?? __('No definida') }}</p>
            </div>
            <div class="col-md-4">
                <h6 class="text-muted text-uppercase">{{ __('Horas estimadas') }}</h6>
                <p class="mb-0 h5">{{ $assistance->estimated_hours ?? '—' }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="text-primary">{{ __('Descripción y objetivos') }}</h5>
                <p>{{ data_get($detailData, 'description') ?? __('Sin descripción registrada') }}</p>
                <p>{{ data_get($detailData, 'objectives') ?? __('Sin objetivos registrados') }}</p>
            </div>
            <div class="col-md-6">
                <h5 class="text-primary">{{ __('Metodología y evidencias') }}</h5>
                <p>{{ data_get($detailData, 'methodology') ?? __('Sin metodología registrada') }}</p>
                <p>{{ data_get($detailData, 'evidence') ?? __('Sin evidencias registradas') }}</p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Productos esperados') }}</h6>
                <p class="mb-0">{{ $assistance->expected_products ?? __('No definidos') }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Comentarios adicionales') }}</h6>
                <p class="mb-0">{{ data_get($detailData, 'comments') ?? __('Sin comentarios') }}</p>
            </div>
        </div>
    </div>
</div>
@endif
