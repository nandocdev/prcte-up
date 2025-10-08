{{-- Secciones Dinámicas para Edición - Pre-carga datos existentes --}}

<!-- Sección de Proyecto -->
<div id="section-1" class="work-type-section @if($work->work_type_id == 1) active @endif">
    <div class="card card-info">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-project-diagram"></i>
                {{ __('Detalles del Proyecto') }}
            </h3>
        </div>
        <div class="card-body">
            @php
                $projectDetail = $work->projectDetail;
            @endphp

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="objectives" class="required">
                            <i class="fas fa-bullseye"></i>
                            {{ __('Objetivos') }}
                        </label>
                        <textarea name="objectives" id="objectives"
                            class="form-control @error('objectives') is-invalid @enderror" rows="3"
                            placeholder="{{ __('Describe los objetivos generales y específicos del proyecto...') }}">{{ old('objectives', $projectDetail?->objectives ?? '') }}</textarea>
                        @error('objectives')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="methodology">
                            <i class="fas fa-cogs"></i>
                            {{ __('Metodología') }}
                        </label>
                        <textarea name="methodology" id="methodology"
                            class="form-control @error('methodology') is-invalid @enderror" rows="3"
                            placeholder="{{ __('Describe la metodología a utilizar...') }}">{{ old('methodology', $projectDetail?->methodology ?? '') }}</textarea>
                        @error('methodology')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="direct_beneficiaries">
                            <i class="fas fa-users"></i>
                            {{ __('Beneficiarios Directos') }}
                        </label>
                        <input type="number" name="direct_beneficiaries" id="direct_beneficiaries"
                            class="form-control @error('direct_beneficiaries') is-invalid @enderror"
                            value="{{ old('direct_beneficiaries', $projectDetail?->direct_beneficiaries ?? '') }}"
                            min="1" placeholder="{{ __('Número estimado...') }}">
                        @error('direct_beneficiaries')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="indirect_beneficiaries">
                            <i class="fas fa-user-plus"></i>
                            {{ __('Beneficiarios Indirectos') }}
                        </label>
                        <input type="number" name="indirect_beneficiaries" id="indirect_beneficiaries"
                            class="form-control @error('indirect_beneficiaries') is-invalid @enderror"
                            value="{{ old('indirect_beneficiaries', $projectDetail?->indirect_beneficiaries ?? '') }}"
                            min="0" placeholder="{{ __('Número estimado...') }}">
                        @error('indirect_beneficiaries')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="geographic_area">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ __('Área Geográfica') }}
                        </label>
                        <input type="text" name="geographic_area" id="geographic_area"
                            class="form-control @error('geographic_area') is-invalid @enderror"
                            value="{{ old('geographic_area', $projectDetail?->geographic_area ?? '') }}"
                            placeholder="{{ __('Ej: Ciudad de Panamá, Provincia de Panamá') }}">
                        @error('geographic_area')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Actividad -->
<div id="section-2" class="work-type-section @if($work->work_type_id == 2) active @endif">
    <div class="card card-success">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-check"></i>
                {{ __('Detalles de la Actividad') }}
            </h3>
        </div>
        <div class="card-body">
            @php
                $activityDetail = $work->activityDetail;
            @endphp

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="activity_type" class="required">
                            <i class="fas fa-tags"></i>
                            {{ __('Tipo de Actividad') }}
                        </label>
                        <select name="activity_type" id="activity_type"
                            class="form-control select2 @error('activity_type') is-invalid @enderror">
                            <option value="">{{ __('Selecciona el tipo...') }}</option>
                            @foreach($config['activity_types'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('activity_type', $activityDetail?->activity_type ?? '') == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('activity_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="modality" class="required">
                            <i class="fas fa-laptop"></i>
                            {{ __('Modalidad') }}
                        </label>
                        <select name="modality" id="modality"
                            class="form-control @error('modality') is-invalid @enderror">
                            @foreach($config['modalities'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('modality', $activityDetail?->modality ?? '') == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('modality')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="duration_hours">
                            <i class="fas fa-clock"></i>
                            {{ __('Duración (horas)') }}
                        </label>
                        <input type="number" name="duration_hours" id="duration_hours"
                            class="form-control @error('duration_hours') is-invalid @enderror"
                            value="{{ old('duration_hours', $activityDetail?->duration_hours ?? '') }}" min="1"
                            step="0.5">
                        @error('duration_hours')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="expected_participants">
                            <i class="fas fa-users"></i>
                            {{ __('Participantes Esperados') }}
                        </label>
                        <input type="number" name="expected_participants" id="expected_participants"
                            class="form-control @error('expected_participants') is-invalid @enderror"
                            value="{{ old('expected_participants', $activityDetail?->expected_participants ?? '') }}"
                            min="1">
                        @error('expected_participants')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="offers_certificate">
                            <i class="fas fa-certificate"></i>
                            {{ __('¿Ofrece Certificado?') }}
                        </label>
                        <select name="offers_certificate" id="offers_certificate"
                            class="form-control @error('offers_certificate') is-invalid @enderror">
                            <option value="0" @selected(old('offers_certificate', $activityDetail?->offers_certificate ?? '0') == '0')>
                                {{ __('No') }}
                            </option>
                            <option value="1" @selected(old('offers_certificate', $activityDetail?->offers_certificate ?? '0') == '1')>
                                {{ __('Sí') }}
                            </option>
                        </select>
                        @error('offers_certificate')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="participant_profile">
                            <i class="fas fa-user-graduate"></i>
                            {{ __('Perfil de Participantes') }}
                        </label>
                        <textarea name="participant_profile" id="participant_profile"
                            class="form-control @error('participant_profile') is-invalid @enderror" rows="2"
                            placeholder="{{ __('Describe el perfil de los participantes objetivo...') }}">{{ old('participant_profile', $activityDetail?->participant_profile ?? '') }}</textarea>
                        @error('participant_profile')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Publicación -->
<div id="section-3" class="work-type-section @if($work->work_type_id == 3) active @endif">
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-book"></i>
                {{ __('Detalles de la Publicación') }}
            </h3>
        </div>
        <div class="card-body">
            @php
                $publicationDetail = $work->publicationDetail;
            @endphp

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="publication_type" class="required">
                            <i class="fas fa-file-alt"></i>
                            {{ __('Tipo de Publicación') }}
                        </label>
                        <select name="publication_type" id="publication_type"
                            class="form-control select2 @error('publication_type') is-invalid @enderror">
                            <option value="">{{ __('Selecciona el tipo...') }}</option>
                            @foreach($config['publication_types'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('publication_type', $publicationDetail?->publication_type ?? '') == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('publication_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="language">
                            <i class="fas fa-language"></i>
                            {{ __('Idioma') }}
                        </label>
                        <select name="language" id="language"
                            class="form-control @error('language') is-invalid @enderror">
                            @foreach($config['languages'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('language', $publicationDetail?->language ?? 'español') == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('language')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="editorial">
                            <i class="fas fa-building"></i>
                            {{ __('Editorial/Revista') }}
                        </label>
                        <input type="text" name="editorial" id="editorial"
                            class="form-control @error('editorial') is-invalid @enderror"
                            value="{{ old('editorial', $publicationDetail?->editorial ?? '') }}"
                            placeholder="{{ __('Nombre de la editorial o revista...') }}">
                        @error('editorial')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="isbn_issn">
                            <i class="fas fa-barcode"></i>
                            {{ __('ISBN/ISSN') }}
                        </label>
                        <input type="text" name="isbn_issn" id="isbn_issn"
                            class="form-control @error('isbn_issn') is-invalid @enderror"
                            value="{{ old('isbn_issn', $publicationDetail?->isbn_issn ?? '') }}"
                            placeholder="{{ __('Ej: 978-3-16-148410-0') }}">
                        @error('isbn_issn')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="target_audience">
                            <i class="fas fa-users"></i>
                            {{ __('Audiencia Objetivo') }}
                        </label>
                        <input type="text" name="target_audience" id="target_audience"
                            class="form-control @error('target_audience') is-invalid @enderror"
                            value="{{ old('target_audience', $publicationDetail?->target_audience ?? '') }}"
                            placeholder="{{ __('Ej: Estudiantes, Profesionales, Público general...') }}">
                        @error('target_audience')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="print_run">
                            <i class="fas fa-print"></i>
                            {{ __('Tiraje') }}
                        </label>
                        <input type="number" name="print_run" id="print_run"
                            class="form-control @error('print_run') is-invalid @enderror"
                            value="{{ old('print_run', $publicationDetail?->print_run ?? '') }}" min="1"
                            placeholder="{{ __('Número de ejemplares...') }}">
                        @error('print_run')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Asistencia Técnica -->
<div id="section-4" class="work-type-section @if($work->work_type_id == 4) active @endif">
    <div class="card card-danger">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-hands-helping"></i>
                {{ __('Detalles de la Asistencia Técnica') }}
            </h3>
        </div>
        <div class="card-body">
            @php
                $technicalDetail = $work->technicalAssistanceDetail;
            @endphp

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="assistance_type" class="required">
                            <i class="fas fa-tools"></i>
                            {{ __('Tipo de Asistencia') }}
                        </label>
                        <select name="assistance_type" id="assistance_type"
                            class="form-control select2 @error('assistance_type') is-invalid @enderror">
                            <option value="">{{ __('Selecciona el tipo...') }}</option>
                            @foreach($config['assistance_types'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('assistance_type', $technicalDetail?->assistance_type ?? '') == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('assistance_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="work_modality" class="required">
                            <i class="fas fa-laptop-house"></i>
                            {{ __('Modalidad de Trabajo') }}
                        </label>
                        <select name="work_modality" id="work_modality"
                            class="form-control @error('work_modality') is-invalid @enderror">
                            @foreach($config['work_modalities'] as $key => $label)
                                <option value="{{ $key }}" @selected(old('work_modality', $technicalDetail?->work_modality ?? 'presencial') == $key)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('work_modality')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="collaborating_institution" class="required">
                            <i class="fas fa-handshake"></i>
                            {{ __('Institución Colaboradora') }}
                        </label>
                        <input type="text" name="collaborating_institution" id="collaborating_institution"
                            class="form-control @error('collaborating_institution') is-invalid @enderror"
                            value="{{ old('collaborating_institution', $technicalDetail?->collaborating_institution ?? '') }}"
                            placeholder="{{ __('Nombre de la institución que recibe la asistencia...') }}">
                        @error('collaborating_institution')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="estimated_hours">
                            <i class="fas fa-clock"></i>
                            {{ __('Horas Estimadas') }}
                        </label>
                        <input type="number" name="estimated_hours" id="estimated_hours"
                            class="form-control @error('estimated_hours') is-invalid @enderror"
                            value="{{ old('estimated_hours', $technicalDetail?->estimated_hours ?? '') }}" min="1"
                            step="0.5">
                        @error('estimated_hours')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="specialization_area">
                            <i class="fas fa-microscope"></i>
                            {{ __('Área de Especialización') }}
                        </label>
                        <input type="text" name="specialization_area" id="specialization_area"
                            class="form-control @error('specialization_area') is-invalid @enderror"
                            value="{{ old('specialization_area', $technicalDetail?->specialization_area ?? '') }}"
                            placeholder="{{ __('Ej: Tecnología educativa, Gestión ambiental, etc.') }}">
                        @error('specialization_area')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="expected_products">
                            <i class="fas fa-clipboard-list"></i>
                            {{ __('Productos Esperados') }}
                        </label>
                        <textarea name="expected_products" id="expected_products"
                            class="form-control @error('expected_products') is-invalid @enderror" rows="3"
                            placeholder="{{ __('Describe los entregables o productos esperados de la asistencia técnica...') }}">{{ old('expected_products', $technicalDetail?->expected_products ?? '') }}</textarea>
                        @error('expected_products')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
