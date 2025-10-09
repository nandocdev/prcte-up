@extends('adminlte::page')

@section('title', $evaluationCriteria->name)

@section('content_header')
<div class="row">
    <div class="col-sm-8">
        <h1 class="m-0">
            <i class="fas fa-scale-balanced"></i>
            {{ $evaluationCriteria->name }}
        </h1>
    </div>
    <div class="col-sm-4">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.evaluation-criteria.index') }}">{{ __('Criterios de evaluacion') }}</a></li>
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
                    {{ __('Informacion del criterio') }}
                </h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">{{ __('Nombre') }}</dt>
                    <dd class="col-sm-8">{{ $evaluationCriteria->name }}</dd>

                    <dt class="col-sm-4">{{ __('Descripcion') }}</dt>
                    <dd class="col-sm-8">{{ $evaluationCriteria->description }}</dd>

                    <dt class="col-sm-4">{{ __('Categoria') }}</dt>
                    <dd class="col-sm-8">{{ $evaluationCriteria->category ?? __('Sin categoria') }}</dd>

                    <dt class="col-sm-4">{{ __('Puntuacion maxima') }}</dt>
                    <dd class="col-sm-8">{{ $evaluationCriteria->max_score }}</dd>

                    <dt class="col-sm-4">{{ __('Peso') }}</dt>
                    <dd class="col-sm-8">{{ $evaluationCriteria->weight }}%</dd>

                    <dt class="col-sm-4">{{ __('Orden') }}</dt>
                    <dd class="col-sm-8">{{ $evaluationCriteria->order }}</dd>

                    <dt class="col-sm-4">{{ __('Estado') }}</dt>
                    <dd class="col-sm-8">
                        @if ($evaluationCriteria->is_active)
                        <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Activo') }}</span>
                        @else
                        <span class="badge badge-secondary"><i class="fas fa-pause"></i> {{ __('Inactivo') }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">{{ __('Es obligatorio') }}</dt>
                    <dd class="col-sm-8">
                        @if ($evaluationCriteria->is_required)
                        <span class="badge badge-primary"><i class="fas fa-flag"></i> {{ __('Si') }}</span>
                        @else
                        <span class="badge badge-warning text-dark"><i class="fas fa-flag"></i> {{ __('No') }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">{{ __('Evaluaciones asociadas') }}</dt>
                    <dd class="col-sm-8"><span class="badge badge-info">{{ $evaluationCriteria->evaluation_details_count }}</span></dd>

                    <dt class="col-sm-4">{{ __('Creado el') }}</dt>
                    <dd class="col-sm-8">{{ optional($evaluationCriteria->created_at)->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-4">{{ __('Actualizado el') }}</dt>
                    <dd class="col-sm-8">{{ optional($evaluationCriteria->updated_at)->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.evaluation-criteria.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Volver al listado') }}
                </a>
                <a href="{{ route('admin.evaluation-criteria.edit', $evaluationCriteria) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> {{ __('Editar') }}
                </a>
            </div>
        </div>
    </div>
</div>
@stop
