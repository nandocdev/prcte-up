{{-- Detalles Generales del Trabajo --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-info-circle mr-2"></i>
            Información General del Trabajo
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        {{-- Fechas --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <strong>
                    <i class="fas fa-calendar-check text-primary"></i>
                    Fecha de Envío
                </strong>
                <p class="text-muted">
                    {{ $work->getAttribute('submitted_at')?->format('d/m/Y H:i') ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-4">
                <strong>
                    <i class="fas fa-calendar-alt text-success"></i>
                    Fecha de Inicio
                </strong>
                <p class="text-muted">
                    {{ $work->getAttribute('start_date')?->format('d/m/Y') ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-4">
                <strong>
                    <i class="fas fa-calendar-times text-danger"></i>
                    Fecha de Finalización
                </strong>
                <p class="text-muted">
                    {{ $work->getAttribute('end_date')?->format('d/m/Y') ?? 'N/A' }}
                </p>
            </div>
        </div>

        {{-- Duración --}}
        @if($work->getAttribute('start_date') && $work->getAttribute('end_date'))
            <div class="row mb-3">
                <div class="col-md-12">
                    @php
                        $duration = $work->getAttribute('start_date')->diffInDays($work->getAttribute('end_date'));
                    @endphp
                    <div class="alert alert-info">
                        <i class="fas fa-clock"></i>
                        <strong>Duración del trabajo:</strong>
                        {{ $duration }} días
                        ({{ number_format($duration / 30, 1) }} meses aproximadamente)
                    </div>
                </div>
            </div>
        @endif

        {{-- Participantes --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <strong>
                    <i class="fas fa-users text-info"></i>
                    Número de Participantes
                </strong>
                <p class="text-muted">
                    {{ $work->getAttribute('participants_count') ?? 0 }} personas
                </p>
            </div>
            <div class="col-md-6">
                <strong>
                    <i class="fas fa-tag text-warning"></i>
                    Tipo de Trabajo
                </strong>
                <p class="text-muted">
                    {{ $work->workType->name ?? 'N/A' }}
                </p>
            </div>
        </div>

        <hr>

        {{-- Descripción --}}
        <div class="mb-3">
            <h5>
                <i class="fas fa-align-left text-primary"></i>
                Descripción del Trabajo
            </h5>
            <div class="callout callout-info">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $work->getAttribute('description') ?? 'Sin descripción disponible' }}</p>
            </div>
        </div>

        {{-- Objetivos --}}
        @if($work->getAttribute('objectives'))
            <div class="mb-3">
                <h5>
                    <i class="fas fa-bullseye text-success"></i>
                    Objetivos
                </h5>
                <div class="callout callout-success">
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $work->getAttribute('objectives') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
