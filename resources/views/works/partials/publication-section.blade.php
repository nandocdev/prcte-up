<!-- Sección 4: Específica para Publicaciones -->
@php
$publicationDetail = $publicationDetail ?? null;
$workTypesConfig = $workTypesConfig ?? [];
@endphp

<div class="card card-info work-section" id="section-publicacion" style="display: none;">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-book"></i>
            {{ __('2. Información Específica de la Publicación') }}
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Tipo de Publicación -->
        <div class="form-group">
            <label for="publicacion_tipo">
                <strong>{{ __('Tipo de Publicación') }}</strong> <span class="text-danger">*</span>
            </label>
            <select class="form-control @error('publication_type') is-invalid @enderror" id="publication_type"
                name="publication_type" data-required="true">
                <option value="">{{ __('Seleccione...') }}</option>
                @foreach(($workTypesConfig['publication_types'] ?? []) as $value => $label)
                <option value="{{ $value }}" {{ old('publication_type', optional($publicationDetail)->publication_type) == $value ? 'selected' : '' }}>
                    {{ __($label) }}
                </option>
                @endforeach
            </select>
            @error('publication_type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Editorial/Revista -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="publicacion_editorial">
                        <strong>{{ __('Editorial/Revista') }}</strong>
                    </label>
                    <input type="text" class="form-control @error('editorial') is-invalid @enderror"
                        id="editorial" name="editorial"
                        value="{{ old('editorial', optional($publicationDetail)->editorial) }}"
                        placeholder="{{ __('Nombre de la editorial o revista') }}">
                    @error('editorial')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="publicacion_isbn_issn">
                        <strong>{{ __('ISBN/ISSN') }}</strong>
                    </label>
                    <input type="text" class="form-control @error('isbn_issn') is-invalid @enderror"
                        id="isbn_issn" name="isbn_issn"
                        value="{{ old('isbn_issn', optional($publicationDetail)->isbn_issn) }}" placeholder="{{ __('Número ISBN o ISSN') }}">
                    @error('isbn_issn')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Público Objetivo -->
        <div class="form-group">
            <label for="publicacion_publico_objetivo">
                <strong>{{ __('Público Objetivo') }}</strong>
            </label>
            <textarea class="form-control @error('target_audience') is-invalid @enderror"
                id="target_audience" name="target_audience" rows="2"
                placeholder="{{ __('Describa el público al que va dirigida la publicación...') }}">{{ old('target_audience', optional($publicationDetail)->target_audience) }}</textarea>
            @error('target_audience')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Idioma y Tiraje -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="publicacion_idioma">
                        <strong>{{ __('Idioma') }}</strong>
                    </label>
                    <select class="form-control @error('language') is-invalid @enderror"
                        id="language" name="language">
                        @foreach(($workTypesConfig['languages'] ?? []) as $value => $label)
                        <option value="{{ $value }}" {{ (old('language', optional($publicationDetail)->language ?? 'español') == $value) ? 'selected' : '' }}>
                            {{ __($label) }}
                        </option>
                        @endforeach
                    </select>
                    @error('language')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="publicacion_tiraje">
                        <strong>{{ __('Tiraje Estimado') }}</strong>
                    </label>
                    <input type="number" class="form-control @error('print_run') is-invalid @enderror"
                        id="print_run" name="print_run" min="1"
                        value="{{ old('print_run', optional($publicationDetail)->print_run) }}" placeholder="{{ __('Número de ejemplares') }}">
                    @error('print_run')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>