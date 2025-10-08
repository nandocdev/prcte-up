{{-- Detalles Específicos de la Actividad --}}
<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tasks mr-2"></i>
            Detalles de la Actividad
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <strong>
                    <i class="fas fa-clipboard-list text-primary"></i>
                    Tipo de Actividad
                </strong>
                <p class="text-muted">
                    {{ $work->activityDetails->getAttribute('activity_type') ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>
                    <i class="fas fa-users text-info"></i>
                    Público Objetivo
                </strong>
                <p class="text-muted">
                    {{ $work->activityDetails->getAttribute('target_audience') ?? 'N/A' }}
                </p>
            </div>
        </div>

        @if($work->activityDetails->getAttribute('modality'))
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>
                        <i class="fas fa-laptop-house text-warning"></i>
                        Modalidad
                    </strong>
                    <p class="text-muted">
                        {{ $work->activityDetails->getAttribute('modality') }}
                    </p>
                </div>
                @if($work->activityDetails->getAttribute('location'))
                    <div class="col-md-6 mb-3">
                        <strong>
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            Ubicación
                        </strong>
                        <p class="text-muted">
                            {{ $work->activityDetails->getAttribute('location') }}
                        </p>
                    </div>
                @endif
            </div>
        @endif

        @if($work->activityDetails->getAttribute('methodology'))
            <div class="mb-3">
                <strong>
                    <i class="fas fa-chalkboard-teacher text-success"></i>
                    Metodología
                </strong>
                <div class="alert alert-light mt-2">
                    <p style="white-space: pre-wrap;">{{ $work->activityDetails->getAttribute('methodology') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
