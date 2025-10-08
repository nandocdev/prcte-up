<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="callout callout-info">
        <i class="fas fa-info-circle mr-2"></i>
        {{ __('Asegúrate de usar una contraseña larga y aleatoria para mantener tu cuenta segura.') }}
    </div>

    <div class="form-group">
        <label for="update_password_current_password">{{ __('Contraseña Actual') }}</label>
        <div class="input-group">
            <input id="update_password_current_password" name="current_password" type="password"
                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                autocomplete="current-password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-group">
        <label for="update_password_password">{{ __('Nueva Contraseña') }}</label>
        <div class="input-group">
            <input id="update_password_password" name="password" type="password"
                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                autocomplete="new-password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <i class="fas fa-key"></i>
                </div>
            </div>
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="form-text text-muted">
            {{ __('La contraseña debe tener al menos 8 caracteres.') }}
        </small>
    </div>

    <div class="form-group">
        <label for="update_password_password_confirmation">{{ __('Confirmar Nueva Contraseña') }}</label>
        <div class="input-group">
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                autocomplete="new-password">
            <div class="input-group-append">
                <div class="input-group-text">
                    <i class="fas fa-key"></i>
                </div>
            </div>
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-group mb-0">
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-key mr-2"></i>
            {{ __('Cambiar Contraseña') }}
        </button>

        @if (session('status') === 'password-updated')
            <span class="text-success ml-2">
                <i class="fas fa-check-circle"></i>
                {{ __('Contraseña actualizada correctamente.') }}
            </span>
        @endif
    </div>
</form>
