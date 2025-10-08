<!-- Sección 5: Específica para Asistencia Técnica -->
<div class="card card-secondary work-section" id="section-asistencia" style="display: none;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tools"></i>
            {{ __('2. Información Específica de Asistencia Técnica') }}
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Tipo de Asistencia -->
        <div class="form-group">
            <label for="asistencia_tipo">
                <strong>{{ __('Tipo de Asistencia Técnica') }}</strong> <span class="text-danger">*</span>
            </label>
            <select class="form-control @error('assistance_type') is-invalid @enderror" id="assistance_type"
                name="assistance_type" data-required="true">
                <option value="">{{ __('Seleccione...') }}</option>
                @foreach($workTypesConfig['assistance_types'] ?? [] as $value => $label)
                    <option value="{{ $value }}" {{ old('assistance_type') == $value ? 'selected' : '' }}>
                        {{ __($label) }}
                    </option>
                @endforeach
            </select>
            @error('assistance_type')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Institución/Empresa Beneficiaria -->
        <div class="form-group">
            <label for="asistencia_beneficiario">
                <strong>{{ __('Institución/Empresa Beneficiaria') }}</strong> <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control @error('collaborating_institution') is-invalid @enderror"
                id="collaborating_institution" name="collaborating_institution" data-required="true"
                value="{{ old('collaborating_institution') }}"
                placeholder="{{ __('Nombre de la organización beneficiaria') }}">
            @error('collaborating_institution')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Área de Especialización -->
        <div class="form-group">
            <label for="asistencia_area_especializacion">
                <strong>{{ __('Área de Especialización') }}</strong>
            </label>
            <input type="text" class="form-control @error('specialization_area') is-invalid @enderror"
                id="specialization_area" name="specialization_area"
                value="{{ old('specialization_area') }}"
                placeholder="{{ __('Ej: Tecnología, Salud, Educación...') }}">
            @error('specialization_area')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Productos Esperados -->
        <div class="form-group">
            <label for="asistencia_productos_esperados">
                <strong>{{ __('Productos/Resultados Esperados') }}</strong>
            </label>
            <textarea class="form-control @error('expected_products') is-invalid @enderror"
                id="expected_products" name="expected_products" rows="3"
                placeholder="{{ __('Describa los productos o resultados esperados de la asistencia técnica...') }}">{{ old('expected_products') }}</textarea>
            @error('expected_products')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Modalidad de Trabajo -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="asistencia_modalidad">
                        <strong>{{ __('Modalidad de Trabajo') }}</strong>
                    </label>
                    <select class="form-control @error('work_modality') is-invalid @enderror"
                        id="work_modality" name="work_modality">
                        @foreach($workTypesConfig['modalities'] ?? [] as $value => $label)
                            <option value="{{ $value }}" {{ (old('work_modality', 'presencial') == $value) ? 'selected' : '' }}>
                                {{ __($label) }}
                            </option>
                        @endforeach
                    </select>
                    @error('work_modality')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="asistencia_horas_estimadas">
                        <strong>{{ __('Horas de Trabajo Estimadas') }}</strong>
                    </label>
                    <input type="number" class="form-control @error('estimated_hours') is-invalid @enderror"
                        id="estimated_hours" name="estimated_hours" min="1"
                        value="{{ old('estimated_hours') }}" placeholder="{{ __('Ej: 40') }}">
                    @error('estimated_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>
