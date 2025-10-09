@extends('adminlte::page')

@section('title', __('Detalle de la unidad'))

@section('content_header')
<div class="row">
   <div class="col-sm-6">
      <h1>
         <i class="fas fa-university"></i>
         {{ $unit->name }}
      </h1>
   </div>
   <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
         <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
         <li class="breadcrumb-item"><a href="{{ route('admin.organizational-units.index') }}">{{ __('Unidades organizacionales') }}</a></li>
         <li class="breadcrumb-item active">{{ __('Detalle') }}</li>
      </ol>
   </div>
</div>
@stop

@section('content')
<div class="row">
   <div class="col-md-8">
      <div class="card">
         <div class="card-header">
            <h3 class="card-title mb-0">
               <i class="fas fa-info-circle"></i>
               {{ __('Información general') }}
            </h3>
         </div>
         <div class="card-body">
            <dl class="row">
               <dt class="col-sm-4">{{ __('Nombre') }}</dt>
               <dd class="col-sm-8">{{ $unit->name }}</dd>

               <dt class="col-sm-4">{{ __('Tipo') }}</dt>
               <dd class="col-sm-8">{{ $unit->typeLabel() }}</dd>

               <dt class="col-sm-4">{{ __('Unidad padre') }}</dt>
               <dd class="col-sm-8">
                  @if($unit->parent)
                  {{ $unit->parent->name }}
                  @else
                  <span class="text-muted">{{ __('Sin unidad padre') }}</span>
                  @endif
               </dd>

               <dt class="col-sm-4">{{ __('Subunidades') }}</dt>
               <dd class="col-sm-8">{{ $unit->children->count() }}</dd>

               <dt class="col-sm-4">{{ __('Usuarios asociados') }}</dt>
               <dd class="col-sm-8">{{ $unit->users->count() }}</dd>
            </dl>
         </div>
         <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('admin.organizational-units.index') }}" class="btn btn-secondary">
               <i class="fas fa-arrow-left"></i> {{ __('Volver al listado') }}
            </a>
            <a href="{{ route('admin.organizational-units.edit', $unit) }}" class="btn btn-primary">
               <i class="fas fa-edit"></i> {{ __('Editar unidad') }}
            </a>
         </div>
      </div>
   </div>

   <div class="col-md-4">
      <div class="card card-outline card-primary">
         <div class="card-header">
            <h3 class="card-title mb-0">
               <i class="fas fa-layer-group"></i>
               {{ __('Subunidades') }}
            </h3>
         </div>
         <div class="card-body">
            @if($unit->children->isEmpty())
            <p class="text-muted mb-0">{{ __('Esta unidad no tiene subunidades registradas.') }}</p>
            @else
            <ul class="list-unstyled mb-0">
               @foreach($unit->children as $child)
               <li class="mb-2">
                  <strong>{{ $child->name }}</strong>
                  <br>
                  <small class="text-muted">{{ $child->typeLabel() }}</small>
               </li>
               @endforeach
            </ul>
            @endif
         </div>
      </div>

      <div class="card card-outline card-info">
         <div class="card-header">
            <h3 class="card-title mb-0">
               <i class="fas fa-users"></i>
               {{ __('Usuarios asignados') }}
            </h3>
         </div>
         <div class="card-body">
            @if($unit->users->isEmpty())
            <p class="text-muted mb-0">{{ __('No hay usuarios asociados a esta unidad.') }}</p>
            @else
            <ul class="list-unstyled mb-0">
               @foreach($unit->users as $user)
               <li class="mb-2">
                  <strong>{{ $user->name }}</strong>
                  <br>
                  <small class="text-muted">{{ $user->email }}</small>
               </li>
               @endforeach
            </ul>
            @endif
         </div>
      </div>
   </div>
</div>
@stop