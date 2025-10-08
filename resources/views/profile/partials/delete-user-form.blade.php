<div class="callout callout-danger">
    <h5><i class="fas fa-exclamation-triangle mr-2"></i>{{ __('Zona Peligrosa') }}</h5>
    <p class="mb-3">
        {{ __('Una vez que elimines tu cuenta, todos sus recursos y datos se eliminarán permanentemente. Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.') }}
    </p>

    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAccountModal">
        <i class="fas fa-trash mr-2"></i>
        {{ __('Eliminar Cuenta') }}
    </button>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ __('Confirmar Eliminación de Cuenta') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-body">
                    <div class="callout callout-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>{{ __('¿Estás seguro de que quieres eliminar tu cuenta?') }}</strong>
                    </div>

                    <p>
                        {{ __('Una vez que elimines tu cuenta, todos sus recursos y datos se eliminarán permanentemente. Por favor, introduce tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta.') }}
                    </p>

                    <div class="form-group">
                        <label for="password" class="sr-only">{{ __('Contraseña') }}</label>
                        <div class="input-group">
                            <input id="password" name="password" type="password"
                                class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                placeholder="{{ __('Tu contraseña actual') }}" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </div>
                            </div>
                            @error('password', 'userDeletion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-2"></i>
                        {{ __('Cancelar') }}
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-2"></i>
                        {{ __('Eliminar Cuenta Permanentemente') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#deleteAccountModal').modal('show');
            });
        </script>
    @endpush
@endif
