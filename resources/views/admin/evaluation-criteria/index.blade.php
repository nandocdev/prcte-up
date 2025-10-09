@extends('adminlte::page')

@section('title', __('Criterios de evaluacion'))

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fas fa-balance-scale"></i>
            {{ __('Criterios de evaluacion') }}
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ __('Configuracion') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Criterios de evaluacion') }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <i class="icon fas fa-check"></i>
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <i class="icon fas fa-ban"></i>
    {{ session('error') }}
</div>
@endif

@if (session('warning'))
<div class="alert alert-warning alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <i class="icon fas fa-exclamation-triangle"></i>
    {{ session('warning') }}
</div>
@endif

@if ($errors->has('criteria'))
<div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <i class="icon fas fa-ban"></i>
    {{ $errors->first('criteria') }}
</div>
@endif

<div class="row mb-3">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $criteria->count() }}</h3>
                <p>{{ __('Total de criterios') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-layer-group"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $criteria->where('is_active', true)->count() }}</h3>
                <p>{{ __('Criterios activos') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-check"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner text-dark">
                <h3>{{ $activeWeight }}%</h3>
                <p>{{ __('Suma de pesos activos') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-percentage"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ max(0, 100 - $activeWeight) }}%</h3>
                <p>{{ __('Pendiente para llegar a 100%') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

<div class="card" id="evaluation-criteria-catalog"
    data-confirm-title="{{ __('Estas seguro?') }}"
    data-confirm-text="{{ __('Esta accion no se puede deshacer.') }}"
    data-confirm-accept="{{ __('Si, eliminar') }}"
    data-confirm-cancel="{{ __('Cancelar') }}">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-list"></i>
            {{ __('Catalogo de criterios') }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('admin.evaluation-criteria.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> {{ __('Nuevo criterio') }}
            </a>
        </div>
    </div>

    <form action="{{ route('admin.evaluation-criteria.sync') }}" method="POST">
        @csrf
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-nowrap">#</th>
                            <th>{{ __('Nombre') }}</th>
                            <th>{{ __('Categoria') }}</th>
                            <th class="text-center">{{ __('Max score') }}</th>
                            <th class="text-center" style="width: 120px;">{{ __('Peso (%)') }}</th>
                            <th class="text-center" style="width: 120px;">{{ __('Orden') }}</th>
                            <th class="text-center">{{ __('Activo') }}</th>
                            <th class="text-center">{{ __('Evaluaciones') }}</th>
                            <th class="text-center" style="width: 160px;">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($criteria as $index => $criterion)
                        <tr>
                            <td>{{ $criterion->id }}</td>
                            <td>
                                <strong>{{ $criterion->name }}</strong>
                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($criterion->description, 120) }}</div>
                            </td>
                            <td>{{ $criterion->category ?? __('Sin categoria') }}</td>
                            <td class="text-center">{{ $criterion->max_score }}</td>
                            <td class="text-center">
                                <input type="hidden" name="criteria[{{ $index }}][id]" value="{{ $criterion->id }}">
                                <input type="number" class="form-control form-control-sm text-center" min="0" max="100"
                                    name="criteria[{{ $index }}][weight]"
                                    value="{{ old('criteria.' . $index . '.weight', $criterion->weight) }}">
                            </td>
                            <td class="text-center">
                                <input type="number" class="form-control form-control-sm text-center" min="0" max="1000"
                                    name="criteria[{{ $index }}][order]"
                                    value="{{ old('criteria.' . $index . '.order', $criterion->order) }}">
                            </td>
                            <td class="text-center">
                                <input type="hidden" name="criteria[{{ $index }}][is_active]" value="0">
                                <div class="custom-control custom-switch d-inline-block">
                                    <input type="checkbox" class="custom-control-input"
                                        id="criterion-active-{{ $criterion->id }}"
                                        name="criteria[{{ $index }}][is_active]" value="1"
                                        {{ old('criteria.' . $index . '.is_active', $criterion->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="criterion-active-{{ $criterion->id }}"></label>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-info">{{ $criterion->evaluation_details_count }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.evaluation-criteria.show', $criterion) }}" class="btn btn-info" title="{{ __('Ver detalles') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.evaluation-criteria.edit', $criterion) }}" class="btn btn-warning" title="{{ __('Editar') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if ($criterion->hasEvaluationDetails())
                                    <button type="button" class="btn btn-secondary" disabled title="{{ __('No se puede eliminar: tiene evaluaciones') }}">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                    @else
                                    <button type="button" class="btn btn-danger delete-criterion" data-criterion-id="{{ $criterion->id }}" title="{{ __('Eliminar') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                                <form id="delete-criterion-{{ $criterion->id }}" action="{{ route('admin.evaluation-criteria.destroy', $criterion) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fas fa-balance-scale fa-2x mb-3"></i>
                                <p class="mb-2">{{ __('No hay criterios registrados.') }}</p>
                                <a href="{{ route('admin.evaluation-criteria.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> {{ __('Crear primer criterio') }}
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($criteria->isNotEmpty())
        <div class="card-footer d-flex justify-content-between align-items-center">
            <p class="mb-0 text-muted">
                {{ __('Ajusta pesos y orden, luego guarda para validar el total de 100%.') }}
            </p>
            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-save"></i> {{ __('Guardar ajustes') }}
            </button>
        </div>
        @endif
    </form>
</div>
@stop

@section('css')
<style>
    .table td {
        vertical-align: middle;
    }

    .table td .form-control-sm {
        padding: 0.25rem 0.5rem;
    }

    .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #28a745;
        border-color: #28a745;
    }
</style>
@stop

@section('js')
<script>
    const container = document.getElementById('evaluation-criteria-catalog');

    const confirmData = {
        title: (container && container.getAttribute('data-confirm-title')) || 'Confirmar',
        text: (container && container.getAttribute('data-confirm-text')) || 'Esta accion no se puede deshacer.',
        accept: (container && container.getAttribute('data-confirm-accept')) || 'Aceptar',
        cancel: (container && container.getAttribute('data-confirm-cancel')) || 'Cancelar',
    };

    function confirmDelete(criterionId) {
        Swal.fire({
            title: confirmData.title,
            text: confirmData.text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: confirmData.accept,
            cancelButtonText: confirmData.cancel,
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('delete-criterion-' + criterionId);
                if (form) {
                    form.submit();
                }
            }
        });
    }

    document.querySelectorAll('.delete-criterion').forEach((button) => {
        button.addEventListener('click', () => {
            confirmDelete(button.getAttribute('data-criterion-id'));
        });
    });

    setTimeout(() => {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@stop
