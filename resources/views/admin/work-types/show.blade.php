@extends('adminlte::page')

@section('title', $workType->name)

@section('content_header')
<div class="row">
   <div class="col-sm-8">
      <h1 class="m-0">
         <i class="fas fa-tag"></i>
         {{ $workType->name }}
      </h1>
   </div>
   <div class="col-sm-4">
      <ol class="breadcrumb float-sm-right">
         <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
         <li class="breadcrumb-item"><a href="{{ route('admin.work-types.index') }}">{{ __('Tipos de trabajo') }}</a></li>
         <li class="breadcrumb-item active">{{ __('Detalle') }}</li>
      </ol>
   </div>
</div>
@stop

@section('content')
<div class="row justify-content-center">
   <div class="col-lg-8">
      <div class="card">
         <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">
               <i class="fas fa-info-circle"></i>
               {{ __('Informacion del tipo de trabajo') }}
            </h3>
         </div>
         <div class="card-body">
            <dl class="row">
               <dt class="col-sm-4">{{ __('Nombre') }}</dt>
               <dd class="col-sm-8">{{ $workType->name }}</dd>

               <dt class="col-sm-4">{{ __('Descripcion') }}</dt>
               <dd class="col-sm-8">
                  @if ($workType->description)
                  {{ $workType->description }}
                  @else
                  <span class="text-muted">{{ __('Sin descripcion disponible') }}</span>
                  @endif
               </dd>

               <dt class="col-sm-4">{{ __('Estado') }}</dt>
               <dd class="col-sm-8">
                  @if ($workType->is_active)
                  <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Activo') }}</span>
                  @else
                  <span class="badge badge-secondary"><i class="fas fa-pause"></i> {{ __('Inactivo') }}</span>
                  @endif
               </dd>

               <dt class="col-sm-4">{{ __('Trabajos asociados') }}</dt>
               <dd class="col-sm-8">
                  <span class="badge badge-info">{{ $workType->works_count }} {{ __('registros') }}</span>
               </dd>

               <dt class="col-sm-4">{{ __('Creado el') }}</dt>
               <dd class="col-sm-8">{{ optional($workType->created_at)->format('d/m/Y H:i') }}</dd>

               <dt class="col-sm-4">{{ __('Actualizado el') }}</dt>
               <dd class="col-sm-8">{{ optional($workType->updated_at)->format('d/m/Y H:i') }}</dd>
            </dl>
         </div>
         <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('admin.work-types.index') }}" class="btn btn-secondary">
               <i class="fas fa-arrow-left"></i> {{ __('Volver al listado') }}
            </a>
            <a href="{{ route('admin.work-types.edit', $workType) }}" class="btn btn-primary">
               <i class="fas fa-edit"></i> {{ __('Editar') }}
            </a>
         </div>
      </div>
   </div>
</div>
@stop