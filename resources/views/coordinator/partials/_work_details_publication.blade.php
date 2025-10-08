{{-- Detalles Específicos de la Publicación --}}
<div class="card card-outline card-warning">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-book-open mr-2"></i>
            Detalles de la Publicación
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <strong>
                    <i class="fas fa-newspaper text-primary"></i>
                    Tipo de Publicación
                </strong>
                <p class="text-muted">
                    {{ $work->publicationDetails->getAttribute('publication_type') ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>
                    <i class="fas fa-calendar text-success"></i>
                    Fecha de Publicación
                </strong>
                <p class="text-muted">
                    {{ $work->publicationDetails->getAttribute('publication_date')?->format('d/m/Y') ?? 'N/A' }}
                </p>
            </div>
        </div>

        @if($work->publicationDetails->getAttribute('isbn_issn'))
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>
                        <i class="fas fa-barcode text-info"></i>
                        ISBN/ISSN
                    </strong>
                    <p class="text-muted">
                        {{ $work->publicationDetails->getAttribute('isbn_issn') }}
                    </p>
                </div>
                @if($work->publicationDetails->getAttribute('publisher'))
                    <div class="col-md-6 mb-3">
                        <strong>
                            <i class="fas fa-building text-warning"></i>
                            Editorial
                        </strong>
                        <p class="text-muted">
                            {{ $work->publicationDetails->getAttribute('publisher') }}
                        </p>
                    </div>
                @endif
            </div>
        @endif

        @if($work->publicationDetails->getAttribute('abstract'))
            <div class="mb-3">
                <strong>
                    <i class="fas fa-file-alt text-primary"></i>
                    Resumen / Abstract
                </strong>
                <div class="alert alert-light mt-2">
                    <p style="white-space: pre-wrap;">{{ $work->publicationDetails->getAttribute('abstract') }}</p>
                </div>
            </div>
        @endif

        @if($work->publicationDetails->getAttribute('doi'))
            <div class="mb-3">
                <strong>
                    <i class="fas fa-link text-success"></i>
                    DOI (Digital Object Identifier)
                </strong>
                <p class="text-muted">
                    <a href="https://doi.org/{{ $work->publicationDetails->getAttribute('doi') }}" target="_blank">
                        {{ $work->publicationDetails->getAttribute('doi') }}
                        <i class="fas fa-external-link-alt ml-1"></i>
                    </a>
                </p>
            </div>
        @endif
    </div>
</div>
