@extends('adminlte::page')

@section('title', __('Nueva unidad organizacional'))

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1>
            <i class="fas fa-plus"></i>
            {{ __('Nueva unidad organizacional') }}
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.organizational-units.index') }}">{{ __('Unidades organizacionales') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Nueva') }}</li>
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
                    <i class="fas fa-university"></i>
                    {{ __('Datos de la unidad') }}
                </h3>
            </div>

            <form action="{{ route('admin.organizational-units.store') }}" method="POST">
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
                        <label for="name">{{ __('Nombre de la unidad') }} <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="type">{{ __('Tipo de unidad') }} <span class="text-danger">*</span></label>
                        <select id="type" name="type" class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">{{ __('Selecciona un tipo') }}</option>
                            @foreach($typeOptions as $value => $label)
                            <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                        @error('type')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="parent_id">{{ __('Unidad padre (opcional)') }}</label>
                        <select id="parent_id" name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                            <option value="">{{ __('Sin unidad padre') }}</option>
                            @foreach($parentUnits as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }} — {{ $parent->typeLabel() }}
                            </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.organizational-units.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Cancelar') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('Guardar unidad') }}
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