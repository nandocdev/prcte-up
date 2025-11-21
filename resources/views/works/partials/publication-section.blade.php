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
            <label for="publication_type">
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

        <div class="form-group">
            <label for="publication_summary">
                <strong>{{ __('Resumen / Abstract') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('publication_summary') is-invalid @enderror" id="publication_summary"
                name="publication_summary" rows="4" data-required="true"
                placeholder="{{ __('Describa el contenido, objetivos y aportes de la publicación...') }}">{{ old('publication_summary', optional($publicationDetail)->summary) }}</textarea>
            @error('publication_summary')
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
            <label for="target_audience">
                <strong>{{ __('Público Objetivo') }}</strong>
            </label>
            <textarea class="form-control @error('target_audience') is-invalid @enderror"
                id="target_audience" name="target_audience" rows="2"
                placeholder="{{ __('Describa el público al que va dirigida la publicación...') }}">{{ old('target_audience', optional($publicationDetail)->target_audience) }}</textarea>
            @error('target_audience')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="relevance_justification">
                <strong>{{ __('Justificación de Relevancia / Impacto') }}</strong> <span class="text-danger">*</span>
            </label>
            <textarea class="form-control @error('relevance_justification') is-invalid @enderror" id="relevance_justification"
                name="relevance_justification" rows="4" data-required="true"
                placeholder="{{ __('Explique la contribución académica o social de la publicación...') }}">{{ old('relevance_justification', optional($publicationDetail)->relevance_justification) }}</textarea>
            @error('relevance_justification')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="publication_date">
                        <strong>{{ __('Fecha de Publicación') }}</strong> <span class="text-danger">*</span>
                    </label>
                    <input type="date" class="form-control @error('publication_date') is-invalid @enderror"
                        id="publication_date" name="publication_date" data-required="true"
                        value="{{ old('publication_date', optional(optional($publicationDetail)->publication_date)->format('Y-m-d')) }}">
                    @error('publication_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="media_type">
                        <strong>{{ __('Tipo de Medio / Plataforma') }}</strong> <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('media_type') is-invalid @enderror"
                        id="media_type" name="media_type" data-required="true"
                        value="{{ old('media_type', optional($publicationDetail)->media_type) }}"
                        placeholder="{{ __('Ej: Revista indexada, editorial universitaria, plataforma digital...') }}">
                    @error('media_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="media_nature">
                        <strong>{{ __('Naturaleza del Medio') }}</strong>
                    </label>
                    <input type="text" class="form-control @error('media_nature') is-invalid @enderror"
                        id="media_nature" name="media_nature"
                        value="{{ old('media_nature', optional($publicationDetail)->media_nature) }}"
                        placeholder="{{ __('Ej: Impreso, digital, audiovisual...') }}">
                    @error('media_nature')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="language">
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
        </div>

        <div class="form-group">
            <label for="print_run">
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