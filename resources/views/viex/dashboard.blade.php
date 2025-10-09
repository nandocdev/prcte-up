@extends('layouts.app')

@section('title', 'Dashboard VIEX')

@push('styles')
<style>
    .stat-card {
        border-left: 4px solid;
    }

    .stat-card.pending {
        border-left-color: #ffc107;
    }

    .stat-card.in-evaluation {
        border-left-color: #17a2b8;
    }

    .stat-card.approved {
        border-left-color: #28a745;
    }

    .stat-card.rejected {
        border-left-color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">{{ __('Dashboard VIEX') }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Dashboard VIEX') }}</li>
        </ol>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Estadísticas Generales -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $statistics['pending_review'] }}</h3>
                        <p>{{ __('Pendientes de Asignación') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('viex.index', ['status' => 'pending']) }}" class="small-box-footer">
                        {{ __('Ver trabajos') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $statistics['in_evaluation'] }}</h3>
                        <p>{{ __('En Evaluación') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <a href="{{ route('viex.index', ['status' => 'evaluation']) }}" class="small-box-footer">
                        {{ __('Ver evaluaciones') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $statistics['certified_this_month'] }}</h3>
                        <p>{{ __('Certificados Este Mes') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="{{ route('viex.index', ['status' => 'certified']) }}" class="small-box-footer">
                        {{ __('Ver certificados') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $statistics['total_received'] }}</h3>
                        <p>{{ __('Total Recibidos') }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <a href="{{ route('viex.index') }}" class="small-box-footer">
                        {{ __('Ver todos') }} <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Trabajos Recientes -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Trabajos Recientes en VIEX') }}</h3>
                    </div>
                    <div class="card-body p-0">
                        @if($recentWorks->isEmpty())
                        <div class="text-center p-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('No hay trabajos recientes') }}</p>
                        </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Título') }}</th>
                                        <th>{{ __('Tipo') }}</th>
                                        <th>{{ __('Profesor') }}</th>
                                        <th>{{ __('Unidad') }}</th>
                                        <th>{{ __('Estado') }}</th>
                                        <th>{{ __('Fecha') }}</th>
                                        <th>{{ __('Acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentWorks as $work)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::limit($work->title, 40) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary">{{ $work->workType->name }}</span>
                                        </td>
                                        <td>{{ $work->primaryResponsible->full_name }}</td>
                                        <td>{{ Str::limit($work->organizationalUnit->name, 30) }}</td>
                                        <td>
                                            @php
                                            $statusColors = [
                                            'En VIEX - Pendiente Asignación' => 'warning',
                                            'En VIEX - En Evaluación' => 'info',
                                            'En VIEX - Aprobado' => 'success',
                                            'Certificado' => 'primary',
                                            'Rechazado por VIEX' => 'danger',
                                            ];
                                            $color = $statusColors[$work->currentStatus->name] ?? 'secondary';
                                            @endphp
                                            <span
                                                class="badge badge-{{ $color }}">{{ $work->currentStatus->name }}</span>
                                        </td>
                                        <td>{{ $work->updated_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('viex.show', $work) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                    @if($recentWorks->isNotEmpty())
                    <div class="card-footer">
                        <a href="{{ route('viex.index') }}" class="btn btn-primary">
                            {{ __('Ver todos los trabajos') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Panel de Evaluadores -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Comisión de Evaluación') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-box">
                            <span class="info-box-icon bg-info">
                                <i class="fas fa-users"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Evaluadores Activos') }}</span>
                                <span class="info-box-number">{{ $evaluatorCount }}</span>
                            </div>
                        </div>

                        <div class="info-box">
                            <span class="info-box-icon bg-warning">
                                <i class="fas fa-tasks"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{ __('Evaluaciones Pendientes') }}</span>
                                <span class="info-box-number">{{ $statistics['in_evaluation'] }}</span>
                            </div>
                        </div>

                        <a href="{{ route('viex.index', ['status' => 'evaluation']) }}"
                            class="btn btn-primary btn-block">
                            {{ __('Gestionar Evaluaciones') }}
                        </a>
                    </div>
                </div>

                <!-- Panel de Acciones Rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Acciones Rápidas') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <a href="{{ route('viex.index', ['status' => 'pending']) }}"
                                    class="btn btn-warning btn-block">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    {{ __('Asignar Evaluadores') }}
                                </a>
                            </div>
                            <div class="col-12 mb-2">
                                <a href="{{ route('viex.index', ['status' => 'approved']) }}"
                                    class="btn btn-success btn-block">
                                    <i class="fas fa-certificate mr-2"></i>
                                    {{ __('Generar Certificaciones') }}
                                </a>
                            </div>
                            <div class="col-12">
                                <a href="{{ route('viex.index') }}" class="btn btn-info btn-block">
                                    <i class="fas fa-list mr-2"></i>
                                    {{ __('Ver Todos los Trabajos') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-refresh cada 5 minutos para mantener estadísticas actualizadas
        setTimeout(function() {
            window.location.reload();
        }, 300000);
    });
</script>
@endpush