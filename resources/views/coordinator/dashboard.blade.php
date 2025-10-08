@extends('adminlte::page')

@section('title', 'Dashboard - Coordinador de Extensión')

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>Dashboard - Coordinador de Extensión</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <!-- Estadísticas -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $statistics['pending'] }}</h3>
                <p>Trabajos Pendientes</p>
            </div>
            <div class="icon">
                <i class="ion ion-clock"></i>
            </div>
            <a href="#pending-works" class="small-box-footer"
                onclick="document.getElementById('pending-works').scrollIntoView();">
                Ver trabajos <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $statistics['approved_this_month'] }}</h3>
                <p>Aprobados este mes</p>
            </div>
            <div class="icon">
                <i class="ion ion-checkmark"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $statistics['changes_requested_this_month'] }}</h3>
                <p>Subsanaciones este mes</p>
            </div>
            <div class="icon">
                <i class="ion ion-edit"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $statistics['total_unit_works'] }}</h3>
                <p>Total trabajos unidad</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
        </div>
    </div>
</div>

<!-- Trabajos Pendientes de Revisión -->
<div class="row" id="pending-works">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-clock mr-1"></i>
                    Trabajos Pendientes de Revisión ({{ $pendingWorks->count() }})
                </h3>
            </div>
            <div class="card-body">
                @if($pendingWorks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Profesor</th>
                                    <th>Fecha Envío</th>
                                    <th>Días Pendiente</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingWorks as $work)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::limit($work->getAttribute('title'), 50) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $work->workType->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $work->responsibleUser->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($work->getAttribute('submitted_at'))
                                                {{ $work->getAttribute('submitted_at')->format('d/m/Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($work->getAttribute('submitted_at'))
                                                @php
                                                    $daysPending = $work->getAttribute('submitted_at')->diffInDays(now());
                                                @endphp
                                                <span
                                                    class="badge badge-{{ $daysPending > 5 ? 'danger' : ($daysPending > 2 ? 'warning' : 'success') }}">
                                                    {{ $daysPending }} días
                                                </span>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('coordinator.show', $work) }}" class="btn btn-sm btn-primary"
                                                title="Revisar trabajo">
                                                <i class="fas fa-eye"></i> Revisar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3"></i>
                        <p>No hay trabajos pendientes de revisión en este momento.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Trabajos Recientes -->
@if($recentWorks->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-1"></i>
                        Trabajos Procesados Recientemente
                    </h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Profesor</th>
                                    <th>Estado Actual</th>
                                    <th>Última Actualización</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentWorks->take(10) as $work)
                                    <tr>
                                        <td>{{ Str::limit($work->getAttribute('title'), 40) }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $work->workType->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $work->responsibleUser->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($work->currentStatus)
                                                @php
                                                    $statusClass = match ($work->currentStatus->getAttribute('name')) {
                                                        'Enviado a Decano/Director' => 'badge-success',
                                                        'Devuelto para Corrección' => 'badge-warning',
                                                        'Certificado' => 'badge-primary',
                                                        default => 'badge-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusClass }}">
                                                    {{ $work->currentStatus->getAttribute('name') }}
                                                </span>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>{{ $work->updated_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('coordinator.show', $work) }}"
                                                class="btn btn-xs btn-outline-primary" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@stop

@section('css')
<style>
    .small-box h3 {
        font-size: 2.2rem;
    }

    .table th {
        border-top: none;
    }

    .badge {
        font-size: 0.875em;
    }
</style>
@stop

@section('js')
<script>
    $(document).ready(function () {
        // Auto-refresh cada 5 minutos para trabajos pendientes
        setTimeout(function () {
            location.reload();
        }, 300000);

        // Tooltip para badges de días pendientes
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@stop
