@extends('adminlte::page')

@section('title', __('Editar Usuario'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>{{ __('Editar Usuario') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('Usuarios') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a></li>
                <li class="breadcrumb-item active">{{ __('Editar') }}</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Información del Usuario') }}</h3>
                </div>

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('Nombre Completo') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('Correo Electrónico') }} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">{{ __('Nueva Contraseña') }}</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password">
                                    <small class="form-text text-muted">{{ __('Dejar en blanco para mantener la contraseña actual') }}</small>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">{{ __('Confirmar Nueva Contraseña') }}</label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="professor_code">{{ __('Código de Profesor') }}</label>
                                    <input type="text" class="form-control @error('professor_code') is-invalid @enderror"
                                           id="professor_code" name="professor_code" value="{{ old('professor_code', $user->professor_code) }}">
                                    @error('professor_code')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="organizational_unit_id">{{ __('Unidad Organizacional') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('organizational_unit_id') is-invalid @enderror"
                                            id="organizational_unit_id" name="organizational_unit_id" required>
                                        <option value="">{{ __('Seleccionar unidad...') }}</option>
                                        @foreach($organizationalUnits as $unit)
                                            <option value="{{ $unit->id }}"
                                                    {{ old('organizational_unit_id', $user->main_organizational_unit_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('organizational_unit_id')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ __('Roles') }}</label>
                            <div class="row">
                                @foreach($roles as $role)
                                    <div class="col-md-4">
                                        <div class="icheck-primary">
                                            <input type="checkbox" id="role_{{ $role->id }}" name="roles[]"
                                                   value="{{ $role->name }}"
                                                   {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}>
                                            <label for="role_{{ $role->id }}">
                                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="icheck-primary">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                <label for="is_active">{{ __('Usuario Activo') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Actualizar Usuario') }}
                        </button>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Usuario Actual') }}</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img class="img-circle elevation-2"
                             src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=007bff&color=fff&size=80"
                             alt="{{ $user->name }}"
                             style="width: 80px; height: 80px;">
                        <h5 class="mt-2">{{ $user->name }}</h5>
                        <p class="text-muted">{{ $user->email }}</p>
                    </div>

                    <h6><strong>{{ __('Roles Actuales:') }}</strong></h6>
                    @forelse($user->roles as $role)
                        <span class="badge badge-secondary mr-1 mb-1">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </span>
                    @empty
                        <p class="text-muted">{{ __('Sin roles asignados') }}</p>
                    @endforelse

                    <hr>

                    <h6><strong>{{ __('Estadísticas:') }}</strong></h6>
                    <ul class="list-unstyled">
                        <li><strong>{{ __('Trabajos de extensión:') }}</strong> {{ $user->workOfExtensions->count() }}</li>
                        <li><strong>{{ __('Usuario desde:') }}</strong> {{ $user->created_at->format('d/m/Y') }}</li>
                        <li><strong>{{ __('Última actualización:') }}</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</li>
                    </ul>
                </div>
            </div>

            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Información Importante') }}</h3>
                </div>
                <div class="card-body">
                    <ul>
                        <li>{{ __('Los cambios de rol tendrán efecto inmediato') }}</li>
                        <li>{{ __('Si el usuario está inactivo, no podrá acceder al sistema') }}</li>
                        <li>{{ __('La contraseña solo se cambiará si se proporciona una nueva') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop
