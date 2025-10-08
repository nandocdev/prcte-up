@extends('layouts.app')

@section('title', 'Mi Perfil - VIEX')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('Mi Perfil') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Mi Perfil') }}</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <!-- Información del Perfil -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-2"></i>
                        {{ __('Información Personal') }}
                    </h3>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key mr-2"></i>
                        {{ __('Cambiar Contraseña') }}
                    </h3>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Eliminar Cuenta -->
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trash mr-2"></i>
                        {{ __('Eliminar Cuenta') }}
                    </h3>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Resumen del Usuario -->
            <div class="card card-widget widget-user">
                <div class="widget-user-header bg-info">
                    <h3 class="widget-user-username">{{ Auth::user()->name }}</h3>
                    <h5 class="widget-user-desc">{{ Auth::user()->email }}</h5>
                </div>
                <div class="widget-user-image">
                    <img class="img-circle elevation-2"
                        src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=007bff&color=fff' }}"
                        alt="User Avatar">
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <span
                                    class="description-header">{{ Auth::user()->worksAsResponsible()->count() }}</span>
                                <span class="description-text">{{ __('TRABAJOS') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4 border-right">
                            <div class="description-block">
                                <span
                                    class="description-header">{{ Auth::user()->worksAsResponsible()->where('is_draft', false)->count() }}</span>
                                <span class="description-text">{{ __('ENVIADOS') }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="description-block">
                                <span class="description-header">0</span>
                                <span class="description-text">{{ __('CERTIFICADOS') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Información de la Cuenta') }}</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">{{ __('Nombre:') }}</dt>
                        <dd class="col-sm-7">{{ Auth::user()->name }}</dd>

                        <dt class="col-sm-5">{{ __('Email:') }}</dt>
                        <dd class="col-sm-7">{{ Auth::user()->email }}</dd>

                        @if(Auth::user()->professor_code)
                            <dt class="col-sm-5">{{ __('Código:') }}</dt>
                            <dd class="col-sm-7">{{ Auth::user()->professor_code }}</dd>
                        @endif

                        @if(Auth::user()->cedula)
                            <dt class="col-sm-5">{{ __('Cédula:') }}</dt>
                            <dd class="col-sm-7">{{ Auth::user()->cedula }}</dd>
                        @endif

                        <dt class="col-sm-5">{{ __('Estado:') }}</dt>
                        <dd class="col-sm-7">
                            @if(Auth::user()->is_active)
                                <span class="badge badge-success">{{ __('Activo') }}</span>
                            @else
                                <span class="badge badge-danger">{{ __('Inactivo') }}</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">{{ __('Verificado:') }}</dt>
                        <dd class="col-sm-7">
                            @if(Auth::user()->email_verified_at)
                                <span class="badge badge-success">{{ __('Verificado') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('No verificado') }}</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Acciones Rápidas') }}</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('works.index') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-folder-open mr-2"></i>
                        {{ __('Ver Mis Trabajos') }}
                    </a>
                    <a href="{{ route('works.create') }}" class="btn btn-success btn-block mb-2">
                        <i class="fas fa-plus mr-2"></i>
                        {{ __('Crear Nuevo Trabajo') }}
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-info btn-block">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        {{ __('Volver al Dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@push('css')
    <style>
        .description-block {
            text-align: center;
        }

        .description-header {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007bff;
        }

        .widget-user-image img {
            width: 90px;
            height: 90px;
        }
    </style>
@endpush
