{{-- Detalles Específicos de Asistencia Técnica --}}
<div class="card card-outline card-danger">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-tools mr-2"></i>
            Detalles de Asistencia Técnica
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
                    <i class="fas fa-handshake text-primary"></i>
                    Tipo de Asistencia
                </strong>
                <p class="text-muted">
                    {{ $work->technicalAssistanceDetails->getAttribute('assistance_type') ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>
                    <i class="fas fa-building text-success"></i>
                    Organización Beneficiaria
                </strong>
                <p class="text-muted">
                    {{ $work->technicalAssistanceDetails->getAttribute('beneficiary_organization') ?? 'N/A' }}
                </p>
            </div>
        </div>

        @if($work->technicalAssistanceDetails->getAttribute('scope'))
            <div class="mb-3">
                <strong>
                    <i class="fas fa-bullseye text-info"></i>
                    Alcance de la Asistencia
                </strong>
                <div class="alert alert-light mt-2">
                    <p style="white-space: pre-wrap;">{{ $work->technicalAssistanceDetails->getAttribute('scope') }}</p>
                </div>
            </div>
        @endif

        @if($work->technicalAssistanceDetails->getAttribute('deliverables'))
            <div class="mb-3">
                <strong>
                    <i class="fas fa-tasks text-warning"></i>
                    Entregables
                </strong>
                <div class="alert alert-warning mt-2">
                    <p style="white-space: pre-wrap;">{{ $work->technicalAssistanceDetails->getAttribute('deliverables') }}</p>
                </div>
            </div>
        @endif

        @if($work->technicalAssistanceDetails->getAttribute('contact_person'))
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>
                        <i class="fas fa-user text-primary"></i>
                        Persona de Contacto
                    </strong>
                    <p class="text-muted">
                        {{ $work->technicalAssistanceDetails->getAttribute('contact_person') }}
                    </p>
                </div>
                @if($work->technicalAssistanceDetails->getAttribute('contact_email'))
                    <div class="col-md-6 mb-3">
                        <strong>
                            <i class="fas fa-envelope text-info"></i>
                            Email de Contacto
                        </strong>
                        <p class="text-muted">
                            <a href="mailto:{{ $work->technicalAssistanceDetails->getAttribute('contact_email') }}">
                                {{ $work->technicalAssistanceDetails->getAttribute('contact_email') }}
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
