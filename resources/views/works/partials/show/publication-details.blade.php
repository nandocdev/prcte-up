@if (empty($publication))
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        {{ __('No se registraron detalles específicos para esta publicación.') }}
    </div>
@else
<div class="card card-outline card-warning">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">
            <i class="fas fa-book"></i>
            {{ __('Detalles específicos de la publicación') }}
        </h3>
        <span class="badge badge-warning text-uppercase">
            {{ $publication->publication_type ? __(ucfirst($publication->publication_type)) : __('Tipo no definido') }}
        </span>
    </div>
    <div class="card-body">
        <div class="row text-center mb-4">
            <div class="col-md-3">
                <h6 class="text-muted text-uppercase">{{ __('Fecha de publicación') }}</h6>
                <p class="mb-0">{{ optional($publication->publication_date)->format('d/m/Y') ?? __('Sin fecha') }}</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted text-uppercase">{{ __('Tiraje') }}</h6>
                <p class="mb-0 h5">{{ $publication->print_run ?? '—' }}</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted text-uppercase">{{ __('Idioma') }}</h6>
                <p class="mb-0">{{ $publication->language ?? __('No especificado') }}</p>
            </div>
            <div class="col-md-3">
                <h6 class="text-muted text-uppercase">{{ __('Medio') }}</h6>
                <p class="mb-0">{{ $publication->media_type ?? __('No definido') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h5 class="text-primary">{{ __('Editorial / ISBN-ISSN') }}</h5>
                <p class="mb-2">{{ $publication->editorial ?? __('Sin editorial') }}</p>
                <p class="mb-0">{{ $publication->isbn_issn ?? __('Sin código registrado') }}</p>
            </div>
            <div class="col-md-6">
                <h5 class="text-primary">{{ __('Audiencia objetivo') }}</h5>
                <p class="mb-0">{{ $publication->target_audience ?? __('No especificada') }}</p>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Naturaleza del medio') }}</h6>
                <p class="mb-0">{{ $publication->media_nature ?? __('No definida') }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted text-uppercase">{{ __('Justificación de relevancia') }}</h6>
                <p class="mb-0">{{ $publication->relevance_justification ?? __('Sin justificación registrada') }}</p>
            </div>
        </div>
    </div>
</div>
@endif
