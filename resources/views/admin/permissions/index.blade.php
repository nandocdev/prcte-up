@extends('layouts.app')

@section('title', __('Gestión de Permisos'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <h1><i class="fas fa-key mr-2"></i>{{ __('Gestión de Permisos') }}</h1>
   <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
         <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
         <li class="breadcrumb-item active">{{ __('Permisos') }}</li>
      </ol>
   </nav>
</div>
@stop

@section('content')
<!-- Estadísticas de Permisos -->
<div class="row">
   <div class="col-lg-3 col-6">
      <div class="small-box bg-info">
         <div class="inner">
            <h3>{{ $stats['total_permissions'] }}</h3>
            <p>{{ __('Total Permisos') }}</p>
         </div>
         <div class="icon">
            <i class="fas fa-key"></i>
         </div>
      </div>
   </div>

   <div class="col-lg-3 col-6">
      <div class="small-box bg-success">
         <div class="inner">
            <h3>{{ $stats['total_categories'] }}</h3>
            <p>{{ __('Categorías') }}</p>
         </div>
         <div class="icon">
            <i class="fas fa-layer-group"></i>
         </div>
      </div>
   </div>

   <div class="col-lg-3 col-6">
      <div class="small-box bg-warning">
         <div class="inner">
            <h3>{{ $stats['permissions_with_roles'] }}</h3>
            <p>{{ __('Con Roles Asignados') }}</p>
         </div>
         <div class="icon">
            <i class="fas fa-users-cog"></i>
         </div>
      </div>
   </div>

   <div class="col-lg-3 col-6">
      <div class="small-box bg-danger">
         <div class="inner">
            <h3>{{ $stats['orphan_permissions'] }}</h3>
            <p>{{ __('Sin Roles') }}</p>
         </div>
         <div class="icon">
            <i class="fas fa-exclamation-triangle"></i>
         </div>
      </div>
   </div>
</div>

<div class="row">
   <div class="col-12">
      <div class="card card-outline card-primary">
         <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list mr-2"></i>{{ __('Permisos del Sistema') }}</h3>
            <div class="card-tools">
               <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createPermissionModal">
                  <i class="fas fa-plus mr-1"></i>{{ __('Nuevo Permiso') }}
               </button>
            </div>
         </div>

         <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-3">
               <div class="col-md-6">
                  <div class="input-group">
                     <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                     </div>
                     <input type="text" id="searchPermissions" class="form-control" placeholder="{{ __('Buscar permisos...') }}">
                  </div>
               </div>
               <div class="col-md-6">
                  <select id="categoryFilter" class="form-control">
                     <option value="">{{ __('Todas las categorías') }}</option>
                     @foreach($groupedPermissions->keys() as $category)
                     <option value="{{ $category }}">{{ ucfirst($category) }}</option>
                     @endforeach
                  </select>
               </div>
            </div>

            <!-- Permisos Agrupados -->
            @foreach($groupedPermissions as $category => $permissions)
            <div class="permission-category" data-category="{{ $category }}">
               <div class="card mb-3">
                  <div class="card-header bg-light">
                     <h5 class="mb-0">
                        <i class="fas fa-folder-open mr-2 text-primary"></i>
                        <strong>{{ ucfirst($category) }}</strong>
                        <span class="badge badge-secondary ml-2">{{ $permissions->count() }} {{ __('permisos') }}</span>
                     </h5>
                  </div>
                  <div class="card-body p-0">
                     <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                           <thead class="thead-light">
                              <tr>
                                 <th>{{ __('Permiso') }}</th>
                                 <th>{{ __('Roles Asignados') }}</th>
                                 <th>{{ __('Usuarios Afectados') }}</th>
                                 <th>{{ __('Estado') }}</th>
                                 <th>{{ __('Acciones') }}</th>
                              </tr>
                           </thead>
                           <tbody>
                              @foreach($permissions as $permission)
                              <tr class="permission-row" data-permission="{{ $permission->name }}">
                                 <td>
                                    <div class="d-flex align-items-center">
                                       @if(in_array($permission->name, ['system.manage', 'users.manage', 'roles.manage', 'permissions.manage']))
                                       <i class="fas fa-shield-alt text-danger mr-2" title="{{ __('Permiso crítico del sistema') }}"></i>
                                       @endif
                                       <div>
                                          <strong>{{ str_replace($category . '.', '', $permission->name) }}</strong>
                                          <br>
                                          <small class="text-muted">{{ $permission->name }}</small>
                                       </div>
                                    </div>
                                 </td>
                                 <td>
                                    @if($permission->roles->count() > 0)
                                    @foreach($permission->roles->take(3) as $role)
                                    <span class="badge badge-info mr-1 mb-1">{{ $role->name }}</span>
                                    @endforeach
                                    @if($permission->roles->count() > 3)
                                    <span class="badge badge-light">+{{ $permission->roles->count() - 3 }}</span>
                                    @endif
                                    @else
                                    <span class="text-muted">{{ __('Sin roles') }}</span>
                                    @endif
                                 </td>
                                 <td>
                                    @php
                                    $userCount = $permission->roles->sum(fn($role) => $role->users->count());
                                    @endphp
                                    @if($userCount > 0)
                                    <span class="badge badge-success">{{ $userCount }} {{ __('usuarios') }}</span>
                                    @else
                                    <span class="text-muted">{{ __('Ninguno') }}</span>
                                    @endif
                                 </td>
                                 <td>
                                    @if($permission->roles->count() > 0)
                                    <span class="badge badge-success">{{ __('Activo') }}</span>
                                    @else
                                    <span class="badge badge-warning">{{ __('Sin uso') }}</span>
                                    @endif
                                 </td>
                                 <td>
                                    <div class="btn-group btn-group-sm">
                                       <a href="{{ route('admin.permissions.show', $permission) }}"
                                          class="btn btn-outline-info" title="{{ __('Ver') }}">
                                          <i class="fas fa-eye"></i>
                                       </a>
                                       <a href="{{ route('admin.permissions.edit', $permission) }}"
                                          class="btn btn-outline-primary" title="{{ __('Editar') }}">
                                          <i class="fas fa-edit"></i>
                                       </a>
                                       @unless(in_array($permission->name, ['system.manage', 'users.manage', 'roles.manage', 'permissions.manage']))
                                       <button class="btn btn-outline-danger"
                                          onclick="confirmDelete({{ $permission->id }}, '{{ $permission->name }}')"
                                          title="{{ __('Eliminar') }}">
                                          <i class="fas fa-trash"></i>
                                       </button>
                                       @endunless
                                    </div>
                                 </td>
                              </tr>
                              @endforeach
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
            @endforeach
         </div>
      </div>
   </div>
</div>

<!-- Información sobre el Sistema de Permisos -->
<div class="row">
   <div class="col-md-6">
      <div class="card card-info">
         <div class="card-header">
            <h3 class="card-title">{{ __('Categorías de Permisos') }}</h3>
         </div>
         <div class="card-body">
            <ul class="list-unstyled">
               <li><strong>works:</strong> {{ __('Gestión de trabajos de extensión') }}</li>
               <li><strong>users:</strong> {{ __('Administración de usuarios') }}</li>
               <li><strong>roles:</strong> {{ __('Gestión de roles') }}</li>
               <li><strong>permissions:</strong> {{ __('Gestión de permisos') }}</li>
               <li><strong>system:</strong> {{ __('Administración del sistema') }}</li>
            </ul>
         </div>
      </div>
   </div>

   <div class="col-md-6">
      <div class="card card-warning">
         <div class="card-header">
            <h3 class="card-title">{{ __('Permisos Críticos') }}</h3>
         </div>
         <div class="card-body">
            <div class="alert alert-warning">
               <i class="fas fa-exclamation-triangle"></i>
               {{ __('Los permisos marcados con el escudo no deben ser eliminados ya que son críticos para el funcionamiento del sistema.') }}
            </div>
            <ul class="list-unstyled">
               <li><i class="fas fa-shield-alt text-danger"></i> <strong>system.manage</strong></li>
               <li><i class="fas fa-shield-alt text-danger"></i> <strong>users.manage</strong></li>
               <li><i class="fas fa-shield-alt text-danger"></i> <strong>roles.manage</strong></li>
               <li><i class="fas fa-shield-alt text-danger"></i> <strong>permissions.manage</strong></li>
            </ul>
         </div>
      </div>
   </div>
</div>

<!-- Modal para Crear Permiso -->
<div class="modal fade" id="createPermissionModal" tabindex="-1">
   <div class="modal-dialog">
      <div class="modal-content">
         <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf
            <div class="modal-header">
               <h4 class="modal-title">{{ __('Crear Nuevo Permiso') }}</h4>
               <button type="button" class="close" data-dismiss="modal">
                  <span>&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <div class="form-group">
                  <label for="name">{{ __('Nombre del Permiso') }} <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="name" name="name" required
                     placeholder="ej: works.create.special">
                  <small class="form-text text-muted">
                     {{ __('Usa el formato: categoría.acción (ej: works.create, users.delete)') }}
                  </small>
               </div>

               <div class="form-group">
                  <label for="roles">{{ __('Asignar a Roles (Opcional)') }}</label>
                  <select class="form-control" id="roles" name="roles[]" multiple size="5">
                     @foreach($allRoles as $role)
                     <option value="{{ $role->id }}">{{ $role->name }}</option>
                     @endforeach
                  </select>
                  <small class="form-text text-muted">
                     {{ __('Mantén presionado Ctrl para seleccionar múltiples roles') }}
                  </small>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
               <button type="submit" class="btn btn-primary">{{ __('Crear Permiso') }}</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title">{{ __('Confirmar Eliminación') }}</h5>
            <button type="button" class="close" data-dismiss="modal">
               <span>&times;</span>
            </button>
         </div>
         <div class="modal-body">
            <p>{{ __('¿Estás seguro de que deseas eliminar el permiso') }} <strong id="permissionName"></strong>?</p>
            <div class="alert alert-warning">
               <i class="fas fa-exclamation-triangle"></i>
               {{ __('Esta acción no se puede deshacer.') }}
            </div>
         </div>
         <div class="modal-footer">
            <form id="deleteForm" method="POST">
               @csrf
               @method('DELETE')
               <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancelar') }}</button>
               <button type="submit" class="btn btn-danger">{{ __('Eliminar') }}</button>
            </form>
         </div>
      </div>
   </div>
</div>
@stop

@push('scripts')
<script>
   // Búsqueda de permisos
   document.getElementById('searchPermissions').addEventListener('input', function() {
      const searchTerm = this.value.toLowerCase();
      filterPermissions();
   });

   // Filtro por categoría
   document.getElementById('categoryFilter').addEventListener('change', function() {
      filterPermissions();
   });

   function filterPermissions() {
      const searchTerm = document.getElementById('searchPermissions').value.toLowerCase();
      const selectedCategory = document.getElementById('categoryFilter').value;

      // Filtrar categorías
      document.querySelectorAll('.permission-category').forEach(categoryDiv => {
         const category = categoryDiv.dataset.category;
         let showCategory = selectedCategory === '' || category === selectedCategory;

         if (showCategory && searchTerm) {
            // Si hay término de búsqueda, verificar si algún permiso en la categoría coincide
            const hasMatchingPermission = Array.from(categoryDiv.querySelectorAll('.permission-row')).some(row => {
               return row.dataset.permission.toLowerCase().includes(searchTerm);
            });
            showCategory = hasMatchingPermission;
         }

         categoryDiv.style.display = showCategory ? 'block' : 'none';

         // Filtrar permisos individuales dentro de la categoría
         if (showCategory) {
            categoryDiv.querySelectorAll('.permission-row').forEach(row => {
               const permissionName = row.dataset.permission.toLowerCase();
               const showPermission = searchTerm === '' || permissionName.includes(searchTerm);
               row.style.display = showPermission ? '' : 'none';
            });
         }
      });
   }

   function confirmDelete(permissionId, permissionName) {
      document.getElementById('permissionName').textContent = permissionName;
      document.getElementById('deleteForm').action = `{{ url('admin/permissions') }}/${permissionId}`;
      $('#deleteModal').modal('show');
   }
</script>
@endpush