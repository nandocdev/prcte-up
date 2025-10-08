{{-- Detalles Específicos del Proyecto --}}
<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-project-diagram mr-2"></i>
            Detalles del Proyecto
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
                    <i class="fas fa-layer-group text-primary"></i>
                    Tipo de Proyecto
                </strong>
                <p class="text-muted">
                    {{ $work->projectDetails->getAttribute('project_type') ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>
                    <i class="fas fa-book text-success"></i>
                    Área de Conocimiento
                </strong>
                <p class="text-muted">
                    {{ $work->projectDetails->getAttribute('knowledge_area') ?? 'N/A' }}
                </p>
            </div>
        </div>

        @if($work->projectDetails->getAttribute('justification'))
            <div class="mb-3">
                <strong>
                    <i class="fas fa-question-circle text-warning"></i>
                    Justificación del Proyecto
                </strong>
                <div class="alert alert-light mt-2">
                    <p style="white-space: pre-wrap;">{{ $work->projectDetails->getAttribute('justification') }}</p>
                </div>
            </div>
        @endif

        @if($work->projectDetails->getAttribute('beneficiaries'))
            <div class="mb-3">
                <strong>
                    <i class="fas fa-hands-helping text-info"></i>
                    Beneficiarios del Proyecto
                </strong>
                <div class="alert alert-light mt-2">
                    <p style="white-space: pre-wrap;">{{ $work->projectDetails->getAttribute('beneficiaries') }}</p>
                </div>
            </div>
        @endif

        @if($work->projectDetails->getAttribute('expected_results'))
            <div class="mb-3">
                <strong>
                    <i class="fas fa-chart-line text-success"></i>
                    Resultados Esperados
                </strong>
                <div class="alert alert-success mt-2">
                    <p style="white-space: pre-wrap;">{{ $work->projectDetails->getAttribute('expected_results') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
