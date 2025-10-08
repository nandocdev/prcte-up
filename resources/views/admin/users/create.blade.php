@extends('adminlte::page')

@section('title', __('Crear Usuario'))

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>{{ __('Crear Usuario') }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Inicio') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">{{ __('Usuarios') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Crear') }}</li>
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

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('Nombre Completo') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('Correo Electrónico') }} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">{{ __('Contraseña') }} <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                           id="password" name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">{{ __('Confirmar Contraseña') }} <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control"
                                           id="password_confirmation" name="password_confirmation" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="professor_code">{{ __('Código de Profesor') }}</label>
                                    <input type="text" class="form-control @error('professor_code') is-invalid @enderror"
                                           id="professor_code" name="professor_code" value="{{ old('professor_code') }}">
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
                                                    {{ old('organizational_unit_id') == $unit->id ? 'selected' : '' }}>
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
                                                   {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
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
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active">{{ __('Usuario Activo') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Crear Usuario') }}
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> {{ __('Cancelar') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Información') }}</h3>
                </div>
                <div class="card-body">
                    <p><strong>{{ __('Campos obligatorios') }}</strong></p>
                    <ul class="mb-3">
                        <li>{{ __('Nombre completo') }}</li>
                        <li>{{ __('Correo electrónico') }}</li>
                        <li>{{ __('Contraseña') }}</li>
                        <li>{{ __('Unidad organizacional') }}</li>
                    </ul>

                    <p><strong>{{ __('Sobre los roles:') }}</strong></p>
                    <ul>
                        <li><strong>Profesor:</strong> {{ __('Puede crear trabajos de extensión') }}</li>
                        <li><strong>Coordinador Extensión:</strong> {{ __('Revisa trabajos de su unidad') }}</li>
                        <li><strong>Decano/Director:</strong> {{ __('Aprueba trabajos para VIEX') }}</li>
                        <li><strong>VIEX Admin:</strong> {{ __('Certifica trabajos finales') }}</li>
                        <li><strong>Super Admin:</strong> {{ __('Gestiona el sistema') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop
