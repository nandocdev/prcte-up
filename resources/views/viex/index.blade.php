@extends('layouts.app')

@section('title', 'Gestión VIEX - Trabajos de Extensión')

@push('styles')
    <style>
        .filter-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ __('Gestión VIEX') }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('viex.dashboard') }}">{{ __('Dashboard VIEX') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Trabajos de Extensión') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card filter-card">
                            <div class="card-body">
                                <form method="GET" action="{{ route('viex.index') }}" id="filterForm">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label for="status">{{ __('Estado') }}</label>
                                            <select name="status" id="status" class="form-control"
                                                onchange="document.getElementById('filterForm').submit();">
                                                <option value="">{{ __('Todos los estados') }}</option>
                                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                                    {{ __('Pendientes de Evaluación') }}
                                                </option>
                                                <option value="evaluation" {{ request('status') === 'evaluation' ? 'selected' : '' }}>
                                                    {{ __('En Evaluación') }}
                                                </option>
                                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
                                                    {{ __('Aprobados') }}
                                                </option>
                                                <option value="certified" {{ request('status') === 'certified' ? 'selected' : '' }}>
                                                    {{ __('Certificados') }}
                                                </option>
                                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
                                                    {{ __('Rechazados') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="work_type">{{ __('Tipo de Trabajo') }}</label>
                                            <select name="work_type" id="work_type" class="form-control"
                                                onchange="document.getElementById('filterForm').submit();">
                                                <option value="">{{ __('Todos los tipos') }}</option>
                                                @foreach($workTypes as $type)
                                                    <option value="{{ $type->id }}" {{ request('work_type') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="unit">{{ __('Unidad Académica') }}</label>
                                            <select name="unit" id="unit" class="form-control"
                                                onchange="document.getElementById('filterForm').submit();">
                                                <option value="">{{ __('Todas las unidades') }}</option>
                                                @foreach($organizationalUnits as $unit)
                                                    <option value="{{ $unit->id }}" {{ request('unit') == $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="search">{{ __('Buscar') }}</label>
                                            <div class="input-group">
                                                <input type="text" name="search" id="search" class="form-control"
                                                    placeholder="{{ __('Título o profesor...') }}"
                                                    value="{{ request('search') }}">
                                                <div class="input-group-append">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Trabajos -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    {{ __('Trabajos de Extensión en VIEX') }}
                                    <span class="badge badge-secondary ml-2">{{ $works->total() }}</span>
                                </h3>
                                <div class="card-tools">
                                    <a href="{{ route('viex.dashboard') }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-chart-bar mr-1"></i>
                                        {{ __('Dashboard') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                @if($works->isEmpty())
                                    <div class="text-center p-5">
                                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                                        <h4 class="text-muted">{{ __('No se encontraron trabajos') }}</h4>
                                        <p class="text-muted">
                                            @if(request()->anyFilled(['status', 'work_type', 'unit', 'search']))
                                                {{ __('No hay trabajos que coincidan con los filtros aplicados.') }}
                                            @else
                                                {{ __('No hay trabajos de extensión en VIEX actualmente.') }}
                                            @endif
                                        </p>
                                        @if(request()->anyFilled(['status', 'work_type', 'unit', 'search']))
                                            <a href="{{ route('viex.index') }}" class="btn btn-primary">
                                                {{ __('Ver todos los trabajos') }}
                                            </a>
                                        @endif
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="25%">{{ __('Título') }}</th>
                                                    <th width="12%">{{ __('Tipo') }}</th>
                                                    <th width="15%">{{ __('Profesor') }}</th>
                                                    <th width="15%">{{ __('Unidad') }}</th>
                                                    <th width="15%">{{ __('Estado') }}</th>
                                                    <th width="8%">{{ __('Fecha') }}</th>
                                                    <th width="5%">{{ __('Acciones') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($works as $work)
                                                    <tr>
                                                        <td><strong>#{{ $work->id }}</strong></td>
                                                        <td>
                                                            <div>
                                                                <strong>{{ Str::limit($work->title, 60) }}</strong>
                                                                @if($work->certification)
                                                                    <br><small class="text-success">
                                                                        <i class="fas fa-certificate mr-1"></i>
                                                                        {{ $work->certification->certification_number }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-secondary">{{ $work->workType->name }}</span>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                {{ $work->primaryResponsible->full_name }}
                                                                @if($work->primaryResponsible->professor_code)
                                                                    <br><small
                                                                        class="text-muted">{{ $work->primaryResponsible->professor_code }}</small>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <small>{{ Str::limit($work->organizationalUnit->name, 40) }}</small>
                                                        </td>
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
                                                            <span class="badge badge-{{ $color }}">
                                                                {{ $work->currentStatus->name }}
                                                            </span>

                                                            @if($work->assignedEvaluator)
                                                                <br><small class="text-muted">
                                                                    <i class="fas fa-user-check mr-1"></i>
                                                                    {{ Str::limit($work->assignedEvaluator->full_name, 25) }}
                                                                </small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small>{{ $work->updated_at->format('d/m/Y') }}</small>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('viex.show', $work) }}"
                                                                    class="btn btn-sm btn-info" title="{{ __('Ver detalles') }}">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>

                                                                @if($work->currentStatus->name === 'En VIEX - En Evaluación')
                                                                    <button type="button" class="btn btn-sm btn-success"
                                                                        data-toggle="modal" data-target="#approveModal"
                                                                        data-work-id="{{ $work->id }}"
                                                                        data-work-title="{{ $work->title }}"
                                                                        title="{{ __('Aprobar y certificar') }}">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                @endif

                                                                @if($work->certification)
                                                                    <a href="{{ route('viex.certificate.download', $work->certification) }}"
                                                                        class="btn btn-sm btn-success"
                                                                        title="{{ __('Descargar certificado') }}">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                            @if($works->hasPages())
                                                    <div class="card-footer">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <small class="text-muted">
                                                                    {{ __('Mostrando :from a :to de :total resultados', [
                                    'from' => $works->firstItem(),
                                    'to' => $works->lastItem(),
                                    'total' => $works->total()
                                ]) }}
                                                                </small>
                                                            </div>
                                                            <div class="col-md-6 d-flex justify-content-end">
                                                                {{ $works->links() }}
                                                            </div>
                                                        </div>
                                                    </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal de Aprobación Rápida -->
    <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h5 class="modal-title" id="approveModalLabel">{{ __('Aprobar y Certificar Trabajo') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="approveForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <i class="fas fa-info-circle"></i>
                            <strong>Confirmación de Aprobación y Certificación</strong>
                            <p class="mb-0 mt-2">
                                Al aprobar y certificar este trabajo, se completará el proceso de evaluación y se generará automáticamente una certificación oficial válida por 2 años.
                            </p>
                        </div>

                        {{-- Resumen del Trabajo --}}
                        <div class="mb-3">
                            <strong>{{ __('Trabajo:') }}</strong>
                            <span id="modalWorkTitle"></span>
                        </div>

                        {{-- Comentarios Opcionales --}}
                        <div class="form-group">
                            <label for="modal_comments">
                                <i class="fas fa-comment-alt"></i>
                                Comentarios de Certificación (opcional)
                            </label>
                            <textarea
                                name="comments"
                                id="modal_comments"
                                class="form-control"
                                rows="3"
                                placeholder="Puede agregar comentarios sobre la certificación, observaciones finales, o recomendaciones..."></textarea>
                        </div>

                        {{-- Confirmación --}}
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="modal_confirm_approve" required>
                            <label class="custom-control-label" for="modal_confirm_approve">
                                <strong>Confirmo que he revisado completamente este trabajo y apruebo su certificación oficial.</strong>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-certificate mr-1"></i>
                            Confirmar Aprobación y Certificación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Configurar modal de aprobación
            $('#approveModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var workId = button.data('work-id');
                var workTitle = button.data('work-title');

                var modal = $(this);
                modal.find('#modalWorkTitle').text(workTitle);
                modal.find('#approveForm').attr('action', '/viex-evaluation/works/' + workId + '/approve-and-certify');
            });

            // Limpiar formulario al cerrar modal
            $('#approveModal').on('hidden.bs.modal', function () {
                $(this).find('form')[0].reset();
            });

            // Auto-refresh cada 2 minutos para mantener datos actualizados
            setTimeout(function () {
                window.location.reload();
            }, 120000);
        });
    </script>
@endpush
