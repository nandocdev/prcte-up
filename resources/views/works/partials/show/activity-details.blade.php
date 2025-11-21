@if (empty($activity))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        {{ __('No se registraron detalles específicos para esta actividad.') }}
    </div>
@else
@php
    $detailData = $activity->details_json ?? [];
@endphp
<div class="card card-outline card-info">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-chalkboard-teacher"></i>
            {{ __('Detalles específicos de la actividad') }}
        </h3>
        <div>
            <span class="badge badge-primary text-uppercase mr-2">
                {{ $activity->activity_type ? __(ucfirst($activity->activity_type)) : __('Tipo no definido') }}
            </span>
            <span class="badge badge-secondary text-uppercase">
                {{ $activity->modality ? __(ucfirst($activity->modality)) : __('Modalidad no definida') }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row text-center mb-4">
            <div class="col-md-3">
                <h6 class="text-muted text-uppercase">{{ __('Duración (horas)') }}</h6>
                <p class="mb-0 h5">{{ $activity->duration_hours ?? '—' }}</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted text-uppercase">{{ __('Participantes esperados') }}</h6>
                <p class="mb-0 h5">{{ $activity->expected_participants ?? '—' }}</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted text-uppercase">{{ __('Perfil participante') }}</h6>
                <p class="mb-0">{{ $activity->participant_profile ?? __('No definido') }}</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted text-uppercase">{{ __('Certificado') }}</h6>
                <span class="badge badge-{{ $activity->offers_certificate ? 'success' : 'warning' }}">
                    {{ $activity->offers_certificate ? __('Se emite certificado') : __('Sin certificación') }}
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="text-primary">{{ __('Introducción y justificación') }}</h5>
                <p>{{ data_get($detailData, 'introduction') ?? __('Sin introducción registrada') }}</p>
                <p>{{ data_get($detailData, 'justification') ?? __('Sin justificación registrada') }}</p>
            </div>
            <div class="col-md-6">
                <h5 class="text-primary">{{ __('Objetivos y metodología') }}</h5>
                <p>{{ data_get($detailData, 'objectives') ?? __('Sin objetivos registrados') }}</p>
                <p>{{ data_get($detailData, 'methodology') ?? __('Sin metodología registrada') }}</p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Recursos y beneficiarios') }}</h6>
                <p class="mb-2">{{ data_get($detailData, 'resources') ?? __('Sin recursos detallados') }}</p>
                <p class="mb-0">{{ data_get($detailData, 'beneficiaries') ?? __('Sin beneficiarios descritos') }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Alianzas y comentarios') }}</h6>
                <p class="mb-2">{{ data_get($detailData, 'institution_relationships') ?? __('Sin alianzas registradas') }}</p>
                <p class="mb-0">{{ data_get($detailData, 'comments') ?? __('Sin comentarios adicionales') }}</p>
            </div>
        </div>
    </div>
</div>
@endif
