{{-- Tarjetas de Información General --}}
<div class="row">
    <div class="col-md-6">
        <div class="info-box bg-light">
            <span class="info-box-icon bg-info elevation-1">
                <i class="fas fa-user-tie"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Profesor Responsable</span>
                <span class="info-box-number">
                    {{ $work->responsibleUser->name ?? 'N/A' }}
                </span>
                @if($work->responsibleUser)
                    <small class="text-muted">
                        <i class="fas fa-envelope"></i>
                        {{ $work->responsibleUser->email }}
                    </small>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="info-box bg-light">
            <span class="info-box-icon bg-success elevation-1">
                <i class="fas fa-building"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Unidad Organizacional</span>
                <span class="info-box-number">
                    {{ $work->organizationalUnit->name ?? 'N/A' }}
                </span>
                @if($work->organizationalUnit && $work->organizationalUnit->parent)
                    <small class="text-muted">
                        <i class="fas fa-sitemap"></i>
                        {{ $work->organizationalUnit->parent->name }}
                    </small>
                @endif
            </div>
        </div>
    </div>
</div>
