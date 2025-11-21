@php
    $fieldIndex = $index;
@endphp
<div class="card participant-row mb-3" data-index="{{ $fieldIndex }}">
    <div class="card-body pt-3 pb-1">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="participant_name_{{ $fieldIndex }}">
                    <strong>{{ __('Nombre Completo') }}</strong>
                </label>
                <input type="text" class="form-control" id="participant_name_{{ $fieldIndex }}"
                    name="participants[{{ $fieldIndex }}][name]" value="{{ $participant['name'] ?? '' }}" required>
            </div>
            <div class="form-group col-md-4">
                <label for="participant_email_{{ $fieldIndex }}">
                    <strong>{{ __('Correo Electrónico') }}</strong>
                </label>
                <input type="email" class="form-control" id="participant_email_{{ $fieldIndex }}"
                    name="participants[{{ $fieldIndex }}][email]" value="{{ $participant['email'] ?? '' }}">
            </div>
            <div class="form-group col-md-4">
                <label for="participant_phone_{{ $fieldIndex }}">
                    <strong>{{ __('Teléfono') }}</strong>
                </label>
                <input type="text" class="form-control" id="participant_phone_{{ $fieldIndex }}"
                    name="participants[{{ $fieldIndex }}][phone]" value="{{ $participant['phone'] ?? '' }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="participant_institution_{{ $fieldIndex }}">
                    <strong>{{ __('Institución / Unidad') }}</strong>
                </label>
                <input type="text" class="form-control" id="participant_institution_{{ $fieldIndex }}"
                    name="participants[{{ $fieldIndex }}][institution]" value="{{ $participant['institution'] ?? '' }}">
            </div>
            <div class="form-group col-md-3">
                <label for="participant_role_{{ $fieldIndex }}">
                    <strong>{{ __('Rol dentro del trabajo') }}</strong>
                </label>
                <input type="text" class="form-control" id="participant_role_{{ $fieldIndex }}"
                    name="participants[{{ $fieldIndex }}][role]"
                    value="{{ $participant['role'] ?? __('Participante') }}">
            </div>
            <div class="form-group col-md-3">
                <label class="d-block"><strong>{{ __('Responsable Clave') }}</strong></label>
                <input type="hidden" name="participants[{{ $fieldIndex }}][is_primary]" value="0">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input participant-primary-toggle"
                        id="participant_primary_{{ $fieldIndex }}" value="1"
                        name="participants[{{ $fieldIndex }}][is_primary]" {{ !empty($participant['is_primary']) ? 'checked' : '' }}>
                    <label class="custom-control-label" for="participant_primary_{{ $fieldIndex }}">{{ __('Sí') }}</label>
                </div>
            </div>
            <div class="col-md-2 text-right align-self-end">
                <button type="button" class="btn btn-outline-danger btn-sm remove-participant">
                    <i class="fas fa-times"></i> {{ __('Eliminar') }}
                </button>
            </div>
        </div>
    </div>
</div>
