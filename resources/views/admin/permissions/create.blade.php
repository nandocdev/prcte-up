@extends('layouts.app')

@section('title', __('Crear Permiso'))

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_custom.css') }}">
@endpush

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
   <h1><i class="fas fa-plus mr-2"></i>{{ __('Crear Nuevo Permiso') }}</h1>
   <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
         <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
         <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}">{{ __('Permisos') }}</a></li>
         <li class="breadcrumb-item active">{{ __('Crear') }}</li>
      </ol>
   </nav>
</div>
@stop

@section('content')
<div class="row">
   <div class="col-md-8">
      <div class="card card-primary">
         <div class="card-header">
            <h3 class="card-title">{{ __('Información del Permiso') }}</h3>
         </div>

         <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf
            <div class="card-body">
               <div class="form-group">
                  <label for="name">{{ __('Nombre del Permiso') }} <span class="text-danger">*</span></label>
                  <input type="text" class="form-control @error('name') is-invalid @enderror"
                     id="name" name="name" value="{{ old('name') }}" required>
                  @error('name')
                  <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                  <small class="form-text text-muted">
                     {{ __('Formato recomendado: categoría.acción (ej: works.create, users.delete, reports.view)') }}
                  </small>
               </div>

               <div class="form-group">
                  <label for="description">{{ __('Descripción (Opcional)') }}</label>
                  <textarea class="form-control @error('description') is-invalid @enderror"
                     id="description" name="description" rows="3"
                     placeholder="{{ __('Describe qué permite hacer este permiso...') }}">{{ old('description') }}</textarea>
                  @error('description')
                  <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
               </div>

               <div class="form-group">
                  <label>{{ __('Asignar a Roles') }}</label>
                  <div class="card">
                     <div class="card-body">
                        <div class="row">
                           @foreach($roles as $role)
                           <div class="col-md-6 mb-2">
                              <div class="icheck-primary">
                                 <input type="checkbox" id="role_{{ $role->id }}" name="roles[]"
                                    value="{{ $role->id }}"
                                    {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                 <label for="role_{{ $role->id }}">
                                    <strong>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $role->users->count() }} {{ __('usuarios') }}</small>
                                 </label>
                              </div>
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
                  @error('roles')
                  <span class="text-danger">{{ $message }}</span>
                  @enderror
               </div>
            </div>

            <div class="card-footer">
               <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i> {{ __('Crear Permiso') }}
               </button>
               <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                  <i class="fas fa-times"></i> {{ __('Cancelar') }}
               </a>
            </div>
         </form>
      </div>
   </div>

   <div class="col-md-4">
      <div class="card card-info">
         <div class="card-header">
            <h3 class="card-title">{{ __('Guía de Permisos') }}</h3>
         </div>
         <div class="card-body">
            <h6><strong>{{ __('Convenciones de Nomenclatura:') }}</strong></h6>
            <ul class="list-unstyled">
               <li><code>categoría.acción</code></li>
               <li><code>recurso.operación</code></li>
            </ul>

            <h6><strong>{{ __('Ejemplos:') }}</strong></h6>
            <ul class="list-unstyled">
               <li><code>works.create</code> - Crear trabajos</li>
               <li><code>works.view.all</code> - Ver todos los trabajos</li>
               <li><code>users.manage</code> - Gestionar usuarios</li>
               <li><code>reports.export</code> - Exportar reportes</li>
            </ul>

            <hr>

            <h6><strong>{{ __('Categorías Disponibles:') }}</strong></h6>
            <div class="mb-2">
               @foreach($categories as $category)
               <span class="badge badge-secondary mr-1 mb-1">{{ $category }}</span>
               @endforeach
            </div>

            <hr>

            <h6><strong>{{ __('Buenas Prácticas:') }}</strong></h6>
            <ul>
               <li>{{ __('Usa nombres descriptivos y claros') }}</li>
               <li>{{ __('Mantén consistencia con permisos existentes') }}</li>
               <li>{{ __('Evita permisos demasiado generales') }}</li>
               <li>{{ __('Considera el principio de menor privilegio') }}</li>
            </ul>
         </div>
      </div>

      <div class="card card-warning">
         <div class="card-header">
            <h3 class="card-title">{{ __('Importante') }}</h3>
         </div>
         <div class="card-body">
            <div class="alert alert-warning">
               <i class="fas fa-exclamation-triangle"></i>
               {{ __('Los permisos una vez asignados a roles afectan inmediatamente a todos los usuarios que tienen esos roles.') }}
            </div>
            <p>{{ __('Asegúrate de probar el nuevo permiso en un entorno de desarrollo antes de aplicarlo en producción.') }}</p>
         </div>
      </div>
   </div>
</div>
@stop

@push('scripts')
<script>
   // Auto-completar categorías basado en permisos existentes
   document.getElementById('name').addEventListener('input', function() {
      const value = this.value;
      const parts = value.split('.');

      if (parts.length >= 2) {
         const category = parts[0];
         // Aquí podrías agregar lógica para mostrar sugerencias
         // basadas en las categorías existentes
      }
   });

   // Validación en tiempo real
   document.getElementById('name').addEventListener('blur', function() {
      const value = this.value;
      const pattern = /^[a-z_]+\.[a-z_\.]+$/;

      if (value && !pattern.test(value)) {
         this.classList.add('is-invalid');
         // Mostrar mensaje de error personalizado
      } else {
         this.classList.remove('is-invalid');
      }
   });
</script>
@endpush