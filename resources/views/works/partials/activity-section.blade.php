<!-- Sección 3: Específica para Actividades de Extensión -->
@php
$activityDetail = $activityDetail ?? null;
$workTypesConfig = $workTypesConfig ?? [];
$activityDetailsData = is_array(optional($activityDetail)->details_json)
? $activityDetail->details_json
: [];
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

        <!-- Modalidad y duración -->
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

        <div class="form-group">
            <label for="activity_introduction">
                <strong>{{ __('Introducción de la Actividad') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('activity_introduction') is-invalid @enderror" id="activity_introduction"
                name="activity_introduction" rows="3" data-required="true"
                placeholder="{{ __('Contextualice la actividad y su alineación institucional...') }}">{{ old('activity_introduction', data_get($activityDetailsData, 'introduction')) }}</textarea>
            @error('activity_introduction')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="activity_justification">
                <strong>{{ __('Justificación de la Actividad') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('activity_justification') is-invalid @enderror" id="activity_justification"
                name="activity_justification" rows="3" data-required="true"
                placeholder="{{ __('Explique la necesidad y pertinencia de la actividad...') }}">{{ old('activity_justification', data_get($activityDetailsData, 'justification')) }}</textarea>
            @error('activity_justification')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="activity_objectives">
                <strong>{{ __('Objetivos Específicos de la Actividad') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('activity_objectives') is-invalid @enderror" id="activity_objectives"
                name="activity_objectives" rows="3" data-required="true"
                placeholder="{{ __('Detalle objetivos generales y específicos...') }}">{{ old('activity_objectives', data_get($activityDetailsData, 'objectives')) }}</textarea>
            @error('activity_objectives')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="activity_methodology">
                <strong>{{ __('Metodología / Estrategias Didácticas') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('activity_methodology') is-invalid @enderror" id="activity_methodology"
                name="activity_methodology" rows="3" data-required="true"
                placeholder="{{ __('Describa las estrategias metodológicas que se emplearán...') }}">{{ old('activity_methodology', data_get($activityDetailsData, 'methodology')) }}</textarea>
            @error('activity_methodology')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="activity_resources">
                <strong>{{ __('Recursos y Materiales') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('activity_resources') is-invalid @enderror" id="activity_resources"
                name="activity_resources" rows="3" data-required="true"
                placeholder="{{ __('Incluya materiales, plataformas o medios requeridos...') }}">{{ old('activity_resources', data_get($activityDetailsData, 'resources')) }}</textarea>
            @error('activity_resources')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="activity_beneficiaries">
                <strong>{{ __('Perfil y Beneficios para Participantes') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('activity_beneficiaries') is-invalid @enderror" id="activity_beneficiaries"
                name="activity_beneficiaries" rows="3" data-required="true"
                placeholder="{{ __('Describa los beneficios concretos y perfiles participantes...') }}">{{ old('activity_beneficiaries', data_get($activityDetailsData, 'beneficiaries')) }}</textarea>
            @error('activity_beneficiaries')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="activity_institution_relationships">
                        <strong>{{ __('Articulación con Instituciones Aliadas') }}</strong>
                    </label>
                    <textarea class="form-control @error('activity_institution_relationships') is-invalid @enderror"
                        id="activity_institution_relationships" name="activity_institution_relationships" rows="3"
                        placeholder="{{ __('Describa colaboraciones o apoyos interinstitucionales...') }}">{{ old('activity_institution_relationships', data_get($activityDetailsData, 'institution_relationships')) }}</textarea>
                    @error('activity_institution_relationships')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="activity_comments">
                        <strong>{{ __('Comentarios u Observaciones') }}</strong>
                    </label>
                    <textarea class="form-control @error('activity_comments') is-invalid @enderror" id="activity_comments"
                        name="activity_comments" rows="3"
                        placeholder="{{ __('Agregue notas adicionales relevantes para la actividad...') }}">{{ old('activity_comments', data_get($activityDetailsData, 'comments')) }}</textarea>
                    @error('activity_comments')
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