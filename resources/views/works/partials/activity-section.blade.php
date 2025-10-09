<!-- Sección 3: Específica para Actividades de Extensión -->
@php
$activityDetail = $activityDetail ?? null;
$workTypesConfig = $workTypesConfig ?? [];
@endphp

<div class="card card-warning work-section" id="section-actividad" style="display: none;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users"></i>
            {{ __('2. Información Específica de la Actividad') }}
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Tipo de Actividad -->
        <div class="form-group">
            <label for="activity_type">
                <strong>{{ __('Tipo de Actividad') }}</strong> <span class="text-danger">*</span>
            </label>
            <select class="form-control @error('activity_type') is-invalid @enderror" id="activity_type"
                name="activity_type" data-required="true">
                <option value="">{{ __('Seleccione...') }}</option>
                @foreach(($workTypesConfig['activity_types'] ?? []) as $value => $label)
                <option value="{{ $value }}" {{ old('activity_type', optional($activityDetail)->activity_type) == $value ? 'selected' : '' }}>
                    {{ __($label) }}
                </option>
                @endforeach
            </select>
            @error('activity_type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Modalidad -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="modality">
                        <strong>{{ __('Modalidad') }}</strong> <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('modality') is-invalid @enderror"
                        id="modality" name="modality" data-required="true">
                        <option value="">{{ __('Seleccione...') }}</option>
                        @foreach(($workTypesConfig['modalities'] ?? []) as $value => $label)
                        <option value="{{ $value }}" {{ old('modality', optional($activityDetail)->modality) == $value ? 'selected' : '' }}>
                            {{ __($label) }}
                        </option>
                        @endforeach
                    </select>
                    @error('modality')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="duration_hours">
                        <strong>{{ __('Duración (horas académicas)') }}</strong>
                    </label>
                    <input type="number" class="form-control @error('duration_hours') is-invalid @enderror"
                        id="duration_hours" name="duration_hours" min="1"
                        value="{{ old('duration_hours', optional($activityDetail)->duration_hours) }}" placeholder="{{ __('Ej: 20') }}">
                    @error('duration_hours')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Participantes -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="expected_participants">
                        <strong>{{ __('Participantes Esperados') }}</strong>
                    </label>
                    <input type="number"
                        class="form-control @error('expected_participants') is-invalid @enderror"
                        id="expected_participants" name="expected_participants" min="1"
                        value="{{ old('expected_participants', optional($activityDetail)->expected_participants) }}"
                        placeholder="{{ __('Número estimado') }}">
                    @error('expected_participants')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="participant_profile">
                        <strong>{{ __('Perfil de Participantes') }}</strong>
                    </label>
                    <input type="text"
                        class="form-control @error('participant_profile') is-invalid @enderror"
                        id="participant_profile" name="participant_profile"
                        value="{{ old('participant_profile', optional($activityDetail)->participant_profile) }}"
                        placeholder="{{ __('Estudiantes, profesionales, etc.') }}">
                    @error('participant_profile')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Certificación -->
        <div class="form-group">
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="offers_certificate"
                    name="offers_certificate" value="1" {{ old('offers_certificate', optional($activityDetail)->offers_certificate) ? 'checked' : '' }}>
                <label class="custom-control-label" for="offers_certificate">
                    <strong>{{ __('La actividad otorga certificado de participación') }}</strong>
                </label>
            </div>
        </div>
    </div>
</div>