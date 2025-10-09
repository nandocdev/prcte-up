@extends('adminlte::page')

@section('title', __('Unidades organizacionales'))

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>
            <i class="fas fa-sitemap"></i>
            {{ __('Unidades organizacionales') }}
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item"><a href="#">{{ __('Configuración') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Unidades organizacionales') }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">
            <i class="fas fa-list"></i>
            {{ __('Listado de unidades') }}
        </h3>
        <a href="{{ route('admin.organizational-units.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            {{ __('Nueva unidad') }}
        </a>
    </div>

    <div class="card-body">
        @foreach (['success', 'error'] as $flash)
        @if (session($flash))
        <div class="alert alert-{{ $flash === 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
            <i class="icon fas fa-{{ $flash === 'success' ? 'check' : 'ban' }} mr-2"></i>
            {{ session($flash) }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @endforeach

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th style="width: 10%">#</th>
                        <th>{{ __('Nombre') }}</th>
                        <th>{{ __('Tipo') }}</th>
                        <th>{{ __('Unidad padre') }}</th>
                        <th>{{ __('Subunidades') }}</th>
                        <th>{{ __('Usuarios') }}</th>
                        <th style="width: 140px">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                    <tr>
                        <td>{{ $unit->id }}</td>
                        <td>{{ $unit->name }}</td>
                        <td>
                            <span class="badge badge-primary">
                                {{ $unit->typeLabel() }}
                            </span>
                        </td>
                        <td>
                            @if($unit->parent)
                            {{ $unit->parent->name }}
                            @else
                            <span class="text-muted">{{ __('Sin unidad padre') }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-light">
                                {{ $unit->children->count() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-light">
                                {{ $unit->users->count() }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.organizational-units.show', $unit) }}" class="btn btn-info" title="{{ __('Ver detalle') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.organizational-units.edit', $unit) }}" class="btn btn-warning" title="{{ __('Editar') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-delete-unit" title="{{ __('Eliminar') }}"
                                    data-unit-id="{{ $unit->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <form id="delete-form-{{ $unit->id }}" action="{{ route('admin.organizational-units.destroy', $unit) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-university fa-3x mb-3"></i>
                            <div>{{ __('No hay unidades organizacionales registradas.') }}</div>
                            <a href="{{ route('admin.organizational-units.create') }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> {{ __('Crear primera unidad') }}
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($units->hasPages())
    <div class="card-footer">
        {{ $units->links() }}
    </div>
    @endif
</div>
@stop

@section('css')
<style>
    .table th {
        vertical-align: middle;
    }

    .btn-group-sm>.btn {
        padding: 0.35rem 0.5rem;
    }
</style>
@stop

@section('js')
<script>
    function confirmDelete(unitId) {
        Swal.fire({
            title: "{{ __('¿Estás seguro?') }}",
            text: "{{ __('Esta acción no se puede deshacer.') }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "{{ __('Sí, eliminar') }}",
            cancelButtonText: "{{ __('Cancelar') }}"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + unitId).submit();
            }
        });
    }

    $(document).ready(function() {
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);

        $('.btn-delete-unit').on('click', function() {
            const unitId = $(this).data('unit-id');
            confirmDelete(unitId);
        });
    });
</script>
@stop