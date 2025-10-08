{{-- Lista de Archivos y Evidencias --}}
@if($work->getMedia('evidencias')->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-paperclip mr-2"></i>
                Evidencias y Documentos
                <span class="badge badge-success ml-2">{{ $work->getMedia('evidencias')->count() }}</span>
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($work->getMedia('evidencias') as $media)
                    <div class="col-md-6 mb-3">
                        <div class="card card-outline card-info">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        @php
                                            $extension = pathinfo($media->name, PATHINFO_EXTENSION);
                                            $iconClass = match (strtolower($extension)) {
                                                'pdf' => 'fas fa-file-pdf text-danger',
                                                'doc', 'docx' => 'fas fa-file-word text-primary',
                                                'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                                'jpg', 'jpeg', 'png', 'gif', 'svg' => 'fas fa-file-image text-warning',
                                                'zip', 'rar' => 'fas fa-file-archive text-secondary',
                                                default => 'fas fa-file text-secondary'
                                            };
                                        @endphp
                                        <i class="{{ $iconClass }} fa-3x"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 font-weight-bold">
                                            {{ Str::limit($media->name, 35) }}
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-weight"></i>
                                            {{ number_format($media->size / 1024, 1) }} KB
                                            <br>
                                            <i class="fas fa-clock"></i>
                                            {{ $media->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    <div>
                                        <a href="{{ $media->getUrl() }}" 
                                           target="_blank"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Descargar {{ $media->name }}">
                                            <i class="fas fa-download"></i>
                                            Descargar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Resumen de Archivos --}}
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i>
                <strong>Total de archivos:</strong> {{ $work->getMedia('evidencias')->count() }}
                <br>
                <strong>Tamaño total:</strong>
                {{ number_format($work->getMedia('evidencias')->sum('size') / 1024 / 1024, 2) }} MB
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="text-center text-muted py-4">
                <i class="fas fa-paperclip fa-3x mb-3"></i>
                <p>No se han adjuntado evidencias o documentos a este trabajo.</p>
            </div>
        </div>
    </div>
@endif
