@extends('adminlte::page')

@section('title', __('Tipos de Trabajos'))

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fas fa-tags"></i>
            {{ __('Tipos de Trabajos de Extension') }}
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ __('Configuracion') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Tipos de Trabajos') }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="card" id="work-types-catalog"
    data-confirm-title="{{ __('Estas seguro?') }}"
    data-confirm-text="{{ __('Esta accion no se puede deshacer.') }}"
    data-confirm-accept="{{ __('Si, eliminar') }}"
    data-confirm-cancel="{{ __('Cancelar') }}">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i>
            {{ __('Catalogo de Tipos de Trabajos') }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('admin.work-types.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i>
                {{ __('Nuevo Tipo') }}
            </a>
        </div>
    </div>

    <div class="card-body">
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

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="text-nowrap">#</th>
                        <th>{{ __('Nombre') }}</th>
                        <th>{{ __('Descripcion') }}</th>
                        <th>{{ __('Estado') }}</th>
                        <th>{{ __('Trabajos Registrados') }}</th>
                        <th class="text-center" style="width: 150px">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workTypes as $workType)
                    <tr>
                        <td>{{ $workType->id }}</td>
                        <td class="font-weight-bold">{{ $workType->name }}</td>
                        <td>
                            @if ($workType->description)
                            {{ \Illuminate\Support\Str::limit($workType->description, 120) }}
                            @else
                            <span class="text-muted">{{ __('Sin descripcion') }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($workType->is_active)
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> {{ __('Activo') }}
                            </span>
                            @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-pause"></i> {{ __('Inactivo') }}
                            </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">
                                {{ $workType->works_count }} {{ __('trabajos') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('Acciones') }}">
                                <a href="{{ route('admin.work-types.show', $workType) }}" class="btn btn-info" title="{{ __('Ver detalles') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.work-types.edit', $workType) }}" class="btn btn-warning" title="{{ __('Editar') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if ($workType->works_count === 0)
                                <button type="button" class="btn btn-danger delete-work-type"
                                    title="{{ __('Eliminar') }}"
                                    data-work-type-id="{{ $workType->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @else
                                <button type="button" class="btn btn-secondary" title="{{ __('No se puede eliminar - tiene trabajos asociados') }}" disabled>
                                    <i class="fas fa-ban"></i>
                                </button>
                                @endif
                            </div>
                            <form id="delete-form-{{ $workType->id }}" action="{{ route('admin.work-types.destroy', $workType) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fas fa-tags fa-2x mb-3"></i>
                            <p class="mb-2">{{ __('No hay tipos de trabajos registrados.') }}</p>
                            <a href="{{ route('admin.work-types.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> {{ __('Crear primer tipo') }}
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($workTypes->hasPages())
    <div class="card-footer">
        {{ $workTypes->links() }}
    </div>
    @endif
</div>

<div class="row">
    @php
    $collection = $workTypes->getCollection();
    @endphp
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $collection->count() }}</h3>
                <p>{{ __('Tipos en la pagina') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-tags"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $collection->where('is_active', true)->count() }}</h3>
                <p>{{ __('Activos') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-check"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner text-dark">
                <h3>{{ $collection->where('is_active', false)->count() }}</h3>
                <p>{{ __('Inactivos') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-pause"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $collection->sum('works_count') }}</h3>
                <p>{{ __('Trabajos asociados (pagina)') }}</p>
            </div>
            <div class="icon">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .table th {
        vertical-align: middle;
    }

    .btn-group-sm>.btn {
        padding: 0.35rem 0.6rem;
    }
</style>
@stop

@section('js')
<script>
    const catalogContainer = document.getElementById('work-types-catalog');

    const confirmMessages = {
        title: (catalogContainer && catalogContainer.getAttribute('data-confirm-title')) || 'Confirmar',
        text: (catalogContainer && catalogContainer.getAttribute('data-confirm-text')) || 'Esta accion no se puede deshacer.',
        accept: (catalogContainer && catalogContainer.getAttribute('data-confirm-accept')) || 'Aceptar',
        cancel: (catalogContainer && catalogContainer.getAttribute('data-confirm-cancel')) || 'Cancelar',
    };

    function confirmDelete(workTypeId) {
        Swal.fire({
            title: confirmMessages.title,
            text: confirmMessages.text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: confirmMessages.accept,
            cancelButtonText: confirmMessages.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + workTypeId).submit();
            }
        });
    }

    document.querySelectorAll('.delete-work-type').forEach((button) => {
        button.addEventListener('click', () => {
            const workTypeId = button.getAttribute('data-work-type-id');
            confirmDelete(workTypeId);
        });
    });

    setTimeout(() => {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@stop