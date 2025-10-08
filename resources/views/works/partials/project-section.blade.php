<!-- Sección 2: Específica para Proyectos de Extensión -->
<div class="card card-success work-section" id="section-proyecto" style="display: none;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-project-diagram"></i>
            {{ __('2. Información Específica del Proyecto') }}
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Objetivos del Proyecto -->
        <div class="form-group">
            <label for="objectives">
                <strong>{{ __('Objetivos del Proyecto') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('objectives') is-invalid @enderror" id="objectives"
                name="objectives" rows="3" data-required="true"
                placeholder="{{ __('Describa los objetivos general y específicos del proyecto...') }}">{{ old('objectives') }}</textarea>
            @error('objectives')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Metodología -->
        <div class="form-group">
            <label for="methodology">
                <strong>{{ __('Metodología') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('methodology') is-invalid @enderror" id="methodology"
                name="methodology" rows="3" data-required="true"
                placeholder="{{ __('Describa la metodología a utilizar en el proyecto...') }}">{{ old('methodology') }}</textarea>
            @error('methodology')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Beneficiarios -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="direct_beneficiaries">
                        <strong>{{ __('Beneficiarios Directos') }}</strong>
                    </label>
                    <input type="number"
                        class="form-control @error('direct_beneficiaries') is-invalid @enderror"
                        id="direct_beneficiaries" name="direct_beneficiaries" min="0"
                        value="{{ old('direct_beneficiaries') }}" placeholder="{{ __('Número estimado') }}">
                    @error('direct_beneficiaries')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="indirect_beneficiaries">
                        <strong>{{ __('Beneficiarios Indirectos') }}</strong>
                    </label>
                    <input type="number"
                        class="form-control @error('indirect_beneficiaries') is-invalid @enderror"
                        id="indirect_beneficiaries" name="indirect_beneficiaries" min="0"
                        value="{{ old('indirect_beneficiaries') }}"
                        placeholder="{{ __('Número estimado') }}">
                    @error('indirect_beneficiaries')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Área Geográfica -->
        <div class="form-group">
            <label for="geographic_area">
                <strong>{{ __('Área Geográfica de Intervención') }}</strong>
            </label>
            <input type="text" class="form-control @error('geographic_area') is-invalid @enderror"
                id="geographic_area" name="geographic_area"
                value="{{ old('geographic_area') }}"
                placeholder="{{ __('Municipio, departamento, región...') }}">
            @error('geographic_area')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
