@extends('adminlte::page')

@section('title', __('Nuevo tipo de trabajo'))

@section('content_header')
<div class="row">
   <div class="col-sm-6">
      <h1 class="m-0">
         <i class="fas fa-plus"></i>
         {{ __('Nuevo tipo de trabajo') }}
      </h1>
   </div>
   <div class="col-sm-6">
      <ol class="breadcrumb float-sm-right">
         <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
         <li class="breadcrumb-item"><a href="{{ route('admin.work-types.index') }}">{{ __('Tipos de trabajo') }}</a></li>
         <li class="breadcrumb-item active">{{ __('Nuevo') }}</li>
      </ol>
   </div>
</div>
@stop

@section('content')
<div class="row justify-content-center">
   <div class="col-md-8">
      <div class="card card-primary">
         <div class="card-header">
            <h3 class="card-title mb-0">
               <i class="fas fa-tags"></i>
               {{ __('Datos del tipo de trabajo') }}
            </h3>
         </div>

         <form action="{{ route('admin.work-types.store') }}" method="POST">
            @csrf

            <div class="card-body">
               @if ($errors->any())
               <div class="alert alert-danger">
                  <h5 class="mb-2"><i class="icon fas fa-ban"></i> {{ __('Hay errores en el formulario') }}</h5>
                  <ul class="mb-0">
                     @foreach ($errors->all() as $error)
                     <li>{{ $error }}</li>
                     @endforeach
                  </ul>
               </div>
               @endif

               <div class="form-group">
                  <label for="name">{{ __('Nombre') }} <span class="text-danger">*</span></label>
                  <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                     value="{{ old('name') }}" maxlength="100" required>
                  @error('name')
                  <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
               </div>

               <div class="form-group">
                  <label for="description">{{ __('Descripcion') }}</label>
                  <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4" maxlength="1000">{{ old('description') }}</textarea>
                  @error('description')
                  <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
               </div>

               <div class="form-group">
                  <div class="custom-control custom-switch">
                     <input type="hidden" name="is_active" value="0">
                     <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                     <label class="custom-control-label" for="is_active">{{ __('Tipo disponible para nuevos trabajos') }}</label>
                  </div>
                  @error('is_active')
                  <span class="text-danger d-block">{{ $message }}</span>
                  @enderror
               </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
               <a href="{{ route('admin.work-types.index') }}" class="btn btn-secondary">
                  <i class="fas fa-arrow-left"></i> {{ __('Cancelar') }}
               </a>
               <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i> {{ __('Guardar tipo') }}
               </button>
            </div>
         </form>
      </div>
   </div>
</div>
@stop

@section('css')
<style>
   .card-primary .card-header {
      background-color: #007bff;
   }
</style>
@stop