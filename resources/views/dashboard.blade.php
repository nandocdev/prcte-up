@extends('layouts.app')

@section('title', 'Dashboard - VIEX')

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ __('Dashboard Principal') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">{{ __('Dashboard') }}</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-folder-open"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Mis Trabajos') }}</span>
                    <span class="info-box-number" id="total-works">
                        {{ auth()->user()->worksAsResponsible()->count() ?? 0 }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-edit"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Borradores') }}</span>
                    <span class="info-box-number" id="draft-works">
                        {{ auth()->user()->worksAsResponsible()->where('is_draft', true)->count() ?? 0 }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Aprobados') }}</span>
                    <span class="info-box-number" id="approved-works">0</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-certificate"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Certificados') }}</span>
                    <span class="info-box-number" id="certified-works">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de bienvenida -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-hand-paper mr-2"></i>
                        {{ __('¡Bienvenido al Sistema VIEX!') }}
                    </h3>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        {{ __('Hola') }} <strong>{{ auth()->user()->name }}</strong>,
                        {{ __('bienvenido al Sistema de Gestión de Trabajos de Extensión de la Universidad de Panamá.') }}
                    </p>

                    <div class="callout callout-info">
                        <h5><i class="icon fas fa-info"></i> {{ __('¿Qué puedes hacer aquí?') }}</h5>
                        <ul class="mb-0">
                            <li>{{ __('Crear y gestionar tus trabajos de extensión') }}</li>
                            <li>{{ __('Hacer seguimiento al estado de revisión') }}</li>
                            <li>{{ __('Descargar certificados una vez aprobados') }}</li>
                            <li>{{ __('Colaborar con otros profesores en proyectos') }}</li>
                        </ul>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('works.index') }}" class="btn btn-primary mr-2">
                            <i class="fas fa-folder-open mr-1"></i>
                            {{ __('Ver Mis Trabajos') }}
                        </a>
                        <a href="{{ route('works.create') }}" class="btn btn-success">
                            <i class="fas fa-plus mr-1"></i>
                            {{ __('Crear Nuevo Trabajo') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Accesos rápidos -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Accesos Rápidos') }}</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="{{ route('works.create') }}" class="nav-link">
                                <i class="fas fa-plus text-success"></i>
                                {{ __('Crear Trabajo') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('works.index') }}" class="nav-link">
                                <i class="fas fa-list text-info"></i>
                                {{ __('Mis Trabajos') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('profile.edit') }}" class="nav-link">
                                <i class="fas fa-user text-primary"></i>
                                {{ __('Mi Perfil') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('testing.info') }}" class="nav-link">
                                <i class="fas fa-info-circle text-warning"></i>
                                {{ __('Información del Sistema') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Información del usuario -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Mi Información') }}</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">{{ __('Nombre:') }}</dt>
                        <dd class="col-sm-7">{{ auth()->user()->name }}</dd>

                        <dt class="col-sm-5">{{ __('Email:') }}</dt>
                        <dd class="col-sm-7">{{ auth()->user()->email }}</dd>

                        @if(auth()->user()->professor_code)
                            <dt class="col-sm-5">{{ __('Código:') }}</dt>
                            <dd class="col-sm-7">{{ auth()->user()->professor_code }}</dd>
                        @endif

                        <dt class="col-sm-5">{{ __('Registrado:') }}</dt>
                        <dd class="col-sm-7">{{ auth()->user()->getAttribute('created_at')?->format('d/m/Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad reciente (si hay trabajos) -->
    @if(auth()->user()->worksAsResponsible()->exists())
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Actividad Reciente') }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('works.index') }}" class="btn btn-tool">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Título') }}</th>
                                        <th>{{ __('Estado') }}</th>
                                        <th>{{ __('Última Actualización') }}</th>
                                        <th>{{ __('Acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(auth()->user()->worksAsResponsible()->latest('updated_at')->take(5)->get() as $work)
                                        <tr>
                                            <td>{{ Str::limit($work->title, 50) }}</td>
                                            <td>
                                                <span class="badge badge-info">
                                                    {{ $work->currentStatus->name ?? __('Sin estado') }}
                                                </span>
                                            </td>
                                            <td>{{ $work->updated_at->diffForHumans() }}</td>
                                            <td>
                                                <a href="{{ route('works.show', $work) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($work->is_draft)
                                                    <a href="{{ route('works.edit', $work) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">
                                                {{ __('No hay trabajos recientes') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@stop

@push('css')
    <style>
        .info-box-number {
            font-weight: bold;
        }

        .card-title i {
            color: #007bff;
        }

        .alert-info {
            border-left: 4px solid #17a2b8;
        }
    </style>
@endpush
