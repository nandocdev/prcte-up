@extends('adminlte::page')

@section('title', __('Editar criterio de evaluacion'))

@section('content_header')
<div class="row">
    <div class="col-sm-6">
        <h1 class="m-0">
            <i class="fas fa-edit"></i>
            {{ __('Editar criterio de evaluacion') }}
        </h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.evaluation-criteria.index') }}">{{ __('Criterios de evaluacion') }}</a></li>
            <li class="breadcrumb-item active">{{ $evaluationCriteria->name }}</li>
        </ol>
    </div>
</div>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fas fa-clipboard-list"></i>
                    {{ __('Actualizar criterio') }}
                </h3>
            </div>
            <form action="{{ route('admin.evaluation-criteria.update', $evaluationCriteria) }}" method="POST">
                @csrf
                @method('PUT')
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
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" maxlength="200" value="{{ old('name', $evaluationCriteria->name) }}" required>
                        @error('name')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('Descripcion') }} <span class="text-danger">*</span></label>
                        <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $evaluationCriteria->description) }}</textarea>
                        @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="category">{{ __('Categoria') }}</label>
                        <input type="text" id="category" name="category" class="form-control @error('category') is-invalid @enderror" maxlength="100" value="{{ old('category', $evaluationCriteria->category) }}">
                        @error('category')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="max_score">{{ __('Puntuacion maxima') }} <span class="text-danger">*</span></label>
                            <input type="number" id="max_score" name="max_score" class="form-control @error('max_score') is-invalid @enderror" min="1" max="100" value="{{ old('max_score', $evaluationCriteria->max_score) }}" required>
                            @error('max_score')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="weight">{{ __('Peso (%)') }} <span class="text-danger">*</span></label>
                            <input type="number" id="weight" name="weight" class="form-control @error('weight') is-invalid @enderror" min="0" max="100" value="{{ old('weight', $evaluationCriteria->weight) }}" required>
                            @error('weight')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-md-4">
                            <label for="order">{{ __('Orden') }} <span class="text-danger">*</span></label>
                            <input type="number" id="order" name="order" class="form-control @error('order') is-invalid @enderror" min="0" max="1000" value="{{ old('order', $evaluationCriteria->order) }}" required>
                            @error('order')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $evaluationCriteria->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">{{ __('Criterio disponible para nuevas evaluaciones') }}</label>
                        </div>
                        @error('is_active')
                        <span class="text-danger d-block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="hidden" name="is_required" value="0">
                            <input type="checkbox" class="custom-control-input" id="is_required" name="is_required" value="1" {{ old('is_required', $evaluationCriteria->is_required) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_required">{{ __('El criterio es obligatorio para los evaluadores') }}</label>
                        </div>
                        @error('is_required')
                        <span class="text-danger d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.evaluation-criteria.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Cancelar') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> {{ __('Guardar cambios') }}
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
