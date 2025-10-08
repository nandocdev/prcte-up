{{-- Timeline del Historial de Estados --}}
@if($work->statusHistory && $work->statusHistory->count() > 0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>
                Historial del Trabajo
                <span class="badge badge-secondary ml-2">{{ $work->statusHistory->count() }} eventos</span>
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="timeline">
                @foreach($work->statusHistory->sortByDesc('created_at') as $history)
                    {{-- Time Label --}}
                    <div class="time-label">
                        <span class="bg-{{ $loop->first ? 'primary' : 'gray' }}">
                            <i class="fas fa-calendar-day"></i>
                            {{ $history->created_at->format('d/m/Y') }}
                        </span>
                    </div>

                    {{-- Timeline Item --}}
                    <div>
                        @php
                            $iconClass = match ($history->status->name ?? '') {
                                'Borrador' => 'fas fa-edit bg-secondary',
                                'Enviado a Coordinador', 'En Revisión Coordinador' => 'fas fa-clock bg-warning',
                                'Enviado a Decano/Director' => 'fas fa-arrow-up bg-success',
                                'Enviado a VIEX' => 'fas fa-check-circle bg-primary',
                                'Aprobado', 'Certificado' => 'fas fa-check-double bg-success',
                                'Devuelto para Corrección' => 'fas fa-redo bg-info',
                                'Rechazado por Coordinador', 'Rechazado por Decano', 'Rechazado por VIEX' => 'fas fa-times-circle bg-danger',
                                default => 'fas fa-circle bg-gray'
                            };
                        @endphp
                        <i class="{{ $iconClass }}"></i>

                        <div class="timeline-item">
                            <span class="time">
                                <i class="fas fa-clock"></i>
                                {{ $history->created_at->format('H:i') }}
                            </span>

                            <h3 class="timeline-header">
                                <strong>{{ $history->status->name ?? 'Estado desconocido' }}</strong>
                            </h3>

                            @if($history->comments)
                                <div class="timeline-body">
                                    <div class="callout callout-info">
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $history->comments }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="timeline-footer">
                                @if($history->changedBy)
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i>
                                        <strong>Por:</strong> {{ $history->changedBy->name }}
                                    </small>
                                @endif
                                <small class="text-muted ml-3">
                                    <i class="fas fa-calendar"></i>
                                    {{ $history->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- End of Timeline --}}
                <div>
                    <i class="fas fa-flag-checkered bg-gray"></i>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="text-center text-muted py-4">
                <i class="fas fa-history fa-3x mb-3"></i>
                <p>No hay historial de estados disponible para este trabajo.</p>
            </div>
        </div>
    </div>
@endif
