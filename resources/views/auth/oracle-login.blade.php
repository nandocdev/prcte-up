@extends('adminlte::master')

@section('adminlte_css')
@stack('css')
@php $adminlte = config('adminlte'); @endphp
@endsection

@section('body_class', 'login-page')

@section('body')
<div class="login-box">
    <div class="login-logo">
        <a href="{{ url('/') }}">
            @if(config('adminlte.logo_img'))
                <img src="{{ asset(config('adminlte.logo_img')) }}" alt="{{ config('adminlte.logo') ?: 'AdminLTE' }}" class="brand-image">
            @else
                <b>{{ config('adminlte.logo') ?: 'VIEX' }}</b>
            @endif
        </a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">{{ __('Iniciar Sesión') }}</p>

            <form action="{{ route('oracle.login') }}" method="post">
                @csrf

                {{-- Estamento --}}
                <div class="input-group mb-3">
                    <select name="estamento" class="form-control @error('estamento') is-invalid @enderror" required>
                        <option value="">{{ __('Seleccionar Estamento') }}</option>
                        <option value="P" {{ old('estamento') == 'P' ? 'selected' : '' }}>{{ __('Profesor') }}</option>
                        <option value="A" {{ old('estamento') == 'A' ? 'selected' : '' }}>{{ __('Administrativo') }}</option>
                        <option value="E" {{ old('estamento') == 'E' ? 'selected' : '' }}>{{ __('Estudiante') }}</option>
                    </select>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    @error('estamento')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- Cédula --}}
                <div class="form-group">
                    <label for="cedula">{{ __('Cédula') }}</label>
                    <div class="row">
                        <div class="col-3">
                            <input type="text" name="provincia" class="form-control @error('provincia') is-invalid @enderror"
                                   placeholder="Provincia" maxlength="1" value="{{ old('provincia') }}" required>
                            @error('provincia')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-3">
                            <input type="text" name="clase" class="form-control @error('clase') is-invalid @enderror"
                                   placeholder="Clase" maxlength="1" value="{{ old('clase') }}" required>
                            @error('clase')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-3">
                            <input type="text" name="tomo" class="form-control @error('tomo') is-invalid @enderror"
                                   placeholder="Tomo" maxlength="4" value="{{ old('tomo') }}" required>
                            @error('tomo')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-3">
                            <input type="text" name="folio" class="form-control @error('folio') is-invalid @enderror"
                                   placeholder="Folio" maxlength="6" value="{{ old('folio') }}" required>
                            @error('folio')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Contraseña --}}
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="{{ __('Contraseña') }}" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                {{-- Recordar --}}
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">
                                {{ __('Recordarme') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">
                            {{ __('Iniciar Sesión') }}
                        </button>
                    </div>
                </div>

                {{-- Errores generales --}}
                @if ($errors->has('credentials'))
                    <div class="alert alert-danger mt-3">
                        {{ $errors->first('credentials') }}
                    </div>
                @endif
            </form>

            @if (Route::has('password.request'))
                <p class="mb-1">
                    <a href="{{ route('password.request') }}">{{ __('¿Olvidaste tu contraseña?') }}</a>
                </p>
            @endif
        </div>
    </div>
</div>
@endsection

@section('adminlte_js')
@stack('js')
@endsection
