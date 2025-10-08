{{-- Encabezado del Trabajo --}}
<div class="row">
    <div class="col-sm-6">
        <h1>
            <i class="fas fa-clipboard-check mr-2"></i>
            Revisar Trabajo de Extensión
        </h1>
        <p class="text-muted">
            <i class="fas fa-tag"></i>
            {{ $work->workType->name ?? 'N/A' }}
        </p>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
                <a href="{{ route('coordinator.dashboard') }}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active">Revisar Trabajo</li>
        </ol>
    </div>
</div>

{{-- Título y Estado del Trabajo --}}
<div class="row mt-2">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-2"></i>
                    {{ $work->getAttribute('title') }}
                </h3>
                <div class="card-tools">
                    @if($work->currentStatus)
                        @php
                            $statusClass = match ($work->currentStatus->getAttribute('name')) {
                                'En Revisión Coordinador', 'Enviado a Coordinador' => 'badge-warning',
                                'Enviado a Decano/Director' => 'badge-success',
                                'Devuelto para Corrección' => 'badge-info',
                                'Rechazado por Coordinador' => 'badge-danger',
                                default => 'badge-secondary'
                            };
                        @endphp
                        <span class="badge badge-lg {{ $statusClass }}">
                            <i class="fas fa-circle mr-1"></i>
                            {{ $work->currentStatus->getAttribute('name') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
