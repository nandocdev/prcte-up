@extends('adminlte::page')

@section('title', __('Estados de trabajo'))

@section('content_header')
<div class="row">
   <div class="col-sm-6">
      <h1 class="m-0">
         <i class="fas fa-traffic-light"></i>
         {{ __('Estados de trabajo') }}
      </h1>
   </div>
   <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
         <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
         <li class="breadcrumb-item"><a href="#">{{ __('Configuracion') }}</a></li>
         <li class="breadcrumb-item active">{{ __('Estados de trabajo') }}</li>
      </ol>
   </div>
</div>
@stop

@section('content')
<div class="card" id="work-statuses-catalog"
   data-confirm-title="{{ __('Estas seguro?') }}"
   data-confirm-text="{{ __('Esta accion no se puede deshacer.') }}"
   data-confirm-accept="{{ __('Si, eliminar') }}"
   data-confirm-cancel="{{ __('Cancelar') }}">
   <div class="card-header">
      <h3 class="card-title">
         <i class="fas fa-list"></i>
         {{ __('Catalogo de estados de trabajo') }}
      </h3>
      <div class="card-tools">
         <a href="{{ route('admin.work-statuses.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> {{ __('Nuevo estado') }}
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
                  <th>{{ __('Trabajos vigentes') }}</th>
                  <th>{{ __('Transiciones registradas') }}</th>
                  <th class="text-center" style="width: 150px;">{{ __('Acciones') }}</th>
               </tr>
            </thead>
            <tbody>
               @forelse ($statuses as $status)
               <tr>
                  <td>{{ $status->id }}</td>
                  <td class="font-weight-bold">{{ $status->name }}</td>
                  <td>
                     @if ($status->description)
                     {{ \Illuminate\Support\Str::limit($status->description, 120) }}
                     @else
                     <span class="text-muted">{{ __('Sin descripcion') }}</span>
                     @endif
                  </td>
                  <td>
                     @if ($status->is_active)
                     <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Activo') }}</span>
                     @else
                     <span class="badge badge-secondary"><i class="fas fa-pause"></i> {{ __('Inactivo') }}</span>
                     @endif
                  </td>
                  <td>
                     <span class="badge badge-info">{{ $status->current_works_count }}</span>
                  </td>
                  <td>
                     <span class="badge badge-primary">
                        {{ $status->transitions_from_count + $status->transitions_to_count }}
                     </span>
                  </td>
                  <td class="text-center">
                     <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('admin.work-statuses.show', $status) }}" class="btn btn-info" title="{{ __('Ver detalles') }}">
                           <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.work-statuses.edit', $status) }}" class="btn btn-warning" title="{{ __('Editar') }}">
                           <i class="fas fa-edit"></i>
                        </a>
                        @if ($status->hasAssociations())
                        <button type="button" class="btn btn-secondary" disabled title="{{ __('No se puede eliminar: tiene registros asociados') }}">
                           <i class="fas fa-ban"></i>
                        </button>
                        @else
                        <button type="button" class="btn btn-danger delete-work-status"
                           data-status-id="{{ $status->id }}" title="{{ __('Eliminar') }}">
                           <i class="fas fa-trash"></i>
                        </button>
                        @endif
                     </div>
                     <form id="delete-work-status-{{ $status->id }}" action="{{ route('admin.work-statuses.destroy', $status) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                     </form>
                  </td>
               </tr>
               @empty
               <tr>
                  <td colspan="7" class="text-center text-muted py-5">
                     <i class="fas fa-traffic-light fa-2x mb-3"></i>
                     <p class="mb-2">{{ __('No hay estados registrados.') }}</p>
                     <a href="{{ route('admin.work-statuses.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Crear estado') }}
                     </a>
                  </td>
               </tr>
               @endforelse
            </tbody>
         </table>
      </div>
   </div>

   @if ($statuses->hasPages())
   <div class="card-footer">
      {{ $statuses->links() }}
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
   const statusesContainer = document.getElementById('work-statuses-catalog');
   const statusMessages = {
      title: (statusesContainer && statusesContainer.getAttribute('data-confirm-title')) || 'Confirmar',
      text: (statusesContainer && statusesContainer.getAttribute('data-confirm-text')) || 'Esta accion no se puede deshacer.',
      accept: (statusesContainer && statusesContainer.getAttribute('data-confirm-accept')) || 'Aceptar',
      cancel: (statusesContainer && statusesContainer.getAttribute('data-confirm-cancel')) || 'Cancelar'
   };

   function confirmWorkStatusDelete(statusId) {
      Swal.fire({
         title: statusMessages.title,
         text: statusMessages.text,
         icon: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#d33',
         cancelButtonColor: '#3085d6',
         confirmButtonText: statusMessages.accept,
         cancelButtonText: statusMessages.cancel
      }).then((result) => {
         if (result.isConfirmed) {
            document.getElementById('delete-work-status-' + statusId).submit();
         }
      });
   }

   document.querySelectorAll('.delete-work-status').forEach((button) => {
      button.addEventListener('click', () => {
         const statusId = button.getAttribute('data-status-id');
         confirmWorkStatusDelete(statusId);
      });
   });

   setTimeout(() => {
      $('.alert').fadeOut('slow');
   }, 5000);
</script>
@stop