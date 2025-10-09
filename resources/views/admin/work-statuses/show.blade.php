@extends('adminlte::page')

@section('title', $workStatus->name)

@section('content_header')
<div class="row">
    <div class="col-sm-8">
        <h1 class="m-0">
            <i class="fas fa-traffic-light"></i>
            {{ $workStatus->name }}
        </h1>
    </div>
    <div class="col-sm-4">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.work-statuses.index') }}">{{ __('Estados de trabajo') }}</a></li>
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
                    {{ __('Informacion del estado') }}
                </h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">{{ __('Nombre') }}</dt>
                    <dd class="col-sm-8">{{ $workStatus->name }}</dd>

                    <dt class="col-sm-4">{{ __('Descripcion') }}</dt>
                    <dd class="col-sm-8">
                        @if ($workStatus->description)
                            {{ $workStatus->description }}
                        @else
                            <span class="text-muted">{{ __('Sin descripcion disponible') }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">{{ __('Estado') }}</dt>
                    <dd class="col-sm-8">
                        @if ($workStatus->is_active)
                            <span class="badge badge-success"><i class="fas fa-check"></i> {{ __('Activo') }}</span>
                        @else
                            <span class="badge badge-secondary"><i class="fas fa-pause"></i> {{ __('Inactivo') }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">{{ __('Trabajos vigentes') }}</dt>
                    <dd class="col-sm-8"><span class="badge badge-info">{{ $workStatus->current_works_count }}</span></dd>

                    <dt class="col-sm-4">{{ __('Transiciones registradas') }}</dt>
                    <dd class="col-sm-8">
                        <span class="badge badge-primary">
                            {{ $workStatus->transitions_from_count + $workStatus->transitions_to_count }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">{{ __('Creado el') }}</dt>
                    <dd class="col-sm-8">{{ optional($workStatus->created_at)->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-4">{{ __('Actualizado el') }}</dt>
                    <dd class="col-sm-8">{{ optional($workStatus->updated_at)->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.work-statuses.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('Volver al listado') }}
                </a>
                <a href="{{ route('admin.work-statuses.edit', $workStatus) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> {{ __('Editar') }}
                </a>
            </div>
        </div>
    </div>
</div>
@stop
