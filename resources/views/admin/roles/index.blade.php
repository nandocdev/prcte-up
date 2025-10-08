@extends('adminlte::page')

@section('title', __('Gestión de Roles'))

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>{{ __('Gestión de Roles') }}</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Roles') }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Lista de Roles') }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Nuevo Rol') }}
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Nombre del Rol') }}</th>
                                <th>{{ __('Usuarios Asignados') }}</th>
                                <th>{{ __('Permisos') }}</th>
                                <th>{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($roles as $role)
                                <tr>
                                    <td>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $role->name }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $role->users->count() }}</span>
                                        @if($role->users->count() > 0)
                                            <div class="mt-1">
                                                @foreach($role->users->take(3) as $user)
                                                    <span class="badge badge-secondary mr-1">{{ $user->name }}</span>
                                                @endforeach
                                                @if($role->users->count() > 3)
                                                    <span
                                                        class="text-muted">{{ __('y :count más...', ['count' => $role->users->count() - 3]) }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $role->permissions->count() }}
                                            {{ __('permisos') }}</span>
                                        @if($role->permissions->count() > 0)
                                            <div class="mt-1">
                                                @foreach($role->permissions->take(3) as $permission)
                                                    <span class="badge badge-outline-info mr-1" style="font-size: 0.7em;">
                                                        {{ str_replace('.', ' › ', $permission->name) }}
                                                    </span>
                                                @endforeach
                                                @if($role->permissions->count() > 3)
                                                    <br><small
                                                        class="text-muted">{{ __('y :count más...', ['count' => $role->permissions->count() - 3]) }}</small>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-info btn-sm"
                                                title="{{ __('Ver') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-sm"
                                                title="{{ __('Editar') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @unless(in_array($role->name, ['super_admin', 'profesor']))
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="confirmDelete({{ $role->id }})" title="{{ __('Eliminar') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        {{ __('No se encontraron roles.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Información sobre roles del sistema -->
<div class="row">
    <div class="col-12">
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">{{ __('Información sobre los Roles del Sistema') }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>{{ __('Roles Principales') }}</strong></h6>
                        <ul class="list-unstyled">
                            <li><strong>Super Admin:</strong> {{ __('Control total del sistema') }}</li>
                            <li><strong>Profesor:</strong> {{ __('Crea y gestiona trabajos de extensión') }}</li>
                            <li><strong>Coordinador Extensión:</strong> {{ __('Revisa trabajos de su unidad') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>{{ __('Roles Administrativos') }}</strong></h6>
                        <ul class="list-unstyled">
                            <li><strong>Decano/Director:</strong> {{ __('Aprueba trabajos para VIEX') }}</li>
                            <li><strong>VIEX Admin:</strong> {{ __('Certifica trabajos finales') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Confirmar Eliminación') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ __('¿Estás seguro de que deseas eliminar este rol? Esta acción no se puede deshacer.') }}
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        {{ __('Eliminar') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    function confirmDelete(roleId) {
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `{{ url('admin/roles') }}/${roleId}`;
        $('#deleteModal').modal('show');
    }
</script>
@stop
