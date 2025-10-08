@extends('adminlte::page')

@section('title', 'Tipos de Trabajos')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>
                <i class="fas fa-tags"></i>
                Tipos de Trabajos de Extensión
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="#">Configuración</a></li>
                <li class="breadcrumb-item active">Tipos de Trabajos</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list"></i>
                Catálogo de Tipos de Trabajos
            </h3>
            <div class="card-tools">
                <a href="{{ route('admin.work-types.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i>
                    Nuevo Tipo
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
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Trabajos Registrados</th>
                            <th style="width: 150px">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workTypes as $workType)
                            <tr>
                                <td>{{ $workType->id }}</td>
                                <td>
                                    <strong>{{ $workType->name }}</strong>
                                </td>
                                <td>
                                    @if($workType->description)
                                        {{ Str::limit($workType->description, 80) }}
                                    @else
                                        <span class="text-muted">Sin descripción</span>
                                    @endif
                                </td>
                                <td>
                                    @if($workType->is_active)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Activo
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i> Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $workType->work_of_extensions_count ?? 0 }} trabajos
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.work-types.show', $workType) }}"
                                           class="btn btn-info btn-xs" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.work-types.edit', $workType) }}"
                                           class="btn btn-warning btn-xs" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($workType->work_of_extensions_count == 0)
                                            <button type="button" class="btn btn-danger btn-xs"
                                                    onclick="confirmDelete({{ $workType->id }})" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-secondary btn-xs"
                                                    title="No se puede eliminar - tiene trabajos asociados" disabled>
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif
                                    </div>

                                    <!-- Form hidden para eliminación -->
                                    <form id="delete-form-{{ $workType->id }}"
                                          action="{{ route('admin.work-types.destroy', $workType) }}"
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-tags fa-3x mb-3"></i>
                                    <br>
                                    No hay tipos de trabajos registrados.
                                    <br>
                                    <a href="{{ route('admin.work-types.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus"></i> Crear primer tipo
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($workTypes->hasPages())
            <div class="card-footer">
                {{ $workTypes->links() }}
            </div>
        @endif
    </div>

    <!-- Info Box con Estadísticas -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $workTypes->count() }}</h3>
                    <p>Tipos Totales</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $workTypes->where('is_active', true)->count() }}</h3>
                    <p>Tipos Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $workTypes->where('is_active', false)->count() }}</h3>
                    <p>Tipos Inactivos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $workTypes->sum('work_of_extensions_count') }}</h3>
                    <p>Trabajos Registrados</p>
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
        .btn-group-sm > .btn, .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
@stop

@section('js')
<script>
    function confirmDelete(workTypeId) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer. Se eliminará el tipo de trabajo permanentemente.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + workTypeId).submit();
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
