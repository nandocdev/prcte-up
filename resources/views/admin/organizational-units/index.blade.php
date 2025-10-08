@extends('adminlte::page')

@section('title', 'Unidades Académicas')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-university"></i>
                Unidades Académicas
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Configuración</a></li>
                <li class="breadcrumb-item active">Unidades Académicas</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i>
                Listado de Unidades Académicas
            </h3>
            <div class="card-tools">
                <a href="{{ route('admin.organizational-units.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i>
                    Nueva Unidad
                </a>
            </div>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-check"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <i class="icon fas fa-ban"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Unidad Padre</th>
                            <th>Sub-unidades</th>
                            <th>Usuarios</th>
                            <th style="width: 150px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                            <tr>
                                <td>{{ $unit->id }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $unit->code }}</span>
                                </td>
                                <td>
                                    <strong>{{ $unit->name }}</strong>
                                    @if($unit->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($unit->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @switch($unit->unit_type)
                                        @case('faculty')
                                            <span class="badge badge-primary">
                                                <i class="fas fa-university"></i> Facultad
                                            </span>
                                            @break
                                        @case('department')
                                            <span class="badge badge-success">
                                                <i class="fas fa-building"></i> Departamento
                                            </span>
                                            @break
                                        @case('school')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-graduation-cap"></i> Escuela
                                            </span>
                                            @break
                                        @case('center')
                                            <span class="badge badge-info">
                                                <i class="fas fa-map-marker-alt"></i> Centro
                                            </span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ $unit->unit_type }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($unit->parent)
                                        <span class="text-sm">{{ $unit->parent->name }}</span>
                                    @else
                                        <span class="text-muted">— Sin unidad padre —</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light">
                                        {{ $unit->children->count() }} sub-unidades
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light">
                                        {{ $unit->users->count() ?? 0 }} usuarios
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.organizational-units.show', $unit) }}"
                                           class="btn btn-info btn-xs" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.organizational-units.edit', $unit) }}"
                                           class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-xs"
                                                onclick="confirmDelete({{ $unit->id }})" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Form hidden para eliminación -->
                                    <form id="delete-form-{{ $unit->id }}"
                                          action="{{ route('admin.organizational-units.destroy', $unit) }}"
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-university fa-3x mb-3"></i>
                                    <br>
                                    No hay unidades académicas registradas.
                                    <br>
                                    <a href="{{ route('admin.organizational-units.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus"></i> Crear primera unidad
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($units->hasPages())
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
        .btn-group-sm > .btn, .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@stop

@section('js')
<script>
    function confirmDelete(unitId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer. Se eliminará la unidad académica permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + unitId).submit();
            }
        });
    }

    // Auto-hide alerts after 5 seconds
    $(document).ready(function() {
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
@stop
