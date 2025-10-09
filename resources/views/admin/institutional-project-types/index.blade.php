@extends('adminlte::page')

@section('title', __('Tipos institucionales'))

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fas fa-university"></i>
            {{ __('Tipos de proyectos institucionales') }}
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ __('Catalogos') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Tipos institucionales') }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="card" id="institutional-types-catalog"
    data-confirm-title="{{ __('Estas seguro?') }}"
    data-confirm-text="{{ __('Esta accion no se puede deshacer.') }}"
    data-confirm-accept="{{ __('Si, eliminar') }}"
     data-confirm-cancel="{{ __('Cancelar') }}">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list"></i>
            {{ __('Catalogo de tipos institucionales') }}
        </h3>
        <div class="card-tools">
            <a href="{{ route('admin.institutional-project-types.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> {{ __('Nuevo tipo') }}
            </a>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <i class="icon fas fa-check"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <i class="icon fas fa-ban"></i> {{ session('error') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Nombre') }}</th>
                        <th>{{ __('Descripcion') }}</th>
                        <th>{{ __('Estado') }}</th>
                        <th>{{ __('Proyectos asociados') }}</th>
                        <th class="text-center" style="width: 150px;">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($types as $type)
                        <tr>
                            <td>{{ $type->id }}</td>
                            <td class="font-weight-bold">{{ $type->name }}</td>
                            <td>
                                @if ($type->description)
                                    {{ \Illuminate\Support\Str::limit($type->description, 120) }}
                                @else
                                    <span class="text-muted">{{ __('Sin descripcion') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($type->is_active)
                                    <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Activo') }}</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-pause"></i> {{ __('Inactivo') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $type->project_details_count }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.institutional-project-types.show', $type) }}" class="btn btn-info" title="{{ __('Ver detalles') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.institutional-project-types.edit', $type) }}" class="btn btn-warning" title="{{ __('Editar') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if ($type->hasAssociatedProjects())
                                        <button type="button" class="btn btn-secondary" disabled title="{{ __('No se puede eliminar: tiene proyectos asociados') }}">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-danger delete-institutional-type"
                                                data-type-id="{{ $type->id }}" title="{{ __('Eliminar') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                                <form id="delete-institutional-type-{{ $type->id }}" action="{{ route('admin.institutional-project-types.destroy', $type) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-university fa-2x mb-3"></i>
                                <p class="mb-2">{{ __('No hay tipos institucionales registrados.') }}</p>
                                <a href="{{ route('admin.institutional-project-types.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> {{ __('Crear tipo') }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($types->hasPages())
        <div class="card-footer">
            {{ $types->links() }}
        </div>
    @endif
</div>
@stop

@section('css')
<style>
    .table th {
        vertical-align: middle;
    }
</style>
@stop

@section('js')
<script>
    const institutionalCatalog = document.getElementById('institutional-types-catalog');
    const typeMessages = {
        title: (institutionalCatalog && institutionalCatalog.getAttribute('data-confirm-title')) || 'Confirmar',
    text: (institutionalCatalog && institutionalCatalog.getAttribute('data-confirm-text')) || 'Esta accion no se puede deshacer.',
        accept: (institutionalCatalog && institutionalCatalog.getAttribute('data-confirm-accept')) || 'Aceptar',
        cancel: (institutionalCatalog && institutionalCatalog.getAttribute('data-confirm-cancel')) || 'Cancelar'
    };

    function confirmInstitutionalTypeDeletion(typeId) {
        Swal.fire({
            title: typeMessages.title,
            text: typeMessages.text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: typeMessages.accept,
            cancelButtonText: typeMessages.cancel
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-institutional-type-' + typeId).submit();
            }
        });
    }

    document.querySelectorAll('.delete-institutional-type').forEach((button) => {
        button.addEventListener('click', () => {
            const typeId = button.getAttribute('data-type-id');
            confirmInstitutionalTypeDeletion(typeId);
        });
    });

    setTimeout(() => {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@stop
