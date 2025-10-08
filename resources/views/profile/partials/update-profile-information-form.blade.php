<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="form-group">
        <label for="name">{{ __('Nombre') }}</label>
        <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="email">{{ __('Correo Electrónico') }}</label>
        <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
            value="{{ old('email', $user->email) }}" required autocomplete="username">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
            <div class="alert alert-warning mt-2">
                <h6>{{ __('Tu dirección de correo electrónico no está verificada.') }}</h6>
                <p class="mb-2">
                    <button form="send-verification" class="btn btn-link p-0" type="submit">
                        {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success">
                        {{ __('Se ha enviado un nuevo enlace de verificación a tu dirección de correo.') }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    @if(isset($user->professor_code))
        <div class="form-group">
            <label for="professor_code">{{ __('Código de Profesor') }}</label>
            <input id="professor_code" name="professor_code" type="text"
                class="form-control @error('professor_code') is-invalid @enderror"
                value="{{ old('professor_code', $user->professor_code) }}" autocomplete="professor_code">
            @error('professor_code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endif

    @if(isset($user->cedula))
        <div class="form-group">
            <label for="cedula">{{ __('Cédula') }}</label>
            <input id="cedula" name="cedula" type="text" class="form-control @error('cedula') is-invalid @enderror"
                value="{{ old('cedula', $user->cedula) }}" autocomplete="cedula">
            @error('cedula')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @endif

    <div class="form-group mb-0">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-2"></i>
            {{ __('Guardar Cambios') }}
        </button>

        @if (session('status') === 'profile-updated')
            <span class="text-success ml-2">
                <i class="fas fa-check-circle"></i>
                {{ __('Información actualizada correctamente.') }}
            </span>
        @endif
    </div>
</form>
